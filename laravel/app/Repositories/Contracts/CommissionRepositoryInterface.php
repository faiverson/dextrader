<?php namespace App\Repositories\Contracts;

use App\Repositories\Contracts\RepositoryInterface;
use DateTime;
/**
 * The UserRepositoryInterface contains ONLY method signatures for methods
 * related to the User object.
 *
 * Note that we extend from RepositoryInterface, so any class that implements
 * this interface must also provide all the standard eloquent methods (find, all, etc.)
 */
interface CommissionRepositoryInterface extends RepositoryInterface {
	public function getIdByCommission($tag);
	public function getPendingToReady($limit);
	public function getCommissionToPay(DateTime $date);
	public function updateToReady($ids);
	public function updateHoldbackToReady($ids);
	public function updateToPaid($ids);
	public function updateHoldbackToPaid($ids);
	public function payCommissionOnNextDate($ids);
	public function payHoldbacksOnNextDate($ids);
	public function getUserCommissions($id, $limit, $offset, $order_by, $where);
	public function getTotalUserCommissions($id, $where);
	public function getSummaryUserCommissions($id);
	public function findByInvoice($invoice_id);
}