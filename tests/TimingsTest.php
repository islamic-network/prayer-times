<?php

$x = (realpath(__DIR__ . '/../vendor/autoload.php'));
require_once($x);

use IslamicNetwork\PrayerTimes\Method;
use IslamicNetwork\PrayerTimes\PrayerTimes;

class TimingsTest extends PHPUnit\Framework\TestCase
{
    public function testIso8601Format()
    {
        $pt   = new PrayerTimes('ISNA');
        $date = new DateTime('2014-4-24', new DateTimezone('Europe/London'));
        $t    = $pt->getTimes($date, '51.508515', '-0.1254872', null, PrayerTimes::LATITUDE_ADJUSTMENT_METHOD_ANGLE, null, PrayerTimes::TIME_FORMAT_ISO8601);
        $this->assertEquals('2014-04-24T03:57:00+01:00', $t['Fajr']);
        $this->assertEquals('2014-04-24T05:46:00+01:00', $t['Sunrise']);
        $this->assertEquals('2014-04-24T12:59:00+01:00', $t['Dhuhr']);
        $this->assertEquals('2014-04-24T16:54:00+01:00', $t['Asr']);
    }

    public function testIso8601FormatNextDay()
    {
        $pt   = new PrayerTimes('ISNA');
        $date = new DateTime('2014-4-24', new DateTimezone('Europe/London'));
        $t    = $pt->getTimes($date, '70', '-10', null, PrayerTimes::LATITUDE_ADJUSTMENT_METHOD_ANGLE, null, PrayerTimes::TIME_FORMAT_ISO8601);
        $this->assertEquals('2014-04-24T03:15:00+01:00', $t['Fajr']);
        $this->assertEquals('2014-04-24T04:50:00+01:00', $t['Sunrise']);
        $this->assertEquals('2014-04-24T13:38:00+01:00', $t['Dhuhr']);
        $this->assertEquals('2014-04-24T17:45:00+01:00', $t['Asr']);
        $this->assertEquals('2014-04-24T22:29:00+01:00', $t['Maghrib']);
        $this->assertEquals('2014-04-25T00:04:00+01:00', $t['Isha']);
        $this->assertEquals('2014-04-25T01:39:00+01:00', $t['Midnight']);
    }

    public function testIso8601FormatPreviousDay()
    {
        $pt   = new PrayerTimes('ISNA');
        $date = new DateTime('2014-4-24', new DateTimezone('Europe/London'));
        $t    = $pt->getTimes($date, '70', '40', null, PrayerTimes::LATITUDE_ADJUSTMENT_METHOD_ANGLE, null, PrayerTimes::TIME_FORMAT_ISO8601);
        $this->assertEquals('2014-04-23T23:55:00+01:00', $t['Fajr']);
        $this->assertEquals('2014-04-24T01:30:00+01:00', $t['Sunrise']);
        $this->assertEquals('2014-04-24T10:18:00+01:00', $t['Dhuhr']);
        $this->assertEquals('2014-04-24T14:25:00+01:00', $t['Asr']);
        $this->assertEquals('2014-04-24T19:08:00+01:00', $t['Maghrib']);
        $this->assertEquals('2014-04-24T20:44:00+01:00', $t['Isha']);
        $this->assertEquals('2014-04-24T22:19:00+01:00', $t['Midnight']);
    }

    public function testTimes()
    {
        $pt   = new PrayerTimes('ISNA');
        $date = new DateTime('2014-4-24', new DateTimezone('Europe/London'));
        $t    = $pt->getTimes($date, '51.508515', '-0.1254872');
        $this->assertEquals('03:57', $t['Fajr']);
        $this->assertEquals('05:46', $t['Sunrise']);
        $this->assertEquals('12:59', $t['Dhuhr']);
        $this->assertEquals('16:54', $t['Asr']);
        $this->assertEquals('20:12', $t['Sunset']);
        $this->assertEquals('20:12', $t['Maghrib']);
        $this->assertEquals('22:02', $t['Isha']);
        $this->assertEquals('03:47', $t['Imsak']);
        $this->assertEquals('00:59', $t['Midnight']);

        $pt   = new PrayerTimes('KARACHI');
        $date = new DateTime('2018-01-19', new DateTimezone('Asia/Yekaterinburg'));
        $t    = $pt->getTimes($date, '67.104732', '67.104732');
        foreach ($t as $ts) {
            $this->assertNotEquals('-----', $ts);
        }
    }

    public function testMonth()
    {
        $longitude    = '69.250770';
        $latitude     = '41.289810';
        $customMethod = new Method('Custom');//todo custom is not working
        $customMethod->setFajrAngle(19);
        $customMethod->params[PrayerTimes::MAGHRIB] = 1.6;
        $pt                                         = new PrayerTimes(Method::METHOD_CUSTOM, PrayerTimes::SCHOOL_HANAFI); // new PrayerTimes($method, $asrJuristicMethod,
        $pt->setCustomMethod($customMethod);

        $t     = $pt->getTimesByMonth(2025, 2, $longitude, $latitude, 'Asia/Tashkent');
        $dates = [
                [
                        'date'     => '2025-02-01',
                        'Fajr'     => '06:10',
                        'Sunrise'  => '07:34',
                        'Dhuhr'    => '12:37',
                        'Asr'      => '15:57',
                        'Sunset'   => '17:39',
                        'Maghrib'  => '17:44',
                        'Isha'     => '18:58',
                        'Imsak'    => '06:00',
                        'Midnight' => '00:37',
                ],
                [
                        'date'     => '2025-02-02',
                        'Fajr'     => '06:10',
                        'Sunrise'  => '07:33',
                        'Dhuhr'    => '12:37',
                        'Asr'      => '15:58',
                        'Sunset'   => '17:41',
                        'Maghrib'  => '17:45',
                        'Isha'     => '18:59',
                        'Imsak'    => '06:00',
                        'Midnight' => '00:37',
                ],
                [
                        'date'     => '2025-02-03',
                        'Fajr'     => '06:09',
                        'Sunrise'  => '07:32',
                        'Dhuhr'    => '12:37',
                        'Asr'      => '15:59',
                        'Sunset'   => '17:42',
                        'Maghrib'  => '17:46',
                        'Isha'     => '19:00',
                        'Imsak'    => '05:59',
                        'Midnight' => '00:37',
                ],
        ];
        foreach ($dates as $date) {
            $this->assertEquals($date['date'], $t[$date['date']]['date']);
            $this->assertEquals($date['Fajr'], $t[$date['date']]['Fajr']);
            $this->assertEquals($date['Sunrise'], $t[$date['date']]['Sunrise']);
            $this->assertEquals($date['Dhuhr'], $t[$date['date']]['Dhuhr']);
            $this->assertEquals($date['Asr'], $t[$date['date']]['Asr']);
            $this->assertEquals($date['Sunset'], $t[$date['date']]['Sunset']);
            $this->assertEquals($date['Maghrib'], $t[$date['date']]['Maghrib']);
            $this->assertEquals($date['Isha'], $t[$date['date']]['Isha']);
            $this->assertEquals($date['Imsak'], $t[$date['date']]['Imsak']);
            $this->assertEquals($date['Midnight'], $t[$date['date']]['Midnight']);
        }
    }
}
