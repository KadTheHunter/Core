<?php

declare(strict_types=1);
namespace Core\Events;

use pocketmine\event\{
	Listener,
	player\PlayerJoinEvent,
	player\PlayerQuitEvent,
	player\PlayerDeathEvent,
	player\PlayerInteractEvent,
	block\LeavesDecayEvent
};
use pocketmine\{
    Player,
    Server
};
use pocketmine\block\Block;
use pocketmine\tile\Sign;
use pocketmine\utils\TextFormat as TF;

use Core\Core;

class CoreEvents implements Listener{

    /** @var Core $plugin */
    private $plugin;

    public function __construct(Core $plugin){
        $this->plugin = $plugin;
    }
    public function Join(PlayerJoinEvent $event) : void{
        $name = $event->getPlayer()->getName();
		$event->setJoinMessage("§7[§b§l+§r§7]§r§f " . "$name");
		$player = $event->getPlayer();
		$player->setGamemode(1);
    }
    public function Leave(PlayerQuitEvent $event) : void{
        $name = $event->getPlayer()->getName();
        $event->setQuitMessage("§7[§c§l-§r§7]§r§f " . "$name");
    }
    public function Death(PlayerDeathEvent $event) : void{
		if($event->getPlayer()->hasPermission("core.lightning.use")){
			$this->plugin->Lightning($event->getPlayer());
		}
    }
	public function Interact(PlayerInteractEvent $event) : void{
		$player = $event->getPlayer();
		$tile = $event->getPlayer()->getLevel()->getTile($event->getBlock());
		if($tile instanceof Sign){
			if(isset($this->plugin->signLines[$player->getName()]) && isset($this->plugin->signText[$player->getName()])){
				$tile->setLine($this->plugin->signLines[$player->getName()], $this->plugin->signText[$player->getName()]);
				$player->sendMessage($this->plugin->mch . TF::GREEN . " You have successfully set line #" . strval($this->plugin->signLines[$player->getName()] + 1) . " of this sign");
				unset($this->plugin->signLines[$player->getName()]);
				unset($this->plugin->signText[$player->getName()]);
			}
		}
	}
	/**
	 * @param LeavesDecayEvent $event
	 * @priority HIGHEST
	 */
	public function Decay(LeavesDecayEvent $event) : void{
		$event->setCancelled(true);
	}
}