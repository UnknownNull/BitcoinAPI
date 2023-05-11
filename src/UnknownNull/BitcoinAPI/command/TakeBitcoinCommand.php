<?php

namespace UnknownNull\BitcoinAPI\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\player\Player;
use pocketmine\Server;

use UnknownNull\BitcoinAPI\BitcoinAPI;

class TakeBitcoinCommand extends Command{
    private $plugin;

    public function __construct(BitcoinAPI $plugin){
        $desc = $plugin->getCommandMessage("takebc");
        parent::__construct("takebc", $desc["description"], $desc["usage"]);

        $this->setPermission("bitcoinapi.command.takeBitcoin");

        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $label, array $params): bool{
        if(!$this->plugin->isEnabled()) return false;
        if(!$this->testPermission($sender)){
            return false;
        }

        $player = array_shift($params);
        $amount = array_shift($params);

        if(!is_numeric($amount)){
            $sender->sendMessage(TextFormat::RED . "Usage: " . $this->getUsage());
            return true;
        }

        if(($p = $this->plugin->getServer()->getPlayerByPrefix($player)) instanceof Player){
            $player = $p->getName();
        }

        if($amount < 0){
            $sender->sendMessage($this->plugin->getMessage("takeBitcoin-invalid-number", [$amount], $sender->getName()));
            return true;
        }

        $result = $this->plugin->reduceBitcoin($player, $amount);
        switch($result){
            case BitcoinAPI::RET_INVALID:
            $sender->sendMessage($this->plugin->getMessage("takeBitcoin-player-lack-of-Bitcoin", [$player, $amount, $this->plugin->myBitcoin($player)], $sender->getName()));
            break;
            case BitcoinAPI::RET_SUCCESS:
            $sender->sendMessage($this->plugin->getMessage("takeBitcoin-took-Bitcoin", [$player, $amount], $sender->getName()));

            if($p instanceof Player){
                $p->sendMessage($this->plugin->getMessage("takeBitcoin-Bitcoin-taken", [$amount], $sender->getName()));
            }
            break;
            case BitcoinAPI::RET_CANCELLED:
            $sender->sendMessage($this->plugin->getMessage("takeBitcoin-failed", [], $sender->getName()));
            break;
            case BitcoinAPI::RET_NO_ACCOUNT:
            $sender->sendMessage($this->plugin->getMessage("player-never-connected", [$player], $sender->getName()));
            break;
        }

        return true;
    }
}
