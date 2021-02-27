<?php

declare(strict_types=1);

namespace Core\Tasks;

use pocketmine\Server;
use pocketmine\entity\{
    Entity,
	Creature,
	Human,
	object\ExperienceOrb,
	object\ItemEntity
};
use pocketmine\scheduler\Task;

use Core\Core;

class EntityClearTask extends Task {

    /** @var Core $plugin */
    private $plugin;

    public function __construct(Core $plugin){
        $this->plugin = $plugin;
    }
    
    public function onRun(int $tick) : void{
        foreach($this->plugin->getServer()->getLevels() as $level){
            foreach($level->getEntities() as $entity){
                if($entity instanceof ItemEntity){
                    $entity->flagForDespawn();
                }elseif($entity instanceof Creature && !$entity instanceof Human){
                    $entity->flagForDespawn();
                }elseif($entity instanceof ExperienceOrb){
                    $entity->flagForDespawn();
                }
            }
        }
    }
}