<?php

namespace App\Jobs;

use App\Gateways\ProductGateway;
use App\Gateways\UserGateway;
use App\Jobs\Job;
use App\Models\Subscription;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use DateTime;
use Log;

class SubscriptionCancelJob extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue,
		SerializesModels;

	protected $subscription;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Subscription $sub)
    {
		$this->subscription = $sub;
	}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(UserGateway $user, ProductGateway $productGateway)
    {
		$this->user = $user;
		$this->product = $productGateway;
		$product = $this->product->find($this->subscription->product_id);
		$role_id= $this->user->getRoleByName($product->name);
		$this->user->deatachRole($this->subscription->user_id, $role_id);
		Log::info('Role removed ' . $this->subscription->user_id . ' - roleId: ' . $role_id);
    }
}
