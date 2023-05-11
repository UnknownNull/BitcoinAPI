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
