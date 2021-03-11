<?php

declare(strict_types=1);

namespace Core\Events;

use pocketmine\event\{
    Listener,
    player\PlayerJoinEvent,
    player\PlayerChatEvent,
    player\PlayerDeathEvent,
    player\PlayerQuitEvent
};
use pocketmine\{
    Player,
    Server,
    utils\TextFormat as TF
};

use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;

use Core\Core;

class DiscordEvents implements Listener{

    /** @var Core $plugin */
    private $plugin;

    public function __construct(Core $plugin){
        $this->plugin = $plugin;
    }
    
    public function dJoin(PlayerJoinEvent $event) : void{
        $webHook = new Webhook("https://discord.com/api/webhooks/819701342171299840/We42z4bWvNG1RTLVKXBZX-EM3nZn5Iyk-Z9A8U9xKYmrrEIT1DeFUXJZgmm4EM1Q3KvQ");
        $playerName = $event->getPlayer()->getDisplayName();
        $msg = new Message();
        if(!$event->getPlayer()->hasPlayedBefore() == "true"){
            $msg->setContent($playerName . " has joined for the first time!");
            $webHook->send($msg);
        }else{
            $msg->setContent($playerName . " has joined the server");
            $webHook->send($msg);
        }
    }
    public function dChat(PlayerChatEvent $event) : void{
        $webHook = new Webhook("https://discord.com/api/webhooks/819701342171299840/We42z4bWvNG1RTLVKXBZX-EM3nZn5Iyk-Z9A8U9xKYmrrEIT1DeFUXJZgmm4EM1Q3KvQ");
        $playerName = $event->getPlayer()->getDisplayName();
        $message = $event->getMessage();
        $message = str_replace('@', '', $event->getMessage());
        $msg = new Message();
        $msg->setContent($playerName . ": " . $message);
        $webHook->send($msg);
    }
    public function dDeath(PlayerDeathEvent $event) : void{
        $webHook = new Webhook("https://discord.com/api/webhooks/819701342171299840/We42z4bWvNG1RTLVKXBZX-EM3nZn5Iyk-Z9A8U9xKYmrrEIT1DeFUXJZgmm4EM1Q3KvQ");
        $playerName = $event->getPlayer()->getDisplayName();
        $msg = new Message();
        $msg->setContent($playerName . " died");
        $webHook->send($msg);
    }
    public function dQuit(PlayerQuitEvent $event) : void{
        $webHook = new Webhook("https://discord.com/api/webhooks/819701342171299840/We42z4bWvNG1RTLVKXBZX-EM3nZn5Iyk-Z9A8U9xKYmrrEIT1DeFUXJZgmm4EM1Q3KvQ");
        $playerName = $event->getPlayer()->getDisplayName();
        $msg = new Message();
        $msg->setContent($playerName . " has left the server");
        $webHook->send($msg);
    }
}
