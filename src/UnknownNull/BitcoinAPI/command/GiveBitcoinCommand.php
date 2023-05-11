<?php

namespace UnknownNull\BitcoinAPI\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\player\Player;
use pocketmine\Server;

use UnknownNull\BitcoinAPI\BitcoinAPI;

class GiveBitcoinCommand extends Command{
    private $plugin;

    public function __construct(BitcoinAPI $plugin){
        $desc = $plugin->getCommandMessage("givebc");
        parent::__construct("givebc", $desc["description"], $desc["usage"]);

        $this->setPermission("bitcoinapi.command.giveBitcoin");

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

        $result = $this->plugin->addBitcoin($player, $amount);
        switch($result){
            case BitcoinAPI::RET_INVALID:
            $sender->sendMessage($this->plugin->getMessage("giveBitcoin-invalid-number", [$amount], $sender->getName()));
            break;
            case BitcoinAPI::RET_SUCCESS:
            $sender->sendMessage($this->plugin->getMessage("giveBitcoin-gave-Bitcoin", [$amount, $player], $sender->getName()));

            if($p instanceof Player){
                $p->sendMessage($this->plugin->getMessage("giveBitcoin-Bitcoin-given", [$amount], $sender->getName()));
            }
            break;
            case BitcoinAPI::RET_CANCELLED:
            $sender->sendMessage($this->plugin->getMessage("request-cancelled", [], $sender->getName()));
            break;
            case BitcoinAPI::RET_NO_ACCOUNT:
            $sender->sendMessage($this->plugin->getMessage("player-never-connected", [$player], $sender->getName()));
            break;
        }
        return true;
    }
}
