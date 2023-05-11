<?php

namespace UnknownNull\BitcoinAPI\command;

//use UnknownNull\BitcoinAPI\menu\MainMenu;
use pocketmine\plugin\Plugin;
use UnknownNull\BitcoinAPI\menu\MainMenu;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\player\Player;

use UnknownNull\BitcoinAPI\BitcoinAPI;

class BitcoinCommand extends Command{
    use MainMenu;
    private $plugin;
    private $economyapi;

    public function __construct(BitcoinAPI $plugin){
        $desc = $plugin->getCommandMessage("bitcoin");
        parent::__construct("bitcoin", $desc["description"], $desc["usage"]);

        $this->setPermission("bitcoinapi.command.menu");

        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $label, array $params): bool{
        if(!$this->plugin->isEnabled()) return false;
        if(!$this->testPermission($sender)){
            return false;
        }

        if(!$sender instanceof Player){
            $sender->sendMessage(TextFormat::RED . "Usage: " . $this->getUsage());
            return true;
        }
        $this->Menu($sender);

        return true;
    }
}
