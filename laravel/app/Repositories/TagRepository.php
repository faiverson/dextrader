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
		$tag = $this->model->where('tag', $tag)->select(['id'])->first();
		return $tag != null ? $tag->id : null;
	}

}