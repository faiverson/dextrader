<?php
namespace App\Repositories;

use App\Repositories\Contracts\TagRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\Tag;

class TagRepository extends AbstractRepository implements TagRepositoryInterface
{
	// This is where the "magic" comes from:
	public function model()
	{
		return Tag::class;
	}

	public function getIdByTag($tag) {
		$this->model = $this->model->where('tag', $tag);
		$t = $this->model->select(['id'])->first();
		return $t != null ? $t->id : null;
	}

}