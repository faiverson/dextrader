<?php
namespace App\Repositories;

use App\Repositories\Contracts\LeadRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\Lead;

class LeadRepository extends AbstractRepository implements LeadRepositoryInterface
{
	public function model()
	{
		return Lead::class;
	}

	public function findByEmail($email) {
		return $this->model->where('email', $email)->first();
	}
}