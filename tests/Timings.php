<?php
$x = (realpath(__DIR__ . '/../vendor/autoload.php'));
require_once($x);

use  IslamicNetwork\PrayerTimes\PrayerTimes;

class TimingsTest extends PHPUnit\Framework\TestCase
{
    public function testIso8601Format()
    {
        $pt = new PrayerTimes('ISNA');
        $date = new DateTime('2014-4-24', new DateTimezone('Europe/London'));
        $t = $pt->getTimes($date, '51.508515', '-0.1254872', null, PrayerTimes::LATITUDE_ADJUSTMENT_METHOD_ANGLE, null, PrayerTimes::TIME_FORMAT_ISO8601);
        $this->assertEquals('2014-04-24T03:57:00+01:00', $t['Fajr']);
        $this->assertEquals('2014-04-24T05:46:00+01:00', $t['Sunrise']);
        $this->assertEquals('2014-04-24T12:59:00+01:00', $t['Dhuhr']);
        $this->assertEquals('2014-04-24T16:55:00+01:00', $t['Asr']);
    }

    public function testTimes()
    {
        $pt = new PrayerTimes('ISNA');
        $date = new DateTime('2014-4-24', new DateTimezone('Europe/London'));
        $t = $pt->getTimes($date, '51.508515', '-0.1254872');
        $this->assertEquals('03:57', $t['Fajr']);
        $this->assertEquals('05:46', $t['Sunrise']);
        $this->assertEquals('12:59', $t['Dhuhr']);
        $this->assertEquals('16:55', $t['Asr']);
        $this->assertEquals('20:12', $t['Sunset']);
        $this->assertEquals('20:12', $t['Maghrib']);
        $this->assertEquals('22:02', $t['Isha']);
        $this->assertEquals('03:47', $t['Imsak']);
        $this->assertEquals('00:59', $t['Midnight']);

        $pt = new PrayerTimes('KARACHI');
        $date = new DateTime('2018-01-19', new DateTimezone('Asia/Yekaterinburg'));
        $t = $pt->getTimes($date, '67.104732', '67.104732');
        foreach($t as $ts) {
            $this->assertNotEquals('-----', $ts);
        }
    }

}
