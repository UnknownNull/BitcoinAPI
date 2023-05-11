<?php

namespace UnknownNull\BitcoinAPI\menu;


use pocketmine\player\Player;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;


trait MainMenu
{
//    public $economyapi;

    //main
    public function Menu(Player $player)
    {
        $form = new SimpleForm(function (Player $player, $data) {
            if ($data === null) {
                return false;
            }
            switch ($data) {

                case 0:
                    $this->shop($player);
                    break;

                case 1:
//                    $this->sell($player);
                    break;

                case 2:
                    $this->my($player);
                    break;

            }
            return true;
        });

        $bitcoin = $this->plugin->getBitcoiPrice();
        $form->setTitle("§l§6BitCoin");
        $form->setContent("slm");
        $form->setContent("Gheymate Lahzei: §2+{$bitcoin}");
        $form->addButton("Shop");
        $form->addButton("Sell");
        $form->addButton("my bitcoin");
        $form->sendToPlayer($player);
    }

    public function shop(Player $player)
    {
        $bitcoin = $this->plugin->getBitcoiPrice();

        $form = new CustomForm(function (Player $player, $data) {
            if ($data === null) {
                return false;
            }
            $bitcoin = $this->plugin->getBitcoiPrice();
            $money = $this->plugin->economyapi->myMoney($player);
            $Total = $bitcoin * $data[0];
            if ($money >= $Total) {
                $this->plugin->economyapi->reduceMoney($player, $Total);
                $this->plugin->addBitcoin($player, $data[0]);
                $player->sendMessage("Successful purchase");

            } else {
                $player->sendMessage("You don't have enough money");
            }
            return true;
        });

        $form->setTitle("§l§6BitCoin");
        $form->addLabel("Current price: §2+{$bitcoin}");
        $form->addSlider("Number", 1, 10, 1, 1);
        $form->sendToPlayer($player);
    }

    public function Sell(Player $player)
    {
        $bitcoin = $this->plugin->getBitcoiPrice();

        $form = new CustomForm(function (Player $player, $data) {
            if ($data === null) {
                return false;
            }
            $bitcoin = $this->plugin->getBitcoiPrice();
            $money = $this->plugin->economyapi->myMoney($player);
            $mybitcoin = $this->plugin->myBitcoin($player);
            $Total = $bitcoin * $data[0];
            if ($mybitcoin >= $data[0]) {
                $this->plugin->reduceBitcoin($player, $data[0]);
                $this->plugin->economyapi->addMoney($player, $Total);
                $player->sendMessage("Successful sale");
            }
            return true;
        });

        $form->setTitle("§l§6BitCoin");
        $form->addLabel("Current price: §2+{$bitcoin}");
        $form->addSlider("Number", 1, 10, 1, 1);
        $form->sendToPlayer($player);
    }

    public function my(Player $player)
    {
        $mybitcoin = $this->plugin->myBitcoin($player);

        $form = new SimpleForm(function (Player $player, $data) {
            if ($data === null) {
                return false;
            }

            return true;
        });

        $form->setTitle("§l§6BitCoin");
        $form->setContent("My Bitcoins: " . $mybitcoin);
        $form->addButton("Close");
        $form->sendToPlayer($player);
    }
}