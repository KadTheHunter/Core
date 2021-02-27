<?php

declare(strict_types=1);

namespace Core\Events;

use pocketmine\event\{
    Listener,
    player\PlayerBucketEmptyEvent,
    entity\EntityExplodeEvent,
    block\BlockBurnEvent
};

use Core\Core;

class GriefPrevention implements Listener{

    /** @var Core $plugin */
    private $plugin;

    public function __construct(Core $plugin){
        $this->plugin = $plugin;
    }
    
    public function Empty(PlayerBucketEmptyEvent $event) : void{
		$event->setCancelled(true);
	}
	public function Explode(EntityExplodeEvent $event) : void{
		$event->setCancelled(true);
	}
	public function Burn(BlockBurnEvent $event) : void{
		$event->setCancelled(true);
	}
}