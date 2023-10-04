<?php


namespace UnknownNull\BitcoinAPI\provider;


use UnknownNull\BitcoinAPI\BitcoinAPI;
use UnknownNull\BitcoinAPI\task\MySQLPingTask;

use pocketmine\player\Player;

class MySQLProvider implements Provider{
    /**
     * @var \mysqli
     */
    private $db;

    /** @var BitcoinAPI */
    private $plugin;

    public function __construct(BitcoinAPI $plugin){
        $this->plugin = $plugin;
    }

    public function open(){
        $config = $this->plugin->getConfig()->get("provider-settings", []);

        $this->db = new \mysqli(
            $config["host"] ?? "127.0.0.1",
            $config["user"] ?? "UnknownNull",
            $config["password"] ?? "hello_world",
            $config["db"] ?? "BitcoinAPI",
            $config["port"] ?? 3306);
        if($this->db->connect_error){
            $this->plugin->getLogger()->critical("Could not connect to MySQL server: ".$this->db->connect_error);
            return;
        }
        if(!$this->db->query("CREATE TABLE IF NOT EXISTS user_coin(
            username VARCHAR(20) PRIMARY KEY,
            Bitcoin FLOAT
        );")){
            $this->plugin->getLogger()->critical("Error creating table: " . $this->db->error);
            return;
        }

        $this->plugin->getScheduler()->scheduleRepeatingTask(new MySQLPingTask($this->plugin, $this->db), 600);
    }

    /**
     * @param \pocketmine\Player|string $player
     * @return bool
     */
    public function accountExists($player){
        if($player instanceof Player){
            $player = $player->getName();
        }
        $player = strtolower($player);

        $result = $this->db->query("SELECT * FROM user_coin WHERE username='".$this->db->real_escape_string($player)."'");
        return $result->num_rows > 0 ? true:false;
    }

    /**
     * @param \pocketmine\Player|string $player
     * @param float $defaultPoint
     * @return bool
     */
    public function createAccount($player, $defaultCoin = 1000.0){
        if($player instanceof Player){
            $player = $player->getName();
        }
        $player = strtolower($player);

        if(!$this->accountExists($player)){
            $this->db->query("INSERT INTO user_coin (username, Bitcoin) VALUES ('".$this->db->real_escape_string($player)."', $defaultCoin);");
            return true;
        }
        return false;
    }

    /**
     * @param \pocketmine\Player|string $player
     * @return bool
     */
    public function removeAccount($player){
        if($player instanceof Player){
            $player = $player->getName();
        }
        $player = strtolower($player);

        if($this->db->query("DELETE FROM user_coin WHERE username='".$this->db->real_escape_string($player)."'") === true) return true;
        return false;
    }

    /**
     * @param string $player
     * @return float|bool
     */
    public function getBitcoin($player){
        if($player instanceof Player){
            $player = $player->getName();
        }
        $player = strtolower($player);

        $res = $this->db->query("SELECT Bitcoin FROM user_coin WHERE username='".$this->db->real_escape_string($player)."'");
        $ret = $res->fetch_array()[0] ?? false;
        $res->free();
        return $ret;
    }

    /**
     * @param \pocketmine\Player|string $player
     * @param float $amount
     * @return bool
     */
    public function getBitcoin($player, $amount){
        if($player instanceof Player){
            $player = $player->getName();
        }
        $player = strtolower($player);

        $amount = (float) $amount;

        return $this->db->query("UPDATE user_coin SET Bitcoin = $amount WHERE username='".$this->db->real_escape_string($player)."'");
    }

    /**
     * @param \pocketmine\Player|string $player
     * @param float $amount
     * @return bool
     */
    public function addBitcoin($player, $amount){
        if($player instanceof Player){
            $player = $player->getName();
        }
        $player = strtolower($player);

        $amount = (float) $amount;

        return $this->db->query("UPDATE user_coin SET Bitcoin = Bitcoin + $amount WHERE username='".$this->db->real_escape_string($player)."'");
    }

    /**
     * @param \pocketmine\Player|string $player
     * @param float $amount
     * @return bool
     */
    public function reduceBitcoin($player, $amount){
        if($player instanceof Player){
            $player = $player->getName();
        }
        $player = strtolower($player);

        $amount = (float) $amount;

        return $this->db->query("UPDATE user_coin SET Bitcoin = Bitcoin - $amount WHERE username='".$this->db->real_escape_string($player)."'");
    }

    /**
     * @return array
     */
    public function getAll(){
        $res = $this->db->query("SELECT * FROM user_coin");

        $ret = [];
        foreach($res->fetch_all() as $val){
            $ret[$val[0]] = $val[1];
        }

        $res->free();

        return $ret;
    }

    /**
     * @return string
     */
    public function getName(){
        return "MySQL";
    }

    public function save(){}

    public function close(){
        if($this->db instanceof \mysqli){
            $this->db->close();
        }
    }
}
