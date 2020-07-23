[![CircleCI](https://circleci.com/gh/islamic-network/prayer-times.svg?style=shield)](https://circleci.com/gh/islamic-network/prayer-times)
[![Releases](https://img.shields.io/github/v/release/islamic-network/prayer-times)](https://github.com/islamic-network/prayer-times/releases)
![](https://img.shields.io/packagist/dt/islamic-network/prayer-times.svg)

## Prayer Times Library (PHP)

This is a PHP edition of the Prayer Times Library (v2.3) originally written in JavaScript by Hamid Zarrabi-Zadeh of PrayTimes.org and available on http://praytimes.org/code/v2/js/PrayTimes.js. It has divulged much from the original since it was first written, so please don't use it as a 'like for like' substitute - the method names, among others things, are different..

## How to Use this Library

The library is a composer package, so to install it, run:

```
composer require islamic-network/prayer-times
```

Using it is rather simple:

```php
<?php
require_once("vendor/autoload.php");
use IslamicNetwork\PrayerTimes\PrayerTimes;

// Instantiate the class with your chosen method, Juristic School for Asr and if you want or own Asr factor, make the juristic school null and pass your own Asr shadow factor as the third parameter. Note that all parameters are optional.

$pt = new PrayerTimes('ISNA'); // new PrayerTimes($method, $asrJuristicMethod, $asrShadowFactor);

// Then, to get times for today.

$times = $pt->getTimesForToday($latitude, $longitude, $timezone, $elevation = null, $latitudeAdjustmentMethod = self::LATITUDE_ADJUSTMENT_METHOD_ANGLE, $midnightMode = self::MIDNIGHT_MODE_STANDARD, $format = self::TIME_FORMAT_24H);

// Or, if you want times for another day

$times = $pt->getTimes(DateTime $date, $latitude, $longitude, $elevation = null, $latitudeAdjustmentMethod = self::LATITUDE_ADJUSTMENT_METHOD_ANGLE, $midnightMode = self::MIDNIGHT_MODE_STANDARD, $format = self::TIME_FORMAT_24H);

//If you would like to offset the time for each result by a particular number of minutes, simply call the tune method before calling getTimes or getTimesForToday.

$pt->tune($imsak = 0, $fajr= 0, $sunrise = 0, $dhuhr = 0, $asr = 0, $maghrib = 0, $sunset = 0, $isha = 0, $midnight = 0);

// Finally, you can also create your own methods:
$method = new Method('My Custom Method');
$method->setFajrAngle(18);
$method->setMaghribAngleOrMins(19.5);
$method->setIshaAngleOrMins('90 min');
// And then:
$pt = new PrayerTimes(PrayerTimes::METHOD_CUSTOM);
$pt->setCustomMethod($method);
// And then the same as before:
$times = $pt->getTimesForToday($latitude, $longitude, $timezone, $elevation = null, $latitudeAdjustmentMethod = self::LATITUDE_ADJUSTMENT_METHOD_ANGLE, $midnightMode = self::MIDNIGHT_MODE_STANDARD, $format = self::TIME_FORMAT_24H);

```

## Methods

Supported methods can be seen @ https://github.com/islamic-network/prayer-times/blob/master/src/PrayerTimes/Method.php#L10.

### Understanding Methods

For a discussion on methods, see https://aladhan.com/calculation-methods.

## Tests

Compare the results produced by this library against the original JS version.

## Contributors

Hamid Zarrabi-Zadeh, Meezaan-ud-Din Abdu Dhil-Jalali Wal-Ikram

## License

Same as the original - License: GNU LGPL v3.0, which effectively means:
```
Permission is granted to use this code, with or without modification, in any website or application provided that credit is given to the original work with a link back to PrayTimes.org.
```
