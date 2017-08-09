<?php

namespace Meezaan\PrayerTimes;

use Meezaan\PrayerTimes\PrayerTimes;

class Method
{
    /**
     * [$name description]
     * @var [type]
     */
    public $name;

    /**
     * [$params description]
     * @var array
     */
    public $params = [];

    /**
     * [$offset description]
     * @var array
     */
    public $offset = [];

    /**
     * [__construct description]
     * @param string $name [description]
     */
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

    /**
     * [setFajrAngle description]
     * @param [type] $angle [description]
     */
    public function setFajrAngle($angle)
    {
        $this->params[PrayerTimes::FAJR] = $angle;
    }

    /**
     * [setMaghribAngleOrMins description]
     * @param [type] $angleOrMinsAfterSunset [description]
     */
    public function setMaghribAngleOrMins($angleOrMinsAfterSunset)
    {
        $this->params[PrayerTimes::MAGHRIB] = $angleOrMinsAfterSunset;
    }

    /**
     * [setIshaAngleOrMins description]
     * @param [type] $angleOrMinsAfterMaghrib [description]
     */
    public function setIshaAngleOrMins($angleOrMinsAfterMaghrib)
    {
        $this->params[PrayerTimes::ISHA] = $angleOrMinsAfterMaghrib;
    }
}
