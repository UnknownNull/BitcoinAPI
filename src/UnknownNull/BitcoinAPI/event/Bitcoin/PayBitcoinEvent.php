<?php

 

namespace UnknownNull\BitcoinAPI\event\Bitcoin;

use UnknownNull\BitcoinAPI\event\BitcoinAPIEvent;
use UnknownNull\BitcoinAPI\BitcoinAPI;

class PayBitcoinEvent extends BitcoinAPIEvent{
	private $payer, $target, $amount;
	public static $handlerList;
	
	public function __construct(BitcoinAPI $plugin, $payer, $target, $amount){
		parent::__construct($plugin, "PaybcCommand");
		
		$this->payer = $payer;
		$this->target = $target;
		$this->amount = $amount;
	}
	
	public function getPayer(){
		return $this->payer;
	}
	
	public function getTarget(){
		return $this->target;
	}
	
	public function getAmount(){
		return $this->amount;
	}
}
