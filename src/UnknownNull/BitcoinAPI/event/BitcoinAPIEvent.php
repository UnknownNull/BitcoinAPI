<?php

 

namespace UnknownNull\BitcoinAPI\event;

use pocketmine\event\plugin\PluginEvent;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

use UnknownNull\BitcoinAPI\BitcoinAPI;

class BitcoinAPIEvent extends PluginEvent implements Cancellable{
    use CancellableTrait;
    private $issuer;
    
    public function __construct(BitcoinAPI $plugin, $issuer){
        parent::__construct($plugin);
        $this->issuer = $issuer;
    }
    
    public function getIssuer(){
        return $this->issuer;
    }
}
