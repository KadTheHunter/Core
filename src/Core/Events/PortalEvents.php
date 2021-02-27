<?php

declare(strict_types=1);

namespace Core\Events;

use pocketmine\event\{
    Listener,
    player\PlayerMoveEvent,
    block\BlockPlaceEvent,
    block\BlockBreakEvent
};
use pocketmine\utils\TextFormat as TF;

use Core\Core;

class PortalEvents implements Listener{

    /** @var Core $plugin */
    private $plugin;

    public function __construct(Core $plugin){
        $this->plugin = $plugin;
    }

    public function Move(PlayerMoveEvent $event) : void{
        if(!$event->getFrom()->equals($event->getTo()) && $event->getTo()->distanceSquared($event->getFrom()) > 0.01){
            $this->plugin->isInPortal($event->getPlayer());
        }
    }
    public function Place(BlockPlaceEvent $event) : void{
        if(isset($this->plugin->sel1[$event->getPlayer()->getName()])){
            $this->plugin->pos1[$event->getPlayer()->getName()] = [$event->getBlock()->x, $event->getBlock()->y, $event->getBlock()->z, $event->getBlock()->level->getFolderName()];
            $event->getPlayer()->sendMessage($this->plugin->mch . TF::GREEN . ' Position 1 set');
            unset($this->plugin->sel1[$event->getPlayer()->getName()]);
            $event->setCancelled();
        }elseif(isset($this->plugin->sel2[$event->getPlayer()->getName()])){
            $this->plugin->pos2[$event->getPlayer()->getName()] = [$event->getBlock()->x, $event->getBlock()->y, $event->getBlock()->z, $event->getBlock()->level->getFolderName()];
            $event->getPlayer()->sendMessage($this->plugin->mch . TF::GREEN . ' Position 2 set');
            unset($this->plugin->sel2[$event->getPlayer()->getName()]);
            $event->setCancelled();
        }    
    }
    public function Break(BlockBreakEvent $event) : void{
        if(isset($this->plugin->sel1[$event->getPlayer()->getName()])){
            $this->plugin->pos1[$event->getPlayer()->getName()] = [$event->getBlock()->x, $event->getBlock()->y, $event->getBlock()->z, $event->getBlock()->level->getFolderName()];
            $event->getPlayer()->sendMessage($this->plugin->mch . TF::GREEN . ' Position 1 set');
            unset($this->plugin->sel1[$event->getPlayer()->getName()]);
            $event->setCancelled();
        }elseif(isset($this->plugin->sel2[$event->getPlayer()->getName()])){
            $this->plugin->pos2[$event->getPlayer()->getName()] = [$event->getBlock()->x, $event->getBlock()->y, $event->getBlock()->z, $event->getBlock()->level->getFolderName()];
            $event->getPlayer()->sendMessage($this->plugin->mch . TF::GREEN . ' Position 2 set');
            unset($this->plugin->sel2[$event->getPlayer()->getName()]);
            $event->setCancelled();
        }
    }
}