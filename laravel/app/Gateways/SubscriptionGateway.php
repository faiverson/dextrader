<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Models\Subscription;
use App\Services\SubscriptionCreateValidator;
use App\Services\SubscriptionUpdateValidator;
use App\Repositories\SubscriptionRepository;
use DateTime;
use DB;
use Event;
use App\Events\SubscriptionFailEvent;
use App\Events\SubscriptionCancelEvent;

class SubscriptionGateway extends AbstractGateway
{

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $userGateway;

	protected $errors;

	public function __construct(SubscriptionRepository $repository, SubscriptionCreateValidator $createValidator, SubscriptionUpdateValidator $updateValidator, UserGateway $userGateway)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
		$this->userGateway = $userGateway;
	}

	public function findByUser($user_id, $columns = array('*'), $limit = null, $offset = null)
	{
		return $this->repository->findByUser($user_id, $columns, $limit, $offset);
	}

	public function findProductByUser($product_id, $user_id)
	{
		return $this->repository->findProductByUser($product_id, $user_id);
	}

	public function isOwner($user_id, $subscription_id)
	{
		return $this->repository->isOwner($user_id, $subscription_id);
	}

	public function getBillings($from = 'today')
	{
		$day = new DateTime($from);
		return $this->repository->getSubForBilling($day);
	}

	public function setDataForTransaction(Subscription $subscription)
	{
		return [
			'user_id' => $subscription->user->user_id,
			'info' => ['type' => 'billing'],
			'enroller_id' => $subscription->enroller_id,
			'amount' => $subscription->amount,
			'first_name' => $subscription->user->first_name,
			'last_name' => $subscription->user->last_name,
			'email' => $subscription->user->email,
			'description' => $subscription->product->display_name,
			'products' => [
				[
					'product_id' => $subscription->product->product_id,
					'product_name' => $subscription->product->name,
					'product_display_name' => $subscription->product->display_name,
					'product_amount' => $subscription->product->amount,
					'product_discount' => $subscription->product->discount
				]
			],
			'number' => $subscription->card->number,
			'card_id' => $subscription->card->id,
			'card_name' => $subscription->card->name,
			'card_network' => $subscription->card->network,
			'card_last_four' => $subscription->card->last_four,
			'card_first_six' => $subscription->card->first_six,
			'card_exp_month' => $subscription->card->exp_month,
			'card_exp_year' => $subscription->card->exp_year,
			'billing_address_id' => $subscription->address->id,
			'billing_address' => $subscription->address->address,
			'billing_address2' => $subscription->address->address2,
			'billing_state' => $subscription->address->state,
			'billing_city' => $subscription->address->city,
			'billing_country' => $subscription->address->country,
			'billing_zip' => $subscription->address->zip,
			'billing_phone' => $subscription->address->phone
		];
	}

	/**
	 * @param Subscription $subscription with products, etc
	 * it should have JOIN like the function getSubForBilling
	 * in the sub's repo
	 */
	public function renewed(Subscription $subscription)
	{
		$now = new DateTime('now');

		$next = new DateTime('now');
		$next->modify($subscription->product->billing_period);
		$data = [
			'attempts_billing' => 0,
			'last_billing' => $now->format('Y-m-d'),
			'next_billing' => $next->format('Y-m-d'),
			'status' => 'active'
		];

		$response = $this->repository->update($data, $subscription->id);
		return $response;
	}

	public function failed(Subscription $subscription)
	{
		$data = [
			'attempts_billing' => ($subscription->attempts_billing + 1)
		];

		if($data['attempts_billing'] >= 3) {
			$data['status'] = 'auto_cancel';
			// we get the role ID we want to deatch from the user
			$role_id = $this->userGateway->getRoleByName($subscription->product->roles);
			// removing the role we delete the permissions for that product
			$this->userGateway->revoke($subscription->user->id, $role_id);
		}

		$response = $this->repository->update($data, $subscription->id);
		if($response) {
			if($data['attempts_billing'] < 3) {
				Event::fire(new SubscriptionFailEvent($subscription));
			} else {
				Event::fire(new SubscriptionCancelEvent($subscription));
			}
		}
		return $response;
	}
}