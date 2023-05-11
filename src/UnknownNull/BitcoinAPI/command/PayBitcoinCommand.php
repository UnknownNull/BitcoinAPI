<?php

namespace UnknownNull\BitcoinAPI\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\player\Player;
use pocketmine\Server;
use UnknownNull\BitcoinAPI\BitcoinAPI;
use UnknownNull\BitcoinAPI\event\Bitcoin\PayBitcoinEvent;

class PayBitcoinCommand extends Command{
    private $plugin;

    public function __construct(BitcoinAPI $plugin){
        $desc = $plugin->getCommandMessage("paybc");
        parent::__construct("paybc", $desc["description"], $desc["usage"]);

        $this->setPermission("bitcoinapi.command.payBitcoin");

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

        $player = array_shift($params);
        $amount = array_shift($params);

        if(!is_numeric($amount)){
            $sender->sendMessage(TextFormat::RED . "Usage: " . $this->getUsage());
            return true;
        }

        if(($p = $this->plugin->getServer()->getPlayerByPrefix($player)) instanceof Player){
            $player = $p->getName();
        }

        if(!$p instanceof Player and $this->plugin->getConfig()->get("allow-pay-offline", true) === false){
            $sender->sendMessage($this->plugin->getMessage("player-not-connected", [$player], $sender->getName()));
            return true;
        }

        if(!$this->plugin->accountExists($player)){
            $sender->sendMessage($this->plugin->getMessage("player-never-connected", [$player], $sender->getName()));
            return true;
        }
  
        $ev = new PayBitcoinEvent($this->plugin, $sender->getName(), $player, $amount);
        $ev->call();
        $result = BitcoinAPI::RET_CANCELLED;
        if(!$ev->isCancelled()){
            $result = $this->plugin->reduceBitcoin($sender, $amount);
        }

        if($result === BitcoinAPI::RET_SUCCESS){
            $this->plugin->addBitcoin($player, $amount, true);

            $sender->sendMessage($this->plugin->getMessage("pay-success", [$amount, $player], $sender->getName()));
            if($p instanceof Player){
                $p->sendMessage($this->plugin->getMessage("Bitcoin-paid", [$sender->getName(), $amount], $sender->getName()));
            }
        }else{
            $sender->sendMessage($this->plugin->getMessage("pay-failed", [$player, $amount], $sender->getName()));
        }
        return true;
    }
}
