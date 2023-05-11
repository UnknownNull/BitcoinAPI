<?php

namespace UnknownNull\BitcoinAPI\command;

use pocketmine\command\Command;
use pocketmine\Command\CommandSender;
use pocketmine\utils\TextFormat;

use UnknownNull\BitcoinAPI\BitcoinAPI;

class SetLangCommand extends Command{
	private $plugin;

	public function __construct(BitcoinAPI $plugin){
		$desc = $plugin->getCommandMessage("setlangbc");
		parent::__construct("setlangbc", $desc["description"], $desc["usage"]);

		$this->setPermission("bitcoinapi.command.setlangBitcoin");

		$this->plugin = $plugin;
	}

	public function execute(CommandSender $sender, string $label, array $params): bool{
		if(!$this->plugin->isEnabled()) return false;
		if(!$this->testPermission($sender)){
			return false;
		}

		$lang = array_shift($params);
		if(trim($lang) === ""){
			$sender->sendMessage(TextFormat::RED . "Usage: " . $this->getUsage());
			return true;
		}

		if($this->plugin->setPlayerLanguage($sender->getName(), $lang)){
			$sender->sendMessage($this->plugin->getMessage("language-set", [$lang], $sender->getName()));
		}else{
			$sender->sendMessage(TextFormat::RED . "There is no language such as $lang");
		}
		return true;
	}
}
