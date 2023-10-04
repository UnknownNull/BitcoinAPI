<?php

 

namespace UnknownNull\BitcoinAPI\provider;


use UnknownNull\BitcoinAPI\BitcoinAPI;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class YamlProvider implements Provider{
    /**
     * @var Config
     */
    private $config;
    private $Bitcoin;

    /** @var BitcoinAPI */
    private $plugin;

    private $coin = [];

    public function __construct(BitcoinAPI $plugin){
        $this->plugin = $plugin;
    }

    public function open(){
        $this->config = new Config($this->plugin->getDataFolder() . "Bitcoin.yml", Config::YAML, ["version" => 2, "Bitcoin" => []]);
        $this->Bitcoin = $this->config->getAll();
    }

    public function accountExists($player){
        if($player instanceof Player){
            $player = $player->getName();
        }
        $player = strtolower($player);

        return isset($this->Bitcoin["Bitcoin"][$player]);
    }

    public function createAccount($player, $defaultCoin = 1000){
        if($player instanceof Player){
            $player = $player->getName();
        }
        $player = strtolower($player);

        if(!isset($this->Bitcoin["Bitcoin"][$player])){
            $this->Bitcoin["Bitcoin"][$player] = $defaultCoin;
            return true;
        }
        return false;
    }

    public function removeAccount($player){
        if($player instanceof Player){
            $player = $player->getName();
        }
        $player = strtolower($player);

        if(isset($this->Bitcoin["Bitcoin"][$player])){
            unset($this->Bitcoin["Bitcoin"][$player]);
            return true;
        }
        return false;
    }

    public function getBitcoin($player){
        if($player instanceof Player){
            $player = $player->getName();
        }
        $player = strtolower($player);

        if(isset($this->Bitcoin["Bitcoin"][$player])){
            return $this->Bitcoin["Bitcoin"][$player];
        }
        return false;
    }

    public function setBitcoin($player, $amount){
        if($player instanceof Player){
            $player = $player->getName();
        }
        $player = strtolower($player);

        if(isset($this->Bitcoin["Bitcoin"][$player])){
            $this->Bitcoin["Bitcoin"][$player] = $amount;
            $this->Bitcoin["Bitcoin"][$player] = round($this->Bitcoin["Bitcoin"][$player], 2);
            return true;
        }
        return false;
    }

    public function addBitcoin($player, $amount){
        if($player instanceof Player){
            $player = $player->getName();
        }
        $player = strtolower($player);

        if(isset($this->Bitcoin["Bitcoin"][$player])){
            $this->Bitcoin["Bitcoin"][$player] += $amount;
            $this->Bitcoin["Bitcoin"][$player] = round($this->Bitcoin["Bitcoin"][$player], 2);
            return true;
        }
        return false;
    }

    public function reduceBitcoin($player, $amount){
        if($player instanceof Player){
            $player = $player->getName();
        }
        $player = strtolower($player);

        if(isset($this->Bitcoin["Bitcoin"][$player])){
            $this->Bitcoin["Bitcoin"][$player] -= $amount;
            $this->Bitcoin["Bitcoin"][$player] = round($this->Bitcoin["Bitcoin"][$player], 2);
            return true;
        }
        return false;
    }

    public function getAll(){
        return isset($this->Bitcoin["Bitcoin"]) ? $this->Bitcoin["Bitcoin"] : [];
    }

    public function save(){
        $this->config->setAll($this->Bitcoin);
        $this->config->save();
    }

    public function close(){
        $this->save();
    }

    public function getName(){
        return "Yaml";
    }
}
