<?php

namespace Bang\Currency\Model;

class CurrencyRate {
    protected $code;
    protected $name;
    protected $buy;
    protected $transfer;
    protected $sell;

    public function __construct($code, $name, $buy, $transfer, $sell) {
        $this->code = $code;
        $this->name = $name;
        $this->buy = $buy;
        $this->transfer = $transfer;
        $this->sell = $sell;
    }

    public function __tostring() {
        return "Exrate: " . $this->code ."\t". $this->name ."\t". $this->buy . "\t". $this->transfer ."\t". $this->sell;
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getBuy() {
        return $this->buy;
    }

    public function setBuy($buy) {
        $this->buy = $buy;
    }

    public function getTransfer() {
        return $this->transfer;
    }

    public function setTransfer($transfer) {
        $this->transfer = $transfer;
    }

    public function getSell() {
        return $this->sell;
    }

    public function setSell($sell) {
        $this->sell = $sell;
    }
}