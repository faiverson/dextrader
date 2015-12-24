<?php
namespace App\Repositories;

use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\Invoice;

class InvoiceRepository extends AbstractRepository implements InvoiceRepositoryInterface
{
	public function model()
	{
		return Invoice::class;
	}
}