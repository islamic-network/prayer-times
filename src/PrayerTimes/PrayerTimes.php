<?php
/**
 * PrayTimes.js: Prayer Times Calculator (ver 2.3)
 * Copyright (C) 2007-2011 PrayTimes.org
 * Copyright (C) 2015-2017 AlAdhan.com

 * Developed in JavaScript by Hamid Zarrabi-Zadeh
 * Ported to PHP by Meezaan-ud-Din Abdu Dhil-Jalali Wal-Ikram
 * License: GNU LGPL v3.0
 */

namespace IslamicNetwork\PrayerTimes;

use DateTime;
use DateTimezone;
use IslamicNetwork\MoonSighting\Fajr;
use IslamicNetwork\MoonSighting\Isha;

/**
 * Class PrayerTimes
 * @package IslamicNetwork\PrayerTimes
 */
class PrayerTimes
{
    /**
     * Constants for all items the times are computed for
     */
    const IMSAK = 'Imsak';
    const FAJR = 'Fajr';
    const SUNRISE = 'Sunrise';
    const ZHUHR = 'Dhuhr';
    const ASR = 'Asr';
    const SUNSET = 'Sunset';
    const MAGHRIB = 'Maghrib';
    const ISHA = 'Isha';
    const MIDNIGHT = 'Midnight';

    /**
     * Schools that determine the Asr shadow for the purpose of this class
     */
    const SCHOOL_STANDARD = 'STANDARD'; //0
    const SCHOOL_HANAFI = 'HANAFI'; // 1

    /**
     * Midnight Mode - how the midnight time is determined
     */
    const MIDNIGHT_MODE_STANDARD = 'STANDARD'; // Mid Sunset to Sunrise
    const MIDNIGHT_MODE_JAFARI = 'JAFARI'; // Mid Sunset to Fajr

    /**
     * Higher Latitude Adjustment Methods
     */
    const LATITUDE_ADJUSTMENT_METHOD_MOTN = 'MIDDLE_OF_THE_NIGHT'; // 1
    const LATITUDE_ADJUSTMENT_METHOD_ANGLE = 'ANGLE_BASED'; // 3, angle/60th of night
    const LATITUDE_ADJUSTMENT_METHOD_ONESEVENTH = 'ONE_SEVENTH'; // 2
    const LATITUDE_ADJUSTMENT_METHOD_NONE = 'NONE'; // 0

    /**
     * Formats in which data can be output
     */
    const TIME_FORMAT_24H = '24h'; // 24-hour format
    const TIME_FORMAT_12H = '12h'; // 12-hour format
    const TIME_FORMAT_12hNS = '12hNS'; // 12-hour format with no suffix
    const TIME_FORMAT_FLOAT = 'Float'; // floating point number
    const TIME_FORMAT_ISO8601 = 'iso8601';

    /**
     * If we're unable to calculate a time, we'll return this
     */
    const INVALID_TIME = '-----';

    /**
     * @Array
     */
    public $methods;

    /**
     * @Array
     */
    public $methodCodes;

    /**
     * @object DateTime
     */
    private $date;

    /**
     * @String
     */
    private $method;

    /**
     * @Sstring
     */
    private $school = self::SCHOOL_STANDARD;

    /**
     * @String
     */
    private $midnightMode;

    /**
     * @String
     */
    private $latitudeAdjustmentMethod;

    /**
     * @String
     */
    private $timeFormat;

    /**
     * @String
     */
    private $latitude;

    /**
     * @String
     */
    private $longitude;

    /**
     * @String
     */
    private $elevation;

    /**
     * @String
     */
    private $asrShadowFactor = null;

    /**
     * @String
     */
    private $settings;

    /**
     * @String
     */
    private $shafaq = Isha::SHAFAQ_GENERAL; // Only valid for METHOD_MOONSIGHTING

    /**
     * @String
     */
    private $offset = [];


    /**
     * @param string $method
     * @param string $school
     * @param null $asrShadowFactor If specified, this will override the school setting
     * @param array|null $offset
     */
    public function __construct($method = Method::METHOD_MWL, $school = self::SCHOOL_STANDARD, $asrShadowFactor = null)
    {
        $this->loadMethods();
        $this->setMethod($method);
        $this->setSchool($school);
        if ($asrShadowFactor !== null) {
            $this->asrShadowFactor = $asrShadowFactor;
        }
        $this->loadSettings();
    }


    public function setShafaq(string $shafaq)
    {
        $this->shafaq = $shafaq;
    }
    /**
     * @param Method $method [description]
     */
    public function setCustomMethod(Method $method)
    {
        $this->setMethod(Method::METHOD_CUSTOM);
        $this->methods[$this->method] = get_object_vars($method);

        $this->loadSettings();
    }

    private function loadSettings(): void
    {
        $this->settings = new \stdClass();
        $this->settings->{self::IMSAK} = isset($this->methods[$this->method]['params'][self::IMSAK]) ? $this->methods[$this->method]['params'][self::IMSAK] : '10 min';
        $this->settings->{self::FAJR} = isset($this->methods[$this->method]['params'][self::FAJR]) ? $this->methods[$this->method]['params'][self::FAJR] : 0;
        $this->settings->{self::ZHUHR} = isset($this->methods[$this->method]['params'][self::ZHUHR]) ? $this->methods[$this->method]['params'][self::ZHUHR] : '0 min';
        $this->settings->{self::ISHA} = isset($this->methods[$this->method]['params'][self::ISHA]) ? $this->methods[$this->method]['params'][self::ISHA] : 0;
        $this->settings->{self::MAGHRIB} = isset($this->methods[$this->method]['params'][self::MAGHRIB]) ? $this->methods[$this->method]['params'][self::MAGHRIB] : '0 min';

        // Pick up methods midnightMode
        if (isset($this->methods[$this->method]['params'][self::MIDNIGHT]) && $this->methods[$this->method]['params'][self::MIDNIGHT] == self::MIDNIGHT_MODE_JAFARI) {
            $this->setMidnightMode(self::MIDNIGHT_MODE_JAFARI);
        } else {
            $this->setMidnightMode(self::MIDNIGHT_MODE_STANDARD);
        }
    }

    /**
     * @param $latitude
     * @param $longitude
     * @param $timezone
     * @param null $elevation
     * @param string $latitudeAdjustmentMethod
     * @param null $midnightMode
     * @param string $format
     * @return mixed
     * @throws \Exception
     */
    public function getTimesForToday($latitude, $longitude, $timezone, $elevation = null, $latitudeAdjustmentMethod = self::LATITUDE_ADJUSTMENT_METHOD_ANGLE, $midnightMode = null, $format = self::TIME_FORMAT_24H)
    {
        $date = new DateTime(null, new DateTimezone($timezone));

        return $this->getTimes($date, $latitude, $longitude, $elevation, $latitudeAdjustmentMethod, $midnightMode, $format);
    }

    /**
     * @param DateTime $date
     * @param $latitude
     * @param $longitude
     * @param null $elevation
     * @param string $latitudeAdjustmentMethod
     * @param string $midnightMode
     * @param string $format
     * @return mixed
     */
    public function getTimes(DateTime $date, $latitude, $longitude, $elevation = null, $latitudeAdjustmentMethod = self::LATITUDE_ADJUSTMENT_METHOD_ANGLE, $midnightMode = null, $format = self::TIME_FORMAT_24H)
    {
        $this->latitude = 1 * $latitude;
        $this->longitude = 1 * $longitude;
        $this->elevation = $elevation === null ? 0 : 1 * $elevation;
        $this->setTimeFormat($format);
        $this->setLatitudeAdjustmentMethod($latitudeAdjustmentMethod);
        if ($midnightMode !== null) {
            $this->setMidnightMode($midnightMode);
        }
        $this->date = $date;

        return $this->computeTimes();
    }

    /**
     * @return Array
     */
    private function computeTimes()
    {
        // default times
        $times = [
            self::IMSAK => 5,
            self::FAJR => 5,
            self::SUNRISE => 6,
            self::ZHUHR => 12,
            self::ASR => 13,
            self::SUNSET => 18,
            self::MAGHRIB => 18,
            self::ISHA => 18
        ];

        $times = $this->computePrayerTimes($times);

        $times = $this->adjustTimes($times);

        // add midnight time
        $times[self::MIDNIGHT] = ($this->midnightMode == self::MIDNIGHT_MODE_JAFARI) ? $times[self::SUNSET] + $this->timeDiff($times[self::SUNSET], $times[self::FAJR]) / 2 : $times[self::SUNSET] + $this->timeDiff($times[self::SUNSET], $times[self::SUNRISE]) / 2;

        // If our method is Moonsighting, reset the Fajr and Isha times
        if ($this->method == Method::METHOD_MOONSIGHTING) {
            $times =$this->moonsightingRecalculation($times);
        }

        $times = $this->tuneTimes($times);

        return $this->modifyFormats($times);
    }

    public function moonsightingRecalculation(array $times): array
    {
        // Reset Fajr
        $fajrMS = new Fajr($this->date, $this->latitude);
        $times[self::FAJR] = $times[self::SUNRISE] - ($fajrMS->getMinutesBeforeSunrise() / 60);

        // Fajr has changed, also reset Imask
        if ($this->isMin($this->settings->{self::IMSAK})) {
            $times[self::IMSAK] = $times[self::FAJR] - $this->evaluate($this->settings->{self::IMSAK})/ 60;
        }

        // Reset Isha
        $ishaMS = new Isha($this->date, $this->latitude, $this->shafaq);
        $times[self::ISHA] = $times[self::SUNSET] + ($ishaMS->getMinutesAfterSunset()/60);

        return $times;

    }

    /**
     * @param $times
     * @return Array
     */
    private function modifyFormats($times)
    {
        foreach ($times as $i => $t) {
            $times[$i] = $this->getFormattedTime($t, $this->timeFormat);
        }

        return $times;
    }

    /**
     * @param $time
     * @param $format
     * @return string
     */
    private function getFormattedTime($time, $format)
    {
        if (is_nan($time)) {
            return self::INVALID_TIME;
        }
        if ($format == self::TIME_FORMAT_FLOAT) {
            return $time;
        }
        $suffixes = ['am', 'pm'];

        $time = DMath::fixHour($time + 0.5/ 60);  // add 0.5 minutes to round

        $hours = floor($time);
        $minutes = floor(($time - $hours)* 60);
        $suffix = ($this->timeFormat == self::TIME_FORMAT_12H) ? $suffixes[$hours < 12 ? 0 : 1] : '';
        $hour = ($format == self::TIME_FORMAT_24H) ? $this->twoDigitsFormat($hours) : (($hours+ 12 -1)% 12+ 1);
        $twoDigitMinutes = $this->twoDigitsFormat($minutes);

        if ($format == self::TIME_FORMAT_ISO8601) {
            // Create temporary date object
            $date = clone $this->date;
            $date->setTime($hours, $twoDigitMinutes);
            return $date->format(DateTime::ATOM);
        }

        return $hour . ':' . $twoDigitMinutes . ($suffix ? ' ' . $suffix : '');
    }

    /**
     * @param $num
     * @return string
     */
    private function twoDigitsFormat($num)
    {
        return ($num <10) ? '0'. $num : $num;
    }

    /**
     * @param $times
     * @return mixed
     */
    private function tuneTimes($times)
    {
        if (!empty($this->offset)) {
            foreach ($times as $i => $t) {
                if (isset($this->offset[$i])) {
                    $times[$i] += $this->offset[$i] / 60;
                }
            }
        }

        return $times;
    }

    /**
     * @param $str
     * @return mixed
     */
    private function evaluate($str)
    {
        //$str = preg_replace('/\D/', '', $str);

        return floatval($str);
    }

    /**
     * @param $times
     * @return mixed
     */
    private function adjustTimes($times)
    {
        $dateTimeZone = $this->date->getTimezone();

        foreach ($times as $i => $t) {
            $times[$i] += ($dateTimeZone->getOffset($this->date)/3600) - $this->longitude / 15;
        }

        if ($this->latitudeAdjustmentMethod != self::LATITUDE_ADJUSTMENT_METHOD_NONE) {
            $times = $this->adjustHighLatitudes($times);
        }

        if ($this->isMin($this->settings->{self::IMSAK})) {
            $times[self::IMSAK] = $times[self::FAJR] - $this->evaluate($this->settings->{self::IMSAK})/ 60;
        }
        if ($this->isMin($this->settings->{self::MAGHRIB})) {
            $times[self::MAGHRIB] = $times[self::SUNSET] + $this->evaluate($this->settings->{self::MAGHRIB})/ 60;
        }
        if ($this->isMin($this->settings->{self::ISHA})) {
            $times[self::ISHA] = $times[self::MAGHRIB] + $this->evaluate($this->settings->{self::ISHA})/ 60;
        }
        $times[self::ZHUHR] += $this->evaluate($this->settings->{self::ZHUHR})/ 60;

        return $times;
    }

    /**
     * @param $times
     * @return mixed
     */
    private function adjustHighLatitudes($times)
    {
        $nightTime = $this->timeDiff($times[self::SUNSET], $times[self::SUNRISE]);

        $times[self::IMSAK] = $this->adjustHLTime($times[self::IMSAK], $times[self::SUNRISE], $this->evaluate($this->settings->{self::IMSAK}), $nightTime, 'ccw');
        $times[self::FAJR]  = $this->adjustHLTime($times[self::FAJR], $times[self::SUNRISE], $this->evaluate($this->settings->{self::FAJR}), $nightTime, 'ccw');
        $times[self::ISHA]  = $this->adjustHLTime($times[self::ISHA], $times[self::SUNSET], $this->evaluate($this->settings->{self::ISHA}), $nightTime);
        $times[self::MAGHRIB] = $this->adjustHLTime($times[self::MAGHRIB], $times[self::SUNSET], $this->evaluate($this->settings->{self::MAGHRIB}), $nightTime);

        return $times;
    }

    /**
     * @param $str
     * @return bool
     */
    private function isMin($str)
    {
        if (strpos($str, 'min') !== false) {
            return true;
        }

        return false;
    }

    /**
     * @param $time
     * @param $base
     * @param $angle
     * @param $night
     * @param null $direction
     * @return mixed
     */
    private function adjustHLTime($time, $base, $angle, $night, $direction = null)
    {
        $portion = $this->nightPortion($angle, $night);
        $timeDiff = ($direction == 'ccw') ? $this->timeDiff($time, $base): $this->timeDiff($base, $time);
        if (is_nan($time) || $timeDiff > $portion) {
            $time = $base + ($direction == 'ccw' ? (- $portion) : $portion);
        }

        return $time;
    }

    /**
     * @param $angle
     * @param $night
     * @return float
     */
    private function nightPortion($angle, $night)
    {
        $method = $this->latitudeAdjustmentMethod;
        $portion = 1/2; // MidNight
        if ($method == self::LATITUDE_ADJUSTMENT_METHOD_ANGLE) {
            $portion = 1/60 * $angle;
        }
        if ($method == self::LATITUDE_ADJUSTMENT_METHOD_ONESEVENTH) {
            $portion = 1/7;
        }
        return $portion * $night;
    }

    /**
     * @param $t1
     * @param $t2
     * @return mixed
     */
    private function timeDiff($t1, $t2)
    {
        return DMath::fixHour($t2 - $t1);
    }

    /**
     * @param $times
     * @return array
     */
    private function computePrayerTimes($times)
    {
        $times = $this->dayPortion($times);
        $imsak   = $this->sunAngleTime($this->evaluate($this->settings->{self::IMSAK}), $times[self::IMSAK], 'ccw');
        $sunrise = $this->sunAngleTime($this->riseSetAngle(), $times[self::SUNRISE], 'ccw');
        $fajr    = $this->sunAngleTime($this->evaluate($this->settings->{self::FAJR}), $times[self::FAJR], 'ccw');
        $dhuhr   = $this->midDay($times[self::ZHUHR]);
        $asr     = $this->asrTime($this->asrFactor(), $times[self::ASR]);
        $sunset  = $this->sunAngleTime($this->riseSetAngle(), $times[self::SUNSET]);
        $maghrib = $this->sunAngleTime($this->evaluate($this->settings->{self::MAGHRIB}), $times[self::MAGHRIB]);
        $isha    = $this->sunAngleTime($this->evaluate($this->settings->{self::ISHA}), $times[self::ISHA]);

        return [
            self::FAJR => $fajr,
            self::SUNRISE => $sunrise,
            self::ZHUHR => $dhuhr,
            self::ASR => $asr,
            self::SUNSET => $sunset,
            self::MAGHRIB => $maghrib,
            self::ISHA => $isha,
            self::IMSAK => $imsak,
        ];
    }

    /**
     * @param $factor
     * @param $time
     * @return mixed
     */
    private function asrTime($factor, $time)
    {
        $julianDate = GregorianToJD($this->date->format('n'), $this->date->format('d'), $this->date->format('Y'));

        $decl = $this->sunPosition($julianDate + $time)->declination;

        $angle = -DMath::arccot($factor+ DMath::tan(abs($this->latitude- $decl)));

        return $this->sunAngleTime($angle, $time);
    }

    /**
     * @return int|null
     */
    private function sunAngleTime($angle, $time, $direction = null)
    {
        $julianDate = $this->julianDate($this->date->format('Y'), $this->date->format('n'), $this->date->format('d')) - $this->longitude/ (15* 24);
        $decl = $this->sunPosition($julianDate + $time)->declination;
        $noon = $this->midDay($time);
        $p1 = -DMath::sin($angle) - DMath::sin($decl) * DMath::sin($this->latitude);
        $p2 = DMath::cos($decl) * DMath::cos($this->latitude);
        $cosRange = ($p1/$p2);
        if ($cosRange > 1) {
            $cosRange = 1;
        }
        if ($cosRange < -1) {
            $cosRange = -1;
        }
        $t = 1/15 * DMath::arccos($cosRange);

        return $noon + ($direction == 'ccw' ? -$t : $t);
    }

    private function asrFactor()
    {
        if ($this->asrShadowFactor !== null) {
            return $this->asrShadowFactor;
        }
        if ($this->school == self::SCHOOL_STANDARD) {
            return 1;
        } elseif ($this->school == self::SCHOOL_HANAFI) {
            return 2;
        } else {
            return 0;
        }
    }

    /**
     * @return float
     */
    private function riseSetAngle()
    {
        //var earthRad = 6371009; // in meters
        //var angle = DMath.arccos(earthRad/(earthRad+ elv));
        $angle = 0.0347* sqrt($this->elevation); // an approximation

        return 0.833+ $angle;
    }


    /**
     * @param $julianDate
     * @return stdClass
     */
    private function sunPosition($julianDate)
    {
        // compute declination angle of sun and equation of time
        // Ref: http://aa.usno.navy.mil/faq/docs/SunApprox.php
        $D = $julianDate - 2451545.0;
        $g = DMath::fixAngle(357.529 + 0.98560028* $D);
        $q = DMath::fixAngle(280.459 + 0.98564736* $D);
        $L = DMath::fixAngle($q + 1.915 * DMath::sin($g) + 0.020 * DMath::sin(2*$g));

        $R = 1.00014 - 0.01671* DMath::cos($g) - 0.00014* DMath::cos(2*$g);
        $e = 23.439 - 0.00000036* $D;

        $RA = DMath::arctan2(DMath::cos($e)* DMath::sin($L), DMath::cos($L))/ 15;
        $eqt = $q/15 - DMath::fixHour($RA);
        $decl = DMath::arcsin(DMath::sin($e)* DMath::sin($L));

        $res = new \stdClass();
        $res->declination = $decl;
        $res->equation = $eqt;

        return $res;
    }

    /**
     * @param $year
     * @param $month
     * @param $day
     * @return float
     */
    private function julianDate($year, $month, $day)
    {
        if ($month <= 2) {
            $year -= 1;
            $month += 12;
        }
        $A = floor($year/ 100);
        $B = 2- $A+ floor($A/ 4);

        $JD = floor(365.25* ($year+ 4716))+ floor(30.6001* ($month+ 1))+ $day+ $B- 1524.5;

        return $JD;
    }

    /**
     * @param $time
     * @return mixed
     */
    private function midDay($time)
    {
        $julianDate = $this->julianDate($this->date->format('Y'), $this->date->format('n'), $this->date->format('d')) - $this->longitude/ (15* 24);
        $eqt = $this->sunPosition($julianDate + $time)->equation;
        $noon = DMath::fixHour(12 - $eqt);

        return $noon;
    }

    /**
     * @param $times
     * @return mixed
     */
    private function dayPortion($times)
    {
        // convert hours to day portions
        foreach ($times as $i => $time) {
            $times[$i] = $time / 24;
        }

        return $times;
    }

    /**
     * @param string $method
     */
    public function setMethod($method = Method::METHOD_MWL)
    {
        if (in_array($method, $this->methodCodes)) {
            $this->method = $method;
        } else {
            $this->method = Method::METHOD_MWL; // Default to MWL
        }
    }

    /**
     * @param string $method
     */
    public function setAsrJuristicMethod($method = self::SCHOOL_STANDARD)
    {
        if (in_array($method, [self::SCHOOL_HANAFI, self::SCHOOL_STANDARD])) {
            $this->school = $method;
        } else {
            $this->school = self::SCHOOL_STANDARD;
        }
    }

    /**
     * @param string $school
     */
    public function setSchool($school = self::SCHOOL_STANDARD)
    {
        $this->setAsrJuristicMethod($school);
    }

    /**
     * @param string $mode
     */
    public function setMidnightMode($mode = self::MIDNIGHT_MODE_STANDARD)
    {
        if (in_array($mode, [self::MIDNIGHT_MODE_JAFARI, self::MIDNIGHT_MODE_STANDARD])) {
            $this->midnightMode = $mode;
        } else {
            $this->midnightMode = self::MIDNIGHT_MODE_STANDARD;
        }
    }

    /**
     * @param string $method
     */
    public function setLatitudeAdjustmentMethod($method = self::LATITUDE_ADJUSTMENT_METHOD_ANGLE)
    {
        if (in_array($method, [self::LATITUDE_ADJUSTMENT_METHOD_MOTN, self::LATITUDE_ADJUSTMENT_METHOD_ANGLE, self::LATITUDE_ADJUSTMENT_METHOD_ONESEVENTH, self::LATITUDE_ADJUSTMENT_METHOD_NONE ])) {
            $this->latitudeAdjustmentMethod = $method;
        } else {
            $this->latitudeAdjustmentMethod = self::LATITUDE_ADJUSTMENT_METHOD_ANGLE;
        }
    }

    /**
     * @param string $format
     */
    public function setTimeFormat($format = self::TIME_FORMAT_24H)
    {
        if (in_array($format, [self::TIME_FORMAT_ISO8601, self::TIME_FORMAT_24H, self::TIME_FORMAT_FLOAT, self::TIME_FORMAT_12hNS, self::TIME_FORMAT_12H])) {
            $this->timeFormat = $format;
        } else {
            $this->timeFormat = self::TIME_FORMAT_24H;
        }
    }

    /**
     * @param int $imsak
     * @param int $fajr
     * @param int $sunrise
     * @param int $dhuhr
     * @param int $asr
     * @param int $maghrib
     * @param int $sunset
     * @param int $isha
     * @param int $midnight
     */
    public function tune($imsak = 0, $fajr = 0, $sunrise = 0, $dhuhr = 0, $asr = 0, $maghrib = 0, $sunset = 0, $isha = 0, $midnight = 0)
    {
        $this->offset = [
            self::IMSAK => $imsak,
            self::FAJR => $fajr,
            self::SUNRISE => $sunrise,
            self::ZHUHR => $dhuhr,
            self::ASR => $asr,
            self::MAGHRIB => $maghrib,
            self::SUNSET => $sunset,
            self::ISHA => $isha,
            self::MIDNIGHT => $midnight
        ];
    }


    /**
     * Loads all the default settings for calculation methods
     */
    public function loadMethods()
    {
        $this->methods = Method::getMethods();

        $this->methodCodes = Method::getMethodCodes();
    }

    /**
     * [getMethods description]
     * @return [type] [description]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * [getMethod description]
     * @return [type] [description]
     */
    public function getMethod()
    {
        return $this->method;
    }


    public function getMeta(): array
    {
        $result = [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'timezone' => ($this->date->getTimezone())->getName(),
            'method' => $this->methods[$this->method],
            'latitudeAdjustmentMethod' => $this->latitudeAdjustmentMethod,
            'midnightMode' => $this->midnightMode,
            'school' => $this->school,
            'offset' => $this->offset,
        ];
        if (isset($result['method']['offset'])) {
            unset($result['method']['offset']);
        }
        if ($this->method == Method::METHOD_MOONSIGHTING) {
            $result['latitudeAdjustmentMethod'] = self::LATITUDE_ADJUSTMENT_METHOD_NONE;
            $result['method']['params']['shafaq'] = $this->shafaq;
        }

        return $result;
    }

}
