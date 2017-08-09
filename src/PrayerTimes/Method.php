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

    /**
     * [tuneFajr description]
     * @param  [type] $mins [description]
     * @return [type]       [description]
     */
    public function tuneFajr($mins)
    {
        $this->offset[PrayerTimes::FAJR] = $mins;
    }

    /**
     * [tuneZhuhr description]
     * @param  [type] $mins [description]
     * @return [type]       [description]
     */
    public function tuneZhuhr($mins)
    {
        $this->offset[PrayerTimes::ZHUHR] = $mins;
    }

    /**
     * [tuneAsr description]
     * @param  [type] $mins [description]
     * @return [type]       [description]
     */
    public function tuneAsr($mins)
    {
        $this->offset[PrayerTimes::ASR] = $mins;
    }

    /**
     * [tuneMaghrib description]
     * @param  [type] $mins [description]
     * @return [type]       [description]
     */
    public function tuneMaghrib($mins)
    {
        $this->offset[PrayerTimes::MAGHRIB] = $mins;
    }

    /**
     * [tuneIsha description]
     * @param  [type] $mins [description]
     * @return [type]       [description]
     */
    public function tuneIsha($mins)
    {
        $this->offset[PrayerTimes::ISHA] = $mins;
    }

    /**
     * [tuneSunset description]
     * @param [type] $mins [description]
     */
    public function tuneSunset($mins)
    {
        $this->offset[PrayerTimes::SUNSET] = $mins;
    }

    /**
     * [tuneSunrise description]
     * @param  [type] $mins [description]
     * @return [type]       [description]
     */
    public function tuneSunrise($mins)
    {
        $this->offset[PrayerTimes::SUNRISE] = $mins;
    }

    /**
     * [tuneImsak description]
     * @param  [type] $mins [description]
     * @return [type]       [description]
     */
    public function tuneImsak($mins)
    {
        $this->offset[PrayerTimes::IMSAK] = $mins;

    }

    /**
     * [tuneMidnight description]
     * @param  [type] $mins [description]
     * @return [type]       [description]
     */
    public function tuneMidnight($mins)
    {
            $this->offset[PrayerTimes::MIDNIGHT] = $mins;
    }
}
