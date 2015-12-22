<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Services\HitUpdateValidator;
use App\Services\HitCreateValidator;
use App\Repositories\HitRepository;
use App\Repositories\UserRepository;
use App\Repositories\TagRepository;

class HitGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(HitRepository $repository, UserRepository $user, TagRepository $tag, HitCreateValidator $createValidator, HitUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
		$this->user = $user;
		$this->tag = $tag;
	}

	public function create(array $data)
	{
		if(array_key_exists('tag', $data)) {
			$data['tag_id'] = $this->tag->getIdByTag($data['tag']);
		}

		if(array_key_exists('enroller', $data)) {
			$data['enroller_id'] = $this->user->getIdByUsername($data['enroller']);
		}

		if( ! $this->createValidator->with($data)->passes() )
		{
			$this->errors = $this->createValidator->errors();
			return false;
		}

		return $this->repository->create($data);
	}
}