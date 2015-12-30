<?php
namespace App\Repositories;

use App\Repositories\Contracts\InvoiceDetailRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\InvoiceDetail;

class InvoiceDetailRepository extends AbstractRepository implements InvoiceDetailRepositoryInterface
{
	public function model()
	{
		return InvoiceDetail::class;
	}
}