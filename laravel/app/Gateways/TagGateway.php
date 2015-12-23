<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Services\TagCreateValidator;
use App\Services\TagUpdateValidator;
use App\Repositories\TagRepository;

class TagGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(TagRepository $repository, TagCreateValidator $createValidator, TagUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}

	public function getIdByTag($tag) {
		return $this->repository->getIdByTag($tag);
	}
}