<?php

namespace App\Listeners;

use App\Gateways\HitGateway;
use App\Gateways\StatsGateway;
use App\Repositories\CommissionRepository;
use App\Repositories\HitRepository;
use App\Repositories\InvoiceDetailRepository;
use App\Repositories\MarketingLinkRepository;
use App\Repositories\ProductRepository;
use App\Repositories\TransactionDetailRepository;
use DateTime;
use Illuminate\Container\Container as App;
use App\Gateways\UserGateway;
use Snowfire\Beautymail\Beautymail;
use Config;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repositories\TransactionRepository;

class MarketingStatsListener //implements ShouldQueue
{
	protected $app;

	protected $statsGateway;

	public function __construct(App $app, StatsGateway $statsGateway)
	{
		$this->app = $app;
		$this->stats = $statsGateway;
	}

    public function onHits($event)
    {
		$hit = $event->hit;
		$hit_repo = new HitRepository($this->app);
		$hit_exists = $hit_repo->findMore($hit->funnel_id, $hit->tag_id, $hit->enroller_id, $hit->ip_address);
		$unique_hits = $hit_exists ? 0 : 1;
		$stat = $this->stats->findByUser($hit->enroller_id, $hit->funnel_id, $hit->tag_id, $hit->created_at->format('Y-m-d'));
		if($stat) {
			$stat->hits += 1;
			$stat->unique_hits += $unique_hits;
			$stat->save();
		} else {
			$funnel_repo = new MarketingLinkRepository($this->app);
			$funnel = $funnel_repo->find($hit->funnel_id);
			$this->stats->create([
				'user_id' => $hit->enroller_id,
				'funnel' => $funnel->title,
				'funnel_id' => $hit->funnel_id,
				'tag' => $hit->tag,
				'tag_id' => $hit->tag_id,
				'hits' => 1,
				'unique_hits' => 1,
				'created_at' => $hit->created_at->format('Y-m-d H:i:s'),
			]);
		}
	}

	/**
     * Handle the event.
     *
     * @param  LiveSignalEvent  $event
     * @return void
     */
	public function subscribe($events)
	{
		$events->listen('App\Events\NewHitEvent', 'App\Listeners\MarketingStatsListener@onHits');
	}
}
