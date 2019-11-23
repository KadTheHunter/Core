<?php

namespace Kad\XOHRCore;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use pocketmine\level\Position;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\Server;
use pocketmine\Player;

class Main extends PluginBase implements Listener{

public $fts = "§7[§dX§aO§dX§aO§7] ";

public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $name = $player->getName();
        $event->setJoinMessage("§0• §7[§b+§7]§f". $name);
        $level = $this->getServer()->getLevelByName("world");
        $x = 0;
        $y = 65;
        $z = 0;
        $pos = new Position($x, $y, $z, $level);
        $player->teleport($pos);
        $player->setGamemode(1);
    }   
    public function onQuit(PlayerQuitEvent $event) {
        $player = $event->getPlayer();
        $name = $player->getName();
        $event->setQuitMessage("§0• §7[§b-§7]§f" . $name);
    }
    public function onDeath(PlayerDeathEvent $event) {
        $player = $event->getPlayer();
        $name = $player->getName();
        $event->setDeathMessage("§0• §7[§4X§7]§f" . $name);
    }
    public function onRespawn(PlayerRespawnEvent $event) {
        $world = $this->getServer()->getLevelByName("world");
        $x = 0;
        $y = 65;
        $z = 0;
        $pos = new Position($x, $y, $z, $world);
        $event->setRespawnPosition($pos);
    }
    public function onPlace(BlockPlaceEvent $event){
        $player = $event->getPlayer();
        $level = $player->getLevel()->getName();
        if($level == "world"){
            if(!$player->hasPermission("verified.user")){
                $event->getPlayer()->sendMessage(TF::RED."You have not been Verified. See Hub for how to Verify, and get access to Build.");
                $event->setCancelled();
            }
        }
    }
    public function onBreak(BlockBreakEvent $event){
        $player = $event->getPlayer();
        $level = $player->getLevel()->getName();
        if($level == "world"){
            if(!$player->hasPermission("verified.user")){
                $event->getPlayer()->sendMessage(TF::RED."You have not been Verified. See Hub for how to Verify, and get access to Build.");
                $event->setCancelled();
            }
        }
    }
    public function onDecay(LeavesDecayEvent $event) {
        $event->setCancelled(true);
    }
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool
    {
        if($cmd->getName() == "gmc") {
            if($sender instanceof Player) {
                if($sender->hasPermission("xohrcore.gmc.use")) {
                    $sender->setGamemode(1);
                    $sender->sendMessage($this->fts . TF::GREEN . "Your gamemode has been set to creative!");
                } else {
                    $sender->sendMessage($this->fts . TF::RED . "An error has occurred. Please contact Jes'kad Ad'aryc#3845 on Discord to report this");    
                }
            }
        }
        if($cmd->getName() == "gms") {
            if($sender instanceof Player) {
                if($sender->hasPermission("xohrcore.gms.use")) {
                    $sender->setGamemode(0);
                    $sender->sendMessage($this->fts . TF::GREEN . "Your gamemode has been set to Survival!");
                } else {
                    $sender->sendMessage($this->fts . TF::RED . "An error has occurred. Please contact Jes'kad Ad'aryc#3845 on Discord to report this");
                }
            }
        }
        if($cmd->getName() == "gma") {
            if($sender instanceof Player) {
                if($sender->hasPermission("xohrcore.gma.use")) {
                    $sender->setGamemode(2);
                    $sender->sendMessage($this->fts . TF::GREEN . "Your gamemode has been set to Adventure!");
                } else {
                    $sender->sendMessage($this->fts . TF::RED . "An error has occurred. Please contact Jes'kad Ad'aryc#3845 on Discord to report this");
                }
            }
        }
        if($cmd->getName() == "gmspc") {
            if($sender instanceof Player) {
                if($sender->hasPermission("xohrcore.gmspc.use")) {
                    $sender->setGamemode(3);
                    $sender->sendMessage($this->fts . TF::GREEN . "Your gamemode has been set to Spectator!");
                } else {
                    $sender->sendMessage($this->fts . TF::RED . "An error has occurred. Please contact Jes'kad Ad'aryc#3845 on Discord to report this");
                }
            }
        }
        if($cmd->getName() == "day") {
            if($sender instanceof Player) {
                if($sender->hasPermission("xohrcore.day.use")) {
                    $sender->getLevel()->setTime(6000);
                    $sender->sendMessage($this->fts . TF::GREEN . "Set the time to Day (6000) in your world!");
                } else {
                    $sender->sendMessage($this->fts . TF::RED . "An error has occurred. Please contact Jes'kad Ad'aryc#3845 on Discord to report this");
                }
            }
        }
        if($cmd->getName() == "night") {
            if($sender instanceof Player) {
                if($sender->hasPermission("xohrcore.night.use")) {
                    $sender->getLevel()->setTime(16000);
                    $sender->sendMessage($this->fts . TF::GREEN . "Set the time to Night (16000) in your world!");
                } else {
                    $sender->sendMessage($this->fts . TF::RED . "An error has occurred. Please contact Jes'kad Ad'aryc#3845 on Discord to report this");
                }
            }
        }
        if($cmd->getName() == "hub") {
            if($sender instanceof Player) {
                $level = $this->getServer()->getLevelByName("world");
                $x = 0;
                $y = 65;
                $z = 0;
                $pos = new Position($x, $y, $z, $level);
                $sender->teleport($pos);
                $sender->sendMessage($this->fts . TF::GOLD . "Teleported to Hub");
            } else {
                $sender->sendMessage($this->fts . TF::RED . "An error has occurred. Please contact Jes'kad Ad'aryc#3845 on Discord to report this");
            }
        }
        if($cmd->getName() == "hybridhub"){
            if($sender->hasPermission("hybrid.member")){
               $level = $this->getServer()->getLevelByName("TerraA");
               $x = 13;
               $y = 72;
               $z = 600;
               $pos = new Position($x, $y, $z, $level);
               $sender->teleport($pos);
               $sender->sendMessage($this->fts . TF::GOLD . "Teleported to Hybridian Prime");
            }
        }       
        if($cmd->getName() == "rules") {
            if($sender instanceof Player) {
                $sender->sendMessage("§6§o§lXOXO High RP Rebooted Rules§r");
                $sender->sendMessage("§f- §eNo Advertising");
                $sender->sendMessage("§f- §eNo NSFW");
                $sender->sendMessage("§f- §eNo cursing. (Censoring words is allowed.)");
                $sender->sendMessage("§f- §eNo asking for OP/Ranks/Perms");
                $sender->sendMessage("§f- §eUse Common Sense. Failure to do so will not exempt you from punishment.");
            }
        }
        if($cmd->getName() == "info") {
            if($sender instanceof Player) {
                $sender->sendMessage("§6§o§lXOXO High RP Rebooted Info§r");
                $sender->sendMessage("§eXOXO High RolePlay is a holdout server for the remnants");
                $sender->sendMessage("§eof JM Pocket Creative, Nebula Games, XOXO High RolePlay, Neptune, Lapis, and Orion.");
                $sender->sendMessage("§eThe Main Owner is LordEllis999, although KadTheHunter does a lot of the coding.");
                $sender->sendMessage("§eThe server is meant to bring together whoever is still there from the past, regardless of the various wars and drama that occured between them.");
                $sender->sendMessage("§eDiscord Link: https://discord.gg/9b2qTXV");
            }
        }
    return true;
    }
}
