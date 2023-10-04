<?php

 

namespace UnknownNull\BitcoinAPI\event\Bitcoin;

use UnknownNull\BitcoinAPI\event\BitcoinAPIEvent;

use UnknownNull\BitcoinAPI\BitcoinAPI;

class SetBitcoinEvent extends BitcoinAPIEvent{
	private $username, $Bitcoin;
	public static $handlerList;
	
	public function __construct(BitcoinAPI $plugin, $username, $Bitcoin, $issuer){
		parent::__construct($plugin, $issuer);
		$this->username = $username;
		$this->Bitcoin = $Bitcoin;
	}
	
	public function getUsername(){
		return $this->username;
	}
	
	public function getBitcoin(){
		return $this->Bitcoin;
	}
}
