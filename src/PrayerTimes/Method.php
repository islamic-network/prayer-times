<?php

namespace Meezaan\PrayerTimes;

use Meezaan\PrayerTimes\PrayerTimes;

class Method
{
    public $name;

    public $params = [];

    public $offset = [];

    public function __construct($name = 'Custom')
    {
        $this->name = $name;
        // Default Params
        $this->params = [
            PrayerTimes::FAJR => 15,
            PrayerTimes::ISHA => 15
        ];

        $this->offset = [
            PrayerTimes::IMSAK => 0,
            PrayerTimes::FAJR => 0,
            PrayerTimes::SUNRISE => 0,
            PrayerTimes::ZHUHR => 0,
            PrayerTimes::ASR => 0,
            PrayerTimes::MAGHRIB => 0,
            PrayerTimes::SUNSET => 0,
            PrayerTimes::ISHA => 0,
            PrayerTimes::MIDNIGHT => 0
        ];
    }

    public function setFajrAngle($angle)
    {
        $this->params[PrayerTimes::FAJR] = $angle;
    }

    public function setMaghribAngleOrMins($angleOrMinsAfterSunset)
    {
        $this->params[PrayerTimes::MAGHRIB] = $angleOrMinsAfterSunset;
    }

    public function setIshaAngleOrMins($angleOrMinsAfterMaghrib)
    {
        $this->params[PrayerTimes::ISHA] = $angleOrMinsAfterMaghrib;
    }

    public function tuneFajr($mins)
    {
        $this->offset[PrayerTimes::FAJR] = $mins;
    }

    public function tuneZhuhr($mins)
    {
        $this->offset[PrayerTimes::ZHUHR] = $mins;
    }

    public function tuneAsr($mins)
    {
        $this->offset[PrayerTimes::ASR] = $mins;
    }

    public function tuneMaghrib($mins)
    {
        $this->offset[PrayerTimes::MAGHRIB] = $mins;
    }

    public function tuneIsha($mins)
    {
        $this->offset[PrayerTimes::ISHA] = $mins;
    }

    public function tuneSunset($mins)
    {
        $this->offset[PrayerTimes::SUNSET] = $mins;
    }

    public function tuneSunrise($mins)
    {
        $this->offset[PrayerTimes::SUNRISE] = $mins;
    }

    public function tuneImsak($mins)
    {
        $this->offset[PrayerTimes::IMSAK] = $mins;

    }

    public function tuneMidnight($mins)
    {
            $this->offset[PrayerTimes::MIDNIGHT] = $mins;
    }
}
