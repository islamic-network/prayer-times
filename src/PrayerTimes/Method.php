<?php

namespace IslamicNetwork\PrayerTimes;

use IslamicNetwork\PrayerTimes\PrayerTimes;

class Method
{
    /**
     * Name of custom method
     * @var string
     */
    public $name;

    /**
     * Default configuration parameters
     * @var array
     */
    public $params = [];

    /**
     * Constructor
     * @param string $name
     */
    public function __construct($name = 'Custom')
    {
        $this->name = $name;
        // Default Params
        $this->params = [
            PrayerTimes::FAJR => 15,
            PrayerTimes::ISHA => 15
        ];

    }

    /**
     * Set the Fajr Angle
     * @param decimal $angle 18 or 18.5 for degrees
     */
    public function setFajrAngle($angle)
    {
        $this->params[PrayerTimes::FAJR] = $angle;
    }

    /**
     * Set Maghrib angle or minutes after sunset. Example 18 or 18.5 or '20 min'
     * @param string $angleOrMinsAfterSunset
     */
    public function setMaghribAngleOrMins($angleOrMinsAfterSunset)
    {
        $this->params[PrayerTimes::MAGHRIB] = $angleOrMinsAfterSunset;
    }

    /**
     * Set Isha angle or mins after Maghrib. Example 18 or 18.5 or '90 min'
     * @param string $angleOrMinsAfterMaghrib
     */
    public function setIshaAngleOrMins($angleOrMinsAfterMaghrib)
    {
        $this->params[PrayerTimes::ISHA] = $angleOrMinsAfterMaghrib;
    }
}
