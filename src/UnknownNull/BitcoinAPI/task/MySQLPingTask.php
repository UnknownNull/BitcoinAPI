<?php



namespace UnknownNull\BitcoinAPI\task;


use UnknownNull\BitcoinAPI\BitcoinAPI;
use pocketmine\scheduler\Task;

class MySQLPingTask extends Task{
    private $mysql;
    
    private $plugin;

    public function __construct(BitcoinAPI $plugin, \mysqli $mysql){
        $this->plugin = $plugin;

        $this->mysql = $mysql;
    }

    public function onRun(): void{
        if(!$this->mysql->ping()){
            $this->plugin->openProvider();
        }
    }
}
