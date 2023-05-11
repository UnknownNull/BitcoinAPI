<?php

namespace UnknownNull\BitcoinAPI\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\player\Player;
use pocketmine\Server;

use UnknownNull\BitcoinAPI\BitcoinAPI;

class SeeBitcoinCommand extends Command{
    private $plugin;

    public function __construct(BitcoinAPI $plugin){
        $desc = $plugin->getCommandMessage("seebc");
        parent::__construct("seebc", $desc["description"], $desc["usage"]);

        $this->setPermission("bitcoinapi.command.seeBitcoin");

        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $label, array $params): bool{
        if(!$this->plugin->isEnabled()) return false;
        if(!$this->testPermission($sender)){
            return false;
        }

        $player = array_shift($params);
        if(trim((string)$player) === ""){
            $sender->sendMessage(TextFormat::RED . "Usage: " . $this->getUsage());
            return true;
        }

        if(($p = $this->plugin->getServer()->getPlayerByPrefix($player)) instanceof Player){
            $player = $p->getName();
        }

        $Bitcoin = $this->plugin->myBitcoin($player);
        if($Bitcoin !== false){
            $sender->sendMessage($this->plugin->getMessage("seeBitcoin-seeBitcoin", [$player, $Bitcoin], $sender->getName()));
        }else{
            $sender->sendMessage($this->plugin->getMessage("player-never-connected", [$player], $sender->getName()));
        }
        return true;
    }
}
