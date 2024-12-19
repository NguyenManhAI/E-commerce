<?php

namespace Bang\Weather\Model;

class WeatherInfo {
    protected $city;
    protected $country;
    protected $icon;
    protected $temp;
    protected $description;
    protected $humidity;
    protected $windSpeed;
    protected $timeStamp;
    protected $maxTemp;
    protected $minTemp;

    public function __construct($city, $country, $icon, $temp, 
                                $description, $humidity, $windSpeed, $timeStamp, 
                                $tempMin, $tempMax) {
        $this->city = $city;
        $this->country = $country;
        $this->icon = $icon;
        $this->temp = $temp;
        $this->description = $description;
        $this->humidity = $humidity;
        $this->windSpeed = $windSpeed;
        $this->timeStamp = $timeStamp;
        $this->maxTemp = $tempMax;
        $this->minTemp = $tempMin;
    }

    // Getter methods
    public function getCity() {
        return $this->city;
    }

    public function getCountry() {
        return $this->country;
    }

    public function getIcon() {
        return $this->icon;
    }

    public function getTemp() {
        return $this->temp;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getHumidity() {
        return $this->humidity;
    }

    public function getWindSpeed() {
        return $this->windSpeed;
    }

    public function getMaxTemp()  {
        if ($this->maxTemp === null) {
            return "max temp not set";
        }
        return $this->maxTemp;
    }

    public function setMaxTemp($maxTemp) {
        $this->maxTemp = $maxTemp;
    }

    public function getMinTemp() {
        if ($this->minTemp === null) {
            return "min temp not set";
        }
        return $this->minTemp;
    }
    
    public function setMinTemp($minTemp) {
        $this->minTempx = $minTemp;
    }

    public function getTimeStamp() {
        return $this->timeStamp;
    }
}
