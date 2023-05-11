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

namespace UnknownNull\BitcoinAPI;

use UnknownNull\BitcoinAPI\menu\MainMenu;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\utils\Utils;
use pocketmine\utils\TextFormat;
use UnknownNull\BitcoinAPI\provider\Provider;
use UnknownNull\BitcoinAPI\provider\YamlProvider;
use UnknownNull\BitcoinAPI\provider\MySQLProvider;
use UnknownNull\BitcoinAPI\event\Bitcoin\SetBitcoinEvent;
use UnknownNull\BitcoinAPI\event\Bitcoin\ReduceBitcoinEvent;
use UnknownNull\BitcoinAPI\event\Bitcoin\AddBitcoinEvent;
use UnknownNull\BitcoinAPI\event\Bitcoin\BitcoinChangedEvent;
use UnknownNull\BitcoinAPI\event\account\CreateAccountEvent;
use UnknownNull\BitcoinAPI\task\SaveTask;

class BitcoinAPI extends PluginBase implements Listener{
//    use menu\MainMenu;

    const API_VERSION = 4;
    const PACKAGE_VERSION = "1.0.0";

    const RET_NO_ACCOUNT = -3;
    const RET_CANCELLED = -2;
    const RET_NOT_FOUND = -1;
    const RET_INVALID = 0;
    const RET_SUCCESS = 1;

    private static $instance = null;

    /** @var Provider */
    private $provider;

    private $langList = [
        "eng" => "English"
    ];
    private $lang = [], $playerLang = [];


    private Config $BitcoinPrice;
    public $economyapi;
    private BitcoinAPI $plugin;

    /**
     * @param string            $command
     * @param bool|string $lang
     *
     * @return array
     */
    public function getCommandMessage(string $command, bool|string $lang = false) : array{
//        if($lang === false){
//        }
        $command = strtolower($command);
        return $this->lang["vie"]["commands"][$command];
    }

    /**
     * @param string        $key
     * @param array         $params
     * @param string        $player
     *
     * @return string
     */
    public function getMessage(string $key, array $params = [], string $player = "console") : string{
        $player = strtolower($player);
        if(isset($this->lang[$this->playerLang[$player]][$key])){
            return $this->replaceParameters($this->lang[$this->playerLang[$player]][$key], $params);
        }elseif(isset($this->lang["vie"][$key])){
            return $this->replaceParameters($this->lang["vie"][$key], $params);
        }
        return "Language matching key \"$key\" does not exist.";
    }

    public function setPlayerLanguage(string $player, string $language) : bool{
        $player = strtolower($player);
        $language = strtolower($language);
        if(isset($this->lang[$language])){
            $this->playerLang[$player] = $language;
            return true;
        }
        return false;
    }

    public function getMonetaryUnit() : string{
        return $this->getConfig()->get("monetary-unit");
    }

    /**
     * @return array
     */
    public function getAllCoin() : array{
        return $this->provider->getAll();
    }

    /**
     * @param string|Player $player
     * @param bool $defaultCoin
     * @param bool $force
     *
     * @return bool
     */
    public function createAccount($player, $defaultCoin = false, bool $force = false) : bool{
        if($player instanceof Player){
            $player = $player->getName();
        }
        $player = strtolower($player);

        if(!$this->provider->accountExists($player)){
            $defaultCoin = ($defaultCoin === false) ? $this->getConfig()->get("default-Bitcoin") : $defaultCoin;
           $ev = $ev = new CreateAccountEvent($this, $player, $defaultCoin, "none");
           $ev->call();
            if(!$ev->isCancelled() or $force === true){
                $this->provider->createAccount($player, $ev->getDefaultCoin());
            }
        }
        return false;
    }

    /**
     * @param string|Player            $player
     *
     * @return bool
     */
    public function accountExists($player) : bool{
        return $this->provider->accountExists($player);
    }

    /**
     * @param Player|string        $player
     *
     * @return float|bool
     */
    public function myBitcoin($player){
        return $this->provider->getBitcoin($player);
    }

    /**
     * @param string|Player     $player
     * @param float             $amount
     * @param bool                $force
     * @param string            $issuer
     *
     * @return int
     */
    public function getBitcoin($player, $amount, bool $force = false, string $issuer = "none") : int{
        if($amount < 0){
            return self::RET_INVALID;
        }

        if($player instanceof Player){
            $player = $player->getName();
        }
        $player = strtolower($player);
        if($this->provider->accountExists($player)){
            $amount = round($amount, 2);
            if($amount > $this->getConfig()->get("max-Bitcoin")){
                return self::RET_INVALID;
            }
  
            $ev = new SetBitcoinEvent($this, $player, $amount, $issuer);
            $ev->call();
            if(!$ev->isCancelled() or $force === true){
                $this->provider->getBitcoin($player, $amount);
                $ev2 = new BitcoinChangedEvent($this, $player, $amount, $issuer);
                $ev2->call();
                return self::RET_SUCCESS;
            }
            return self::RET_CANCELLED;
        }
        return self::RET_NO_ACCOUNT;
    }

    /**
     * @param string|Player     $player
     * @param float             $amount
     * @param bool                $force
     * @param string            $issuer
     *
     * @return int
     */
    public function addBitcoin($player, $amount, bool $force = false, $issuer = "none") : int{
        if($amount < 0){
            return self::RET_INVALID;
        }
        if($player instanceof Player){
            $player = $player->getName();
        }
        $player = strtolower($player);
        if(($Bitcoin = $this->provider->getBitcoin($player)) !== false){
            $amount = round($amount, 2);
            if($Bitcoin + $amount > $this->getConfig()->get("max-Bitcoin")){
                return self::RET_INVALID;
            }
           
            $ev = new AddBitcoinEvent($this, $player, $amount, $issuer);
            $ev->call();
            if(!$ev->isCancelled() or $force === true){
                $this->provider->addBitcoin($player, $amount);
                $ev2 = new BitcoinChangedEvent($this, $player, $amount + $Bitcoin, $issuer);
                $ev2->call();
                return self::RET_SUCCESS;
            }
            return self::RET_CANCELLED;
        }
        return self::RET_NO_ACCOUNT;
    }

    /**
     * @param string|Player     $player
     * @param float             $amount
     * @param bool                $force
     * @param string            $issuer
     *
     * @return int
     */
    public function reduceBitcoin($player, $amount, bool $force = false, $issuer = "none") : int{
        if($amount < 0){
            return self::RET_INVALID;
        }
        if($player instanceof Player){
            $player = $player->getName();
        }
        $player = strtolower($player);
        if(($Bitcoin = $this->provider->getBitcoin($player)) !== false){
            $amount = round($amount, 2);
            if($Bitcoin - $amount < 0){
                return self::RET_INVALID;
            }
           
            $ev = new ReduceBitcoinEvent($this, $player, $amount, $issuer);
            $ev->call();
             if(!$ev->isCancelled() or $force === true){
                $this->provider->reduceBitcoin($player, $amount);
                $ev2 = new BitcoinChangedEvent($this, $player, $Bitcoin - $amount, $issuer);
               $ev2->call();
              return self::RET_SUCCESS;
            }
            return self::RET_CANCELLED;
        }
        return self::RET_NO_ACCOUNT;
    }

    /**
     * @return BitcoinAPI
     */
    public static function getInstance(){
        return self::$instance;
    }

    public function onLoad() : void{
        self::$instance = $this;
    }

    public function onEnable() : void{

        $this->saveDefaultConfig();

        if(!is_file($this->getDataFolder()."PlayerLang.dat")){
            file_put_contents($this->getDataFolder()."PlayerLang.dat", serialize([]));
        }
        $this->playerLang = unserialize(file_get_contents($this->getDataFolder()."PlayerLang.dat"));

        if(!isset($this->playerLang["console"])){
            $this->playerLang["console"] = $this->getConfig()->get("default-lang");
        }
        if(!isset($this->playerLang["rcon"])){
            $this->playerLang["rcon"] = $this->getConfig()->get("default-lang");
        }
        $this->initialize();

        if($this->getConfig()->get("auto-save-interval") > 0){
            $this->getScheduler()->scheduleDelayedRepeatingTask(new SaveTask($this), $this->getConfig()->get("auto-save-interval") * 1200, $this->getConfig()->get("auto-save-interval") * 1200);
        }

        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        $this->economyapi = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");

        $this->getScheduler()->scheduleRepeatingTask(new task\BitcoinTask($this), 20 * 120);

        $this->saveResource("BitcoinPrice.yml");
        $this->BitcoinPrice = new Config($this->getDataFolder() . "BitcoinPrice.yml", Config::YAML);
    }

    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();

        if(!isset($this->playerLang[strtolower($player->getName())])){
            $this->playerLang[strtolower($player->getName())] = $this->getConfig()->get("default-lang");
        }
        if(!$this->provider->accountExists($player)){
            $this->getLogger()->debug("Account of '".$player->getName()."' is not found. Creating account...");
            $this->createAccount($player, false, true);
        }
    }

    public function onDisable() : void{
        $this->saveAll();

        if($this->provider instanceof Provider){
            $this->provider->close();
        }
    }

    public function saveAll(){
        if($this->provider instanceof Provider){
            $this->provider->save();
        }
        file_put_contents($this->getDataFolder()."PlayerLang.dat", serialize($this->playerLang));
    }

    private function replaceParameters($message, $params = []){
        $search = ["%MONETARY_UNIT%"];
        $replace = [$this->getMonetaryUnit()];

        for($i = 0; $i < count($params); $i++){
            $search[] = "%".($i + 1);
            $replace[] = $params[$i];
        }

        $colors = [
            "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f", "k", "l", "m", "n", "o", "r"
        ];
        foreach($colors as $code){
            $search[] = "&".$code;
            $replace[] = TextFormat::ESCAPE.$code;
        }

        return str_replace($search, $replace, $message);
    }

    private function initialize(){
        switch(strtolower($this->getConfig()->get("provider"))){
            case "yaml":
            $this->provider = new YamlProvider($this);
            break;
            case "mysql":
            $this->provider = new MySQLProvider($this);
            break;
            default:
            $this->getLogger()->critical("Invalid database was given.");
            return false;
        }
        $this->provider->open();

        $this->initializeLanguage();
        $this->getLogger()->notice("Database provider was set to: ".$this->provider->getName());
        $this->registerCommands();
        // aref
        return true;
    }

    public function openProvider(){
        if($this->provider !== null)
            $this->provider->open();
    }

    private function registerCommands(){
        $map = $this->getServer()->getCommandMap();

        $commands = [
            "mybc" => "\\UnknownNull\\BitcoinAPI\\command\\MyBitcoinCommand",
            "topbc" => "\\UnknownNull\\BitcoinAPI\\command\\TopBitcoinCommand",
            "setbc" => "\\UnknownNull\\BitcoinAPI\\command\\SetBitcoinCommand",
            "seebc" => "\\UnknownNull\\BitcoinAPI\\command\\SeeBitcoinCommand",
            "givebc" => "\\UnknownNull\\BitcoinAPI\\command\\GiveBitcoinCommand",
            "takebc" => "\\UnknownNull\\BitcoinAPI\\command\\TakeBitcoinCommand",
            "paybc" => "\\UnknownNull\\BitcoinAPI\\command\\PayBitcoinCommand",
            "setlangbc" => "\\UnknownNull\\BitcoinAPI\\command\\SetLangCommand",
            "mystatusbc" => "\\UnknownNull\\BitcoinAPI\\command\\MyStatusBitcoinCommand",
            "bitcoin" => "\\UnknownNull\\BitcoinAPI\\command\\BitcoinCommand"
        ];
        foreach($commands as $cmd => $class){
            $map->register("bitcoinapi", new $class($this));
        }
    }

    private function initializeLanguage(){
        foreach($this->getResources() as $resource){
            if($resource->isFile() and substr(($filename = $resource->getFilename()), 0, 5) === "lang_"){
                $this->lang[substr($filename, 5, -5)] = json_decode(file_get_contents($resource->getPathname()), true);
            }
        }
        $this->lang["user-define"] = (new Config($this->getDataFolder()."messages.yml", Config::YAML, $this->lang["vie"]))->getAll();
    }

    /**
     * @throws \JsonException
     */
    public function setBitcoinPrice()
    {
        $this->BitcoinPrice->setNested("Bitcoin" . ".Price", rand(100, 100000));
        $this->BitcoinPrice->setNested("Bitcoin" . ".LastPrice", "soon...");
        $this->BitcoinPrice->save();
    }
    public function getBitcoiPrice(){
       return $this->BitcoinPrice->getNested("Bitcoin" . ".Price");
    }
}
