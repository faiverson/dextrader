<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Services\HitUpdateValidator;
use App\Services\HitCreateValidator;
use App\Repositories\HitRepository;
use App\Repositories\UserRepository;
use App\Repositories\TagRepository;
use GeoIP;

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
		if(array_key_exists('enroller', $data)) {
			$enroller_id = $this->user->getIdByUsername($data['enroller']);
			if($enroller_id) {
				$data['enroller_id'] = $enroller_id;
			}
		}

		if(array_key_exists('tag', $data) && array_key_exists('enroller_id', $data) ) {
			$tag = $this->tag->getIdByTag($data['enroller_id'], $data['tag']);
			if($tag) {
				$data['tag_id'] = $tag->id;
			}
		}
		$data['info'] = $this->setInfo($data);

		if( ! $this->createValidator->with($data)->passes() )
		{
			$this->errors = $this->createValidator->errors();
			return false;
		}

		return $this->repository->create($data);
	}

	protected function setInfo(array $info)
	{
		$geo = GeoIP::getLocation($info['ip_address']);
		if(array_key_exists('info', $info)) {
			return array_merge($geo, $info['info']);
		}
		return $geo;
	}
}