<?php

 

namespace UnknownNull\BitcoinAPI\provider;

use pocketmine\player\Player;
use UnknownNull\BitcoinAPI\BitcoinAPI;

interface Provider{
	public function __construct(BitcoinAPI $plugin);

	public function open();

	/**
	 * @param Player|string $player
	 * @return bool
	 */
	public function accountExists($player);

	/**
	 * @param Player|string $player
	 * @param float $defaultPoint
	 * @return bool
	 */
	public function createAccount($player, $defaultCoin = 1000);

	/**
	 * @param Player|string $player
	 * @return bool
	 */
	public function removeAccount($player);

	/**
	 * @param string $player
	 * @return float|bool
	 */
	public function getBitcoin($player);

	/**
	 * @param Player|string $player
	 * @param float $amount
	 * @return bool
	 */
	public function setBitcoin($player, $amount);
	/**
	 * @param Player|string $player
	 * @param float $amount
	 * @return bool
	 */
	public function addBitcoin($player, $amount);

	/**
	 * @param Player|string $player
	 * @param float $amount
	 * @return bool
	 */
	public function reduceBitcoin($player, $amount);

	/**
	 * @return array
	 */
	public function getAll();

	/**
	 * @return string
	 */
	public function getName();

	public function save();
	public function close();
}
