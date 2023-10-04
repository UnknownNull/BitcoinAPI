<?php



namespace UnknownNull\BitcoinAPI\event\Bitcoin;

use UnknownNull\BitcoinAPI\BitcoinAPI;
use UnknownNull\BitcoinAPI\event\BitcoinAPIEvent;


class BitcoinChangedEvent extends BitcoinAPIEvent{
	private $username, $Bitcoin;
	public static $handlerList;

	public function __construct(BitcoinAPI $plugin, $username, $Bitcoin, $issuer){
		parent::__construct($plugin, $issuer);
		$this->username = $username;
		$this->Bitcoin = $Bitcoin;
	}

	/**
	 * @return string
	 */
	public function getUsername(){
		return $this->username;
	}

	/**
	 * @return float
	 */
	public function getBitcoin(){
		return $this->Bitcoin;
	}
}
