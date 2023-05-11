<?php

namespace UnknownNull\BitcoinAPI\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\player\Player;

use UnknownNull\BitcoinAPI\BitcoinAPI;

class MyStatusBitcoinCommand extends Command{
    private $plugin;

    public function __construct(BitcoinAPI $plugin){
        $desc = $plugin->getCommandMessage("mystatusbc");
        parent::__construct("mystatusbc", $desc["description"], $desc["usage"]);

        $this->setPermission("bitcoinapi.command.mystatusBitcoin");

        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $label, array $params): bool{
        if(!$this->plugin->isEnabled()) return false;
        if(!$this->testPermission($sender)){
            return false;
        }

        if(!$sender instanceof Player){
            $sender->sendMessage(TextFormat::RED . "Please run this command in-game.");
            return true;
        }

        $Bitcoin = $this->plugin->getAllCoin();

        $allCoin = 0;
        foreach($Bitcoin as $m){
            $allCoin += $m;
        }
        $topCoin = 0;
        if($allCoin > 0){
            $topCoin = round((($Bitcoin[strtolower($sender->getName())] / $allCoin) * 100), 2);
        }

        $sender->sendMessage($this->plugin->getMessage("mystatuspp-show", [$topCoin], $sender->getName()));
        return true;
    }
}
