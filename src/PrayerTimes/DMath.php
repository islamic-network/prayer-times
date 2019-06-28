<?php
namespace IslamicNetwork\PrayerTimes;
/**
 * Class DMath
 */

class DMath
{
    public static function dtr($d)
    {
        return ($d * pi()) / 180.0;
    }
    public static function rtd($r)
    {
        return ($r * 180.0) / pi();
    }

    public static function sin($d)
    {
        return sin(self::dtr($d));
    }
    public static function cos($d)
    {
        return cos(self::dtr($d));
    }
    public static function tan($d)
    {
        return tan(self::dtr($d));
    }

    public static function arcsin($d)
    {
        return self::rtd(asin($d));
    }
    public static function arccos($d)
    {
        return self::rtd(acos($d));
    }
    public static function arctan($d)
    {
        return self::rtd(atan($d));
    }

    public static function arccot($x)
    {
        return self::rtd(atan(1/$x));
    }
    public static function arctan2($y, $x)
    {
        return self::rtd(atan2($y, $x));
    }

    public static function fixAngle($a)
    {
        return self::fix($a, 360);
    }
    public static function fixHour($a)
    {
        return self::fix($a, 24 );
    }

    public static function fix($a, $b)
    {
        $a = $a - $b * (floor($a / $b));
        return ($a < 0) ? $a + $b : $a;
    }
}
