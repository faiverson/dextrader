<?php

namespace App\Listeners;

use App\Gateways\StatsGateway;
use App\Repositories\HitRepository;
use App\Repositories\InvoiceDetailRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\MarketingLinkRepository;
use App\Repositories\TagRepository;
use Illuminate\Container\Container as App;
use Config;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MarketingStatsListener implements ShouldQueue
{
	use InteractsWithQueue;

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
		if($hit->enroller_id) {
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
	}

	public function onLeads($event)
	{
		$lead = $event->lead;
		$stat = $this->stats->findByUser($lead->enroller_id, $lead->funnel_id, $lead->tag_id, $lead->created_at->format('Y-m-d'));
		if($stat) {
			$stat->leads += 1;
			$stat->save();
		} else {
			$funnel_repo = new MarketingLinkRepository($this->app);
			$tag_repo = new TagRepository($this->app);
			$funnel = $funnel_repo->find($lead->funnel_id);
			$tag = $tag_repo->find($lead->tag_id);
			$this->stats->create([
				'user_id' => $lead->enroller_id,
				'funnel' => $funnel->title,
				'funnel_id' => $lead->funnel_id,
				'tag' => $tag->tag,
				'tag_id' => $lead->tag_id,
				'leads' => 1,
				'created_at' => $lead->created_at->format('Y-m-d H:i:s'),
			]);
		}
	}

	public function onCommissions($event)
	{
		$comm = $event->commission;
		if($comm->type == 'parent' || $comm->type == 'enroller') {
			$invoice_repo = new InvoiceRepository($this->app);
			$invoice = $invoice_repo->find($comm->invoice_id);
			$stat = $this->stats->findByUser($comm->to_user_id, $invoice->funnel_id, $invoice->tag_id, $comm->created_at->format('Y-m-d'));
			if($stat) {
				$stat->income += $comm->amount;
				$stat->save();
			} else {
				$funnel_repo = new MarketingLinkRepository($this->app);
				$tag_repo = new TagRepository($this->app);
				$funnel = $funnel_repo->find($invoice->funnel_id);
				$tag = $tag_repo->find($invoice->tag_id);
				$this->stats->create([
					'user_id' => $comm->to_user_id,
					'funnel' => $funnel->title,
					'funnel_id' => $invoice->funnel_id,
					'tag' => $tag->tag,
					'tag_id' => $invoice->tag_id,
					'income' => $comm->amount,
					'created_at' => $comm->created_at->format('Y-m-d H:i:s'),
				]);
			}
		}
	}

	public function onPurchase($event)
	{
		$purchase = $event->purchase;
		if($purchase->enroller_id) {
			$invoice_detail_repo = new InvoiceDetailRepository($this->app);
			$invoice_detail = $invoice_detail_repo->findBy('invoice_id', $purchase->invoice_id);
			$stat = $this->stats->findByUser($purchase->enroller_id, $purchase->funnel_id, $purchase->tag_id, $purchase->created_at->format('Y-m-d'));
			if($stat) {
				foreach($invoice_detail as $detail) {
					$product = strtolower($detail->product_name);
					$stat->{$product} += 1;
				}
				$stat->save();
			} else {
				$funnel_repo = new MarketingLinkRepository($this->app);
				$tag_repo = new TagRepository($this->app);
				$funnel = $funnel_repo->find($purchase->funnel_id);
				$tag = $tag_repo->find($purchase->tag_id);
				$data = [
					'user_id' => $purchase->enroller_id,
					'funnel' => $funnel->title,
					'funnel_id' => $purchase->funnel_id,
					'tag' => $tag->tag,
					'tag_id' => $purchase->tag_id,
					'created_at' => $purchase->created_at->format('Y-m-d H:i:s'),
				];

				foreach($invoice_detail as $detail) {
					$product = strtolower($detail->product_name);
					$data[$product] = 1;
				}
//				dd($data);

				$this->stats->create($data);
			}
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
		$events->listen('App\Events\NewLeadEvent', 'App\Listeners\MarketingStatsListener@onLeads');
		$events->listen('App\Events\CommissionEvent', 'App\Listeners\MarketingStatsListener@onCommissions');
		$events->listen('App\Events\NewPurchaseEvent', 'App\Listeners\MarketingStatsListener@onPurchase');
	}
}
