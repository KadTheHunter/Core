<?php

declare(strict_types=1);

namespace Core\Events;

use pocketmine\event\{
    Listener,
    player\PlayerJoinEvent,
    player\PlayerChatEvent,
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
    
    public function dJoin(PlayerJoinEvent $event){
        $webHook = new Webhook("https://discord.com/api/webhooks/815622075674263592/fVskqcks-rXc5zODxWmaeh2ZtrZGrPLPcWkAxD0z3Q_LqBpw2u3y_qbx3XTYwsRJP3La");
        $player = $event->getPlayer()->getDisplayName();
        $msg = new Message();
        $msg->setContent($player . " has joined the server.");
        $webHook->send($msg);
    }
    public function dChat(PlayerChatEvent $event){
        $webHook = new Webhook("https://discord.com/api/webhooks/815622075674263592/fVskqcks-rXc5zODxWmaeh2ZtrZGrPLPcWkAxD0z3Q_LqBpw2u3y_qbx3XTYwsRJP3La");
        $player = $event->getPlayer()->getDisplayName();
        $message = $event->getMessage();
        $message = str_replace('@', '', $event->getMessage());
        $msg = new Message();
        $msg->setContent($player . ": " . $message);
        $webHook->send($msg);
    }
    public function dQuit(PlayerQuitEvent $event){
        $webHook = new Webhook("https://discord.com/api/webhooks/815622075674263592/fVskqcks-rXc5zODxWmaeh2ZtrZGrPLPcWkAxD0z3Q_LqBpw2u3y_qbx3XTYwsRJP3La");
        $player = $event->getPlayer()->getDisplayName();
        $msg = new Message();
        $msg->setContent($player . " has left the server.");
        $webHook->send($msg);
    }
}