<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Services\HitUpdateValidator;
use App\Services\HitCreateValidator;
use App\Repositories\HitRepository;
use App\Repositories\UserRepository;
use Tag;

class HitGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(HitRepository $repository, UserRepository $user, HitCreateValidator $createValidator, HitUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
		$this->user = $user;
	}

	public function create(array $data)
	{
		if(array_key_exists('tag', $data)) {
			$tag = Tag::where('title', $data['tag'])->first(['id']);
			if($tag != null) {
				$data['tag_id'] = $tag->id;
			}
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