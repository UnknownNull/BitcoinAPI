<?php

/*
 * PointS, the massive point plugin with many features for PocketMine-MP
 * Copyright (C) 2013-2017  UnknownNull <jyc00410@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace UnknownNull\BitcoinAPI\provider;


use UnknownNull\BitcoinAPI\BitcoinAPI;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class YamlProvider implements Provider{
    /**
     * @var Config
     */
    private $config;

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
