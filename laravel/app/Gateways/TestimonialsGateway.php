<?php
namespace App\Gateways;

use App\Services\TestimonialUpdateValidator;
use App\Services\TestimonialCreateValidator;
use App\Repositories\TestimonialRepository;

class TestimonialsGateway extends AbstractGateway {

    protected $repository;

    protected $createValidator;

    protected $updateValidator;

    protected $errors;

    public function __construct(TestimonialRepository $repository, TestimonialCreateValidator $createValidator, TestimonialUpdateValidator $updateValidator)
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;
    }

    public function create(array $data)
    {
        if( ! $this->createValidator->with($data)->passes() )
        {
            $this->errors = $this->createValidator->errors();
            return false;
        }

        return $this->repository->create($data);
    }

    public function update(array $data, $id)
    {
        if( ! $this->updateValidator->with($data)->passes() )
        {
            $this->errors = $this->updateValidator->errors();
            return false;
        }

        return $this->repository->update($data, $id);
    }

    public function findById($id) {
        return $this->repository->findById($id);
    }
}