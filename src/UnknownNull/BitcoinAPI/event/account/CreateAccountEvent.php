<?php

 

namespace UnknownNull\BitcoinAPI\event\account;

use UnknownNull\BitcoinAPI\event\BitcoinAPIEvent;
use UnknownNull\BitcoinAPI\BitcoinAPI;

class CreateAccountEvent extends BitcoinAPIEvent{
	private $username, $defaultCoin;
	public static $handlerList;
	
	public function __construct(BitcoinAPI $plugin, $username, $defaultCoin, $issuer){
		parent::__construct($plugin, $issuer);
		$this->username = $username;
		$this->defaultCoin = $defaultCoin;
	}
	
	public function getUsername(){
		return $this->username;
	}
	
	public function setDefaultCoin($Bitcoin){
		$this->defaultCoin = $Bitcoin;
	}
	
	public function getDefaultCoin(){
		return $this->defaultCoin;
	}
}
