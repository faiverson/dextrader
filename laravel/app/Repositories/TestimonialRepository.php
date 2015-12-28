<?php
namespace App\Repositories;

use App\Repositories\Contracts\TestimonialRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\Testimonial;

class TestimonialRepository extends AbstractRepository implements TestimonialRepositoryInterface
{
    // This is where the "magic" comes from:
    public function model()
    {
        return Testimonial::class;
    }

    public function findById($id, $column = 'id', $columns = array('*')) {
        $user = $this->model->where($column, $id);
        return $user->select($columns)->first();
    }

}