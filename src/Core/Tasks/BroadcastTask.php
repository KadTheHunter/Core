<?php

declare(strict_types=1);

namespace Core\Tasks;

use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat as TF;

use Core\Core;

class BroadcastTask extends Task{

    /** @var Core $plugin */
    private $plugin;

    /** @var int $i */
    private $i;

    public function __construct(Core $plugin){
        $this->plugin = $plugin;
            $this->i = 0;
    }
    
    public function onRun(int $currentTick){
        $messages = $this->plugin->cfg["message-broadcast"]["messages"];
        back:
        if($this->i <= count($messages)){
            foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
                if(!$this->plugin->isSeeMessages($p)){
                    $p->sendMessage(TF::colorize($this->plugin->formatMessage($messages[$this->i])));
                    $this->i++;
                } 
            }
        }else{
            $this->i = 0;
            goto back;
        }
    }
}