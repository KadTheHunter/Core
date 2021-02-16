<?php

declare(strict_types=1);

namespace Core;

use pocketmine\{
    Player,
    Server
};
use pocketmine\block\Block;
use pocketmine\tile\Sign;
use pocketmine\command\{
    Command,
    CommandSender
};
use pocketmine\entity\{
    Entity,
    Effect,
    EffectInstance
};
use pocketmine\level\{
	Position,
	particle\DestroyBlockParticle,
	sound\EndermanTeleportSound,
	sound\GhastShootSound
};
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\{
	AddActorPacket,
	PlaySoundPacket
};
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

use jojoe77777\FormAPI;
use jojoe77777\FormAPI\SimpleForm;

use function array_diff;
use function scandir;

class Core extends PluginBase{

	public $mch = "§7[§4§lK§r§7]§r";
	
	/** @var array */
	public $cfg;

	/** @var array $signLines */
	public $signLines = [];

	/** @var array $signText */
	public $signText = [];

	/** @var array $seeMessages */
	public $seeMessages = [];

    public function onEnable(){
		@mkdir($this->getDataFolder());
		$this->saveDefaultConfig();
		$this->cfg = $this->getConfig()->getAll();
        $this->getServer()->getPluginManager()->registerEvents(new Events\CoreEvents($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new Events\GriefPrevention($this), $this);
		$this->getScheduler()->scheduleRepeatingTask(new Tasks\EntityClearTask($this), 20 * 60);
		$this->getScheduler()->scheduleRepeatingTask(new Tasks\BroadcastTask($this), 20 * 120);
        foreach(array_diff(scandir($this->getServer()->getDataPath() . "worlds"), ["..", "."]) as $levelName){
            if($this->getServer()->loadLevel($levelName)){
                $this->getLogger()->debug("Successfully loaded §6${levelName}");
            }
        }
	}
    public function Lightning(Player $player) : void{
        $light = new AddActorPacket();
		$light->type = "minecraft:lightning_bolt";
		$light->entityRuntimeId = Entity::$entityCount++;
		$light->metadata = [];
		$light->motion = null;
		$light->yaw = $player->getYaw();
		$light->pitch = $player->getPitch();
		$light->position = new Vector3($player->getX(), $player->getY(), $player->getZ());
		Server::getInstance()->broadcastPacket($player->getLevel()->getPlayers(), $light);
		$block = $player->getLevel()->getBlock($player->getPosition()->floor()->down());
		$particle = new DestroyBlockParticle(new Vector3($player->getX(), $player->getY(), $player->getZ()), $block);
		$player->getLevel()->addParticle($particle);
		$sound = new PlaySoundPacket();
		$sound->soundName = "ambient.weather.thunder";
		$sound->x = $player->getX();
		$sound->y = $player->getY();
		$sound->z = $player->getZ();
		$sound->volume = 1;
		$sound->pitch = 1;
		Server::getInstance()->broadcastPacket($player->getLevel()->getPlayers(), $sound);
    }
	/**
	 * @param string $message
	 * 
	 * @return string
	 */
	public function formatMessage($message){
		return $this->replaceVars($message, array(
			"MAXPLAYERS" => $this->getServer()->getMaxPlayers(),
			"TOTALPLAYERS" => count($this->getServer()->getOnlinePlayers())
		));
	}
	/**
	 * @param string $str
	 * 
	 * @param array $vars
	 * 
	 * @return string
	 */
	public function replaceVars($str, array $vars){
		foreach($vars as $key => $value){
			$str = str_replace("{" . $key . "}", $value, $str);
		}
		return $str;
	}
	public function isSeeMessages(Player $player) : bool{
		return isset($this->seeMessages[$player->getLowerCaseName()]);
	}
	public function setSeeMessages(Player $player){
		$this->seeMessages[$player->getLowerCaseName()] = true;
		$player->sendMessage($this->mch . TF::GREEN . " You have disabled seeing the rotating messages. Do /tm to re-enable them.");
	}
	public function unsetSeeMessages(Player $player){
		unset($this->seeMessages[$player->getLowerCaseName()]);
		$player->sendMessage($this->mch . TF::GREEN . " You have enabled seeing the rotating messages. Do /tm to disable them.");
	}
	/** @var FormAPI $api */
	public function infoForm(Player $player){
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = new SimpleForm(function (Player $player, $data){
            if ($data === null){
                return true;
            }
        });
        $form->setTitle("Main Form");
        $form->setContent("Test 1234567890 ABCEDFGHIJKLMNOPQRSTUVWXYZ abcedfghijklmnopqrstuvwxyz §aMinecraft §bHangout §eServer §dMCH ");
        $form->sendToPlayer($player);
	}



	#################################################################################################
	// Everything after this is Commands
	#################################################################################################



    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args ) :bool
    {
        if(strtolower($cmd->getName()) == "gms"){
			if($sender instanceof Player){
				if($sender->hasPermission("core.gms.use")){
					$sender->setGamemode(0);
					$sender->getLevel()->addSound(new GhastShootSound(new Vector3($sender->getX(), $sender->getY(), $sender->getZ())));
					$sender->sendMessage($this->mch . TF::GREEN . " Your gamemode has been set to Survival!");
				}else{
					$sender->sendMessage($this->mch . TF::RED . " You do not have permission to use this command!");
				}
			}else{
				$sender->sendMessage("Please use this command in-game.");
			}
		}
		if(strtolower($cmd->getName()) == "gmc"){
			if($sender instanceof Player){
				if($sender->hasPermission("core.gmc.use")){
					$sender->setGamemode(1);
					$sender->getLevel()->addSound(new GhastShootSound(new Vector3($sender->getX(), $sender->getY(), $sender->getZ())));
					$sender->sendMessage($this->mch . TF::GREEN . " Your gamemode has been set to Creative!");
				}else{
					$sender->sendMessage($this->mch . TF::RED . " You do not have permission to use this command!");
				}
			}else{
				$sender->sendMessage("Please use this command in-game.");
			}
		}
		if(strtolower($cmd->getName()) == "gma"){
			if($sender instanceof Player){
				if($sender->hasPermission("core.gma.use")){
					$sender->setGamemode(2);
					$sender->getLevel()->addSound(new GhastShootSound(new Vector3($sender->getX(), $sender->getY(), $sender->getZ())));
					$sender->sendMessage($this->mch . TF::GREEN . " Your gamemode has been set to Adventure!");
				}else{
					$sender->sendMessage($this->mch . TF::RED . " You do not have permission to use this command!");
				}
			}else{
				$sender->sendMessage("Please use this command in-game.");
			}
		}
		if(strtolower($cmd->getName()) == "gmspc"){
			if($sender instanceof Player){
				if($sender->hasPermission("core.gmspc.use")){
					$sender->setGamemode(3);
					$sender->getLevel()->addSound(new GhastShootSound(new Vector3($sender->getX(), $sender->getY(), $sender->getZ())));
					$sender->sendMessage($this->mch . TF::GREEN . " Your gamemode has been set to Spectator!");
				}else{
					$sender->sendMessage($this->mch . TF::RED . " You do not have permission to use this command!");
				}
			}else{
				$sender->sendMessage("Please use this command in-game.");
			}
		}
		if(strtolower($cmd->getName()) == "day"){
			if($sender instanceof Player){
				if($sender->hasPermission("core.day.use")){
					$sender->getLevel()->setTime(6000);
					$sender->getLevel()->addSound(new GhastShootSound(new Vector3($sender->getX(), $sender->getY(), $sender->getZ())));
					$sender->sendMessage($this->mch . TF::GREEN . " Set the time to Day (6000) in your world!");
				}else{
					$sender->sendMessage($this->mch . TF::RED . " You do not have permission to use this command!");
				}
			}else{
				$sender->sendMessage("Please use this command in-game.");
			}
		}
		if(strtolower($cmd->getName()) == "night"){
			if($sender instanceof Player){
				if($sender->hasPermission("core.night.use")){
					$sender->getLevel()->setTime(16000);
					$sender->getLevel()->addSound(new GhastShootSound(new Vector3($sender->getX(), $sender->getY(), $sender->getZ())));
					$sender->sendMessage($this->mch . TF::GREEN . " Set the time to Night (16000) in your world!");
				}else{
					$sender->sendMessage($this->mch . TF::RED . " You do not have permission to use this command!");
				}
			}else{
				$sender->sendMessage("Please use this command in-game.");
			}
		}
        if(strtolower($cmd->getName()) == "nv"){
			if($sender instanceof Player){
				if($sender->getEffect(Effect::NIGHT_VISION)){
					$sender->sendMessage($this->mch . TF::GREEN . " Night Vision turned off!");
					$sender->removeEffect(Effect::NIGHT_VISION);
				}else{
					$sender->sendMessage($this->mch . TF::GREEN . " Night Vision turned on!");
					$sender->addEffect(new EffectInstance(Effect::getEffectByName("NIGHT_VISION"), INT32_MAX, 1, false));
				}
			}else{
				$sender->sendMessage("This command only works in game");
			}
		}
        if(strtolower($cmd->getName()) == "clearinv"){
			if($sender instanceof Player){
				$sender->getInventory()->clearAll();
				$sender->getLevel()->addSound(new GhastShootSound(new Vector3($sender->getX(), $sender->getY(), $sender->getZ())));
			}else{
				$sender->sendMessage("Please use this command in-game.");
			}
		}
		if(strtolower($cmd->getName()) == "tpworld"){
			if($sender instanceof Player){
				if($sender->hasPermission("core.tpworld.use")){
					if(isset($args[0])){
						$world = strtolower($args[0]);
						if($this->getServer()->isLevelLoaded($world)){
							$level = $this->getServer()->getLevelByName($world);
							$sender->teleport($level->getSafeSpawn());
							$sender->getLevel()->addSound(new GhastShootSound(new Vector3($sender->getX(), $sender->getY(), $sender->getZ())));
							$sender->sendMessage($this->mch . TF::GREEN . " You have been teleported to " . TF::GOLD . $world);
						}else{
							$sender->sendMessage($this->mch . TF::RED . " Error: World " . TF::GOLD . $world . TF::RED . "does not exist.");
						}
					}else{
						$sender->sendMessage($this->mch . TF::RED . " Error: missing arguments.");
						$sender->sendMessage($this->mch . TF::RED . " Usage: /tpworld <freebuild|city>");
					}
				}else{
					$sender->sendMessage($this->mch . TF::RED . " You do not have permission to use this command!");
				}
			}else{
				$sender->sendMessage("Please use this command in-game.");
			}
		}
        if(strtolower($cmd->getName()) == "itemid"){
            if($sender instanceof Player){
                $item = $sender->getInventory()->getItemInHand()->getId();
                $damage = $sender->getInventory()->getItemInHand()->getDamage();
                $sender->sendMessage($this->mch . TF::GREEN . " ID: " . $item . ":" . $damage);
            }else{
                $sender->sendMessage("Please use this command in-game.");
            }
        }
        if(strtolower($cmd->getName()) == "lightning"){
			if($sender instanceof Player){
				if($sender->hasPermission("core.lightning.use")){
					$this->Lightning($sender);
				}else{
					$sender->sendMessage($this->mch . TF::RED . " You do not have permission to use this command!");
				}
			}else{
				$sender->sendMessage("Please run this command in-game.");
			}
		}
		if(strtolower($cmd->getName()) == "changesign"){
			if(!$sender instanceof Player){
				$sender->sendMessage("Please use this command in-game.");
				return false;
			}
			if(!$sender->hasPermission("core.changesign.use")){
				$sender->sendMessage($this->mch . TF::RED . " You do not have permission to use this command!");
				return false;
			}
			if(empty($args[0])){
				$sender->sendMessage($this->mch . TF::GREEN . " Usage: /cs <line #> <text>");
				return false;
			}
			switch($args[0]){
				case "1":
					$this->signLines[$sender->getName()] = 0;
					$this->signText[$sender->getName()] = implode(" ", array_slice($args, 1));
					$sender->sendMessage($this->mch . TF::GREEN . " Tap a sign now to change the first line of text");
					break;
				case "2":
					$this->signLines[$sender->getName()] = 1;
					$this->signText[$sender->getName()] = implode(" ", array_slice($args, 1));
					$sender->sendMessage($this->mch . TF::GREEN . " Tap a sign now to change the second line of text");
					break;
				case "3":
					$this->signLines[$sender->getName()] = 2;
					$this->signText[$sender->getName()] = implode(" ", array_slice($args, 1));
					$sender->sendMessage($this->mch . TF::GREEN . " Tap a sign now to change the third line of text");
					break;
				case "4":
					$this->signLines[$sender->getName()] = 3;
					$this->signText[$sender->getName()] = implode(" ", array_slice($args, 1));
					$sender->sendMessage($this->mch . TF::GREEN . " Tap a sign now to change the fourth line of text");
					break;
				default:
					$sender->sendMessage($this->mch . TF::GRAY . " Usage: /cs <line #> <text>");
					break;
			}
		}
		if(strtolower($cmd->getName()) == "playtime"){
			if($sender instanceof Player){
				$time = ((int) floor(microtime(true) * 1000)) - $sender->getFirstPlayed() ?? microtime();
        		$seconds = floor($time % 60);
        		$minutes = null;
        		$hours = null;
        		$days = null;
        		if($time >= 60){
            		$minutes = floor(($time % 3600) / 60);
            		if($time >= 3600){
                		$hours = floor(($time % (3600 * 24)) / 3600);
                		if($time >= 3600 * 24){
                    		$days = floor($time / (3600 * 24));
                		}
            		}
        		}
        		$uptime = ($minutes !== null ?
                		($hours !== null ?
                    		($days !== null ?
                        		"$days days "
                        		: "") . "$hours hours "
                    		: "") . "$minutes minutes "
                		: "") . "$seconds seconds";
        		$sender->sendMessage($this->mch . TF::GREEN . " Playtime: " . $uptime);
			}else{
				$sender->sendMessage("The console is immortal. To measure it's playtime would be impossible.");
			}
		}
		if(strtolower($cmd->getName()) == "togglemessages"){
			if($this->isSeeMessages($sender)){
				$this->unsetSeeMessages($sender);
			}else{
				$this->setSeeMessages($sender);
			}
		}
        # All commands after this will likely need modifications more than once.
		if(strtolower($cmd->getName()) == "hub"){
			if($sender instanceof Player){
				$x = 0.5;
				$y = 44;
				$z = 0.5;
				$level = $this->getServer()->getLevelByName("freebuild");
				$pos = new Position($x, $y, $z, $level);
				$sender->teleport($pos);
				$sender->getLevel()->addSound(new EndermanTeleportSound(new Vector3($sender->getX(), $sender->getY(), $sender->getZ())));
				$sender->sendMessage($this->mch . TF::GOLD . " Teleported to Hub");
			}else{
				$sender->sendMessage("Sir, you just tried to teleport a non-existent entity into a virtual game to teleport them to another world in said game. I recommend you go see a psychologist.");
			}
		}
		if(strtolower($cmd->getName()) == "rules"){
			if($sender instanceof Player){
				$sender->sendMessage("§6§o§lServer Rules§r");
				$sender->sendMessage("§f- §eNo griefing. §c(§4Ban§c)");
				$sender->sendMessage("§f- §eNo advertising in any way, shape or form. §c(§4Ban§c)");
			    $sender->sendMessage("§f- §eNo NSFW/18+ Builds, Chat or Content. §c(§4Ban§c)");
			    $sender->sendMessage("§f- §eNo asking for OP/Ranks/Perms. §c(§4Kick, then Ban§c)");
			    $sender->sendMessage("§f- §eNo Drama. We've all had enough of it elsewhere, please do not bring it here. §c(§4Kick, then Ban§c)");
                $sender->sendMessage("§f- §eNo Lavacasts/Other excessive usages of Lava and Water. §c(§4Ban§c)");
                $sender->sendMessage("§f- §eNo Dolphin Porn. §c(§4Ban§c)");
			    $sender->sendMessage("§f- §eThat's it, have fun §b:)§e");
			}else{
				$sender->sendMessage("If you have console access you BETTER know the fucking rules...");
			}
		}
		if(strtolower($cmd->getName()) == "info"){
			if($sender instanceof Player){
				$this->infoForm($sender);
			}else{
				$sender->sendMessage("Please use this command in-game.");
			}
		}
		return true;
	}
}