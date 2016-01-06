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

	public function getIdByTag($user_id, $tag) {
		$tag = $this->model->firstOrCreate(['tag' => $tag, 'user_id' => $user_id]);
		return $tag;
	}

}