<?php

namespace UnknownNull\BitcoinAPI\command;

//use pocketmine\event\TranslationContainer;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\player\Player;
use pocketmine\Server;

use UnknownNull\BitcoinAPI\BitcoinAPI;

class MyBitcoinCommand extends Command{
    private $plugin;

    public function __construct(BitcoinAPI $plugin){
        $desc = $plugin->getCommandMessage("mybc");
        parent::__construct("mybc", $desc["description"], $desc["usage"]);

        $this->setPermission("bitcoinapi.command.myBitcoin");

        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $label, array $params): bool{
        if(!$this->plugin->isEnabled()) return false;
        if(!$this->testPermission($sender)){
            return false;
        }

        if($sender instanceof Player){
            $Bitcoin = $this->plugin->myBitcoin($sender);
            $sender->sendMessage($this->plugin->getMessage("mycoin-mycoin", [$Bitcoin]));
            return true;
        }
        $sender->sendMessage(TextFormat::RED."Please run this command in-game.");
        return true;
    }
}
