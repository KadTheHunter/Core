<?php

declare(strict_types=1);

namespace Kad\Core;

use pocketmine\scheduler\Task;

class EntityClearTask extends Task {

    public function onRun(int $tick) : void{
        foreach($this->getServer()->getLevels() as $level){
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