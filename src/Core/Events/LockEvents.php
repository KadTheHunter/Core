<?php

declare(strict_types=1);

namespace Core\Events;


use pocketmine\Player;
use pocketmine\level\Position;
use pocketmine\tile\Chest;
use pocketmine\item\{
    Item,
    ItemFactory,
    ItemIds
};
use pocketmine\block\{
    Door,
    Trapdoor
};
use pocketmine\event\{
    Listener,
    block\BlockBreakEvent,
    block\BlockPlaceEvent,
    player\PlayerInteractEvent
};

use SQLite3;

use Core\Core;

class LockEvents implements Listener{

    /** @var Core $plugin */
    private $plugin;

    public function __construct(Core $plugin){
        $this->plugin = $plugin;
    }

    public function wireHook(BlockPlaceEvent $event){
        if($event->getBlock()->getItemId() == $this->plugin->itemID){
            $event->setCancelled();
        }
    }
    public function Touch(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if($block instanceof Door || $block instanceof \pocketmine\block\Chest || $block instanceof Trapdoor){
            if(isset($this->plugin->lockSession[$player->getName()])){
                if($this->plugin->isLockedDown($event->getBlock(), $event->getItem()) === null){
                    $item = ItemFactory::get($this->plugin->itemID);
                    $item->clearCustomName();
                    $item->setCustomName($this->plugin->lockSession[$player->getName()]);
                    $player->getInventory()->addItem($item);
                    $player->sendPopup($this->plugin->mch . TF::GREEN . " You have received the key, be sure to check your inventory for it");
                    $event->setCancelled();
                    $player->sendMessage($this->plugin->mch . TF::GREEN . " The door has been locked");
                    $this->plugin->lock($event);
                }else{
                    $event->setCancelled();
                    unset($this->plugin->lockSession[$player->getName()]);
                    $player->sendMessage($this->plugin->mch . TF::GOLD . " Error: This item is already locked!");
                }
            }elseif(isset($this->plugin->infoSession[$player->getName()])){
                $x = $event->getBlock()->getX();
                $y = $event->getBlock()->getY();
                $z = $event->getBlock()->getZ();
                $world = $player->getLevel()->getName();
                $name = $this->plugin->getLockedName($x, $y, $z, $world);
                unset($this->plugin->infoSession[$player->getName()]);
                $event->setCancelled();
                $event->getPlayer()->sendMessage($this->plugin->mch . TF::GREEN . " The name of the locked item is: §b$name");
            }else{
                if($player->hasPermission("core.locks.bypass")){
                    return;
                }elseif($this->plugin->isLockedDown($event->getBlock(), $event->getItem())){
                    $event->setCancelled();
                    $player->sendPopup($this->plugin->mch . TF::GOLD . " This item is locked.");
                }
            }
        }
    }
    public function chestTouch(PlayerInteractEvent $event){
        if($event->getBlock()->getItemId() == ItemIds::CHEST){
            $tile = $event->getPlayer()->getLevel()->getTile($event->getBlock());
            if($tile instanceof Chest){
                if($tile->isPaired()){
                    $chest = $tile->getPair();
                    $block = $event->getPlayer()->getLevel()->getBlock($chest);
                    if($block instanceof \pocketmine\block\Chest){
                        if($this->plugin->isLockedDown($block, $event->getItem())){
                            $event->setCancelled();
                            $event->getPlayer()->sendPopup($this->plugin->mch . TF::GOLD . " This item is locked.");
                        }
                    }
                }
            }
        }
    }
    public function unlockTouch(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        if(isset($this->plugin->unlockSession[$player->getName()])){
            if($this->plugin->unlockSession[$player->getName()]){
                $event->setCancelled();
                $x = $event->getBlock()->getX();
                $y = $event->getBlock()->getY();
                $z = $event->getBlock()->getZ();
                $locked_id = $this->plugin->getLockedID($x, $y, $z, $event->getPlayer()->getLevel()->getName());
                $stmt = $this->plugin->handle->prepare("DELETE FROM doors WHERE door_id = :locked_id");
                $stmt->bindParam(":locked_id", $locked_id, SQLITE3_INTEGER);
                $stmt->execute();
                $stmt->close();
                $player->sendMessage($this->plugin->mch . TF::GREEN . " The door has been unlocked");
                unset($this->plugin->unlockSession[$player->getName()]);
            }
        }
    }
    public function DoorBreak(BlockBreakEvent $event){
        $block = $event->getBlock();
        if($block instanceof Door || $block instanceof \pocketmine\block\Chest || $block instanceof Trapdoor){
            $door_status = $this->plugin->isLockedDown($event->getBlock(), $event->getItem());
            if($door_status !== null){
                if(!$door_status || $event->getPlayer()->hasPermission("core.locks.break")){
                    $x = $block->getX();
                    $y = $block->getY();
                    $z = $block->getZ();
                    $locked_id = $this->plugin->getLockedID($x, $y, $z, $event->getPlayer()->getLevel()->getName());
                    $stmt = $this->plugin->handle->prepare("DELETE FROM doors WHERE door_id = :locked_id");
                    $stmt->bindParam(":locked_id", $locked_id, SQLITE3_INTEGER);
                    $stmt->execute();
                    $stmt->close();
                    $event->getPlayer()->sendMessage($this->plugin->mch . TF::GREEN . " The door has been unlocked");
                }else{
                    $event->setCancelled();
                    $event->getPlayer()->sendPopup($this->plugin->mch . TF::GOLD . " You can't break this item!");
                }
            }
        }
        if($block instanceof \pocketmine\block\Chest){
            $tile = $event->getPlayer()->getLevel()->getTile($event->getBlock());
            if($tile instanceof Chest){
                if($tile->isPaired()){
                    $chest = $tile->getPair();
                    $block = $event->getPlayer()->getLevel()->getBlock($chest);
                    if($block instanceof \pocketmine\block\Chest){
                        if($this->plugin->isLockedDown($block, $event->getItem())){
                            $event->setCancelled();
                            $event->getPlayer()->sendPopup($this->plugin->mch . TF::GOLD . " You can't break this item!");
                        }
                    }
                }
            }
        }
    }
}