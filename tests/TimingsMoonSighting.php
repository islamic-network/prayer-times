<?php
$x = (realpath(__DIR__ . '/../vendor/autoload.php'));
require_once($x);

use IslamicNetwork\PrayerTimes\PrayerTimes;
use IslamicNetwork\PrayerTimes\Method;
use IslamicNetwork\MoonSighting\Isha;

class TimingsMoonSightingTest extends PHPUnit\Framework\TestCase
{
    public function testTimes()
    {
        $pt = new PrayerTimes(Method::METHOD_MOONSIGHTING);
        $pt->setShafaq(Isha::SHAFAQ_GENERAL);
        $date = new DateTime('2014-4-24', new DateTimezone('Europe/London'));
        $t = $pt->getTimes($date, '51.508515', '-0.1254872');
        $this->assertEquals('04:04', $t['Fajr']);
        $this->assertEquals('05:46', $t['Sunrise']);
        $this->assertEquals('12:59', $t['Dhuhr']);
        $this->assertEquals('16:55', $t['Asr']);
        $this->assertEquals('20:12', $t['Sunset']);
        $this->assertEquals('20:12', $t['Maghrib']);
        $this->assertEquals('21:21', $t['Isha']);
        $this->assertEquals('03:54', $t['Imsak']);
        $this->assertEquals('00:59', $t['Midnight']);

        $pt = new PrayerTimes('KARACHI');
        $date = new DateTime('2018-01-19', new DateTimezone('Asia/Yekaterinburg'));
        $t = $pt->getTimes($date, '67.104732', '67.104732');
        foreach($t as $ts) {
            $this->assertNotEquals('-----', $ts);
        }
    }

}
