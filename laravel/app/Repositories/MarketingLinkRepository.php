<?php
namespace App\Repositories;

use App\Models\MarketingLink;
use App\Models\Product;
use App\Repositories\Contracts\MarketingLinkRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;

class MarketingLinkRepository extends AbstractRepository implements MarketingLinkRepositoryInterface
{
	public function model()
	{
		return MarketingLink::class;
	}
}