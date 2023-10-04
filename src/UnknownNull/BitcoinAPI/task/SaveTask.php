<?php

 

namespace UnknownNull\BitcoinAPI\task;

use UnknownNull\BitcoinAPI\BitcoinAPI;

use pocketmine\scheduler\Task;

class SaveTask extends Task {
    private $plugin;
    public function __construct(BitcoinAPI $plugin){
        $this->plugin = $plugin;
    }

    public function onRun() : void{
        $this->plugin->saveAll();
    }
}
