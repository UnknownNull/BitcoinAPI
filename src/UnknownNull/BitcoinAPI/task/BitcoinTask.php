<?php

namespace UnknownNull\BitcoinAPI\task;

use UnknownNull\BitcoinAPI\BitcoinAPI;

class BitcoinTask extends \pocketmine\scheduler\Task
{
    public BitcoinAPI $BitcoinAPI;

    /**
     * @param BitcoinAPI $param
     */
    public function __construct(BitcoinAPI $bitcoinAPI)
    {
        $this->BitcoinAPI = $bitcoinAPI;
    }

    /**
     * @throws \JsonException
     */
    public function onRun(): void
    {
        $this->BitcoinAPI->setBitcoinPrice();
        // TODO: Implement onRun() method.
    }
}