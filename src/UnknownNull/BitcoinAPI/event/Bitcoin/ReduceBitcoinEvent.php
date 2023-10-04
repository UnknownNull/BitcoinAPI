<?php

 

namespace UnknownNull\BitcoinAPI\event\Bitcoin;

use UnknownNull\BitcoinAPI\event\BitcoinAPIEvent;
use UnknownNull\BitcoinAPI\BitcoinAPI;

class ReduceBitcoinEvent extends BitcoinAPIEvent{
	private $username, $amount;
	public static $handlerList;
	
	public function __construct(BitcoinAPI $plugin, $username, $amount, $issuer){
		parent::__construct($plugin, $issuer);
		$this->username = $username;
		$this->amount = $amount;
	}
	
	public function getUsername(){
		return $this->username;
	}
	
	public function getAmount(){
		return $this->amount;
	}
}
