<?php

/*
 * PointS, the massive point plugin with many features for PocketMine-MP
 * Copyright (C) 2013-2017  UnknownNull <jyc00410@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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
