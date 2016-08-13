## Prayer Times Library (PHP)

This is a PHP edition of the Prayer Times Library (v2.3) originally written in JavaScript by Hamid Zarrabi-Zadeh of PrayTimes.org and available on http://praytimes.org/code/v2/js/PrayTimes.js.

## How to Use this Library

This is not a composer package, so you need to, for the time being, manually include it in your script.

```
<?php
require_once('/path/to/prayerTimes.php');

// Instantiate the class with your chosen method, Jursitic School for Asr and if you want or own Asr factor, make the jursitic school null and pass your own Asr shadow factor as the third parameter. Note that all parameters are optional.

$pt = new PrayerTimes('ISNA'); // new PrayerTimes($method, $asrJuristicMethod, $asrShadowFactor);

// Then, to get times for today.

$times = $pt->getTimesForToday($latitude, $longitude, $timezone, $elevation = null, $latitudeAdjustmentMethod = self::LATITUDE_ADJUSTMENT_METHOD_ANGLE, $midnightMode = self::MIDNIGHT_MODE_STANDARD, $format = self::TIME_FORMAT_24H);

// Or, if you want times for another day

$times = $pt->getTimes(DateTime $date, $latitude, $longitude, $elevation = null, $latitudeAdjustmentMethod = self::LATITUDE_ADJUSTMENT_METHOD_ANGLE, $midnightMode = self::MIDNIGHT_MODE_STANDARD, $format = self::TIME_FORMAT_24H);

//If you would like to offset the time for each result by a particular number of minutes, simply call the tune method before calling getTimes or getTimesForToday.

$pt->tune($imsak = 0, $fajr= 0, $sunrise = 0, $dhuhr = 0, $asr = 0, $maghrib = 0, $sunset = 0, $isha = 0, $midnight = 0);

```

## Motivation

AlAdhan.com uses version one of the PrayerTimes Library from PrayTimes.org. It will now use this updated version.

## Tests

Compare the results produced by this library against the original JS version.

## Contributors

Hamid Zarrabi-Zadeh, Meezaan-ud-Din Abdu Dhil-Jalali Wal-Ikram

## License

Same as the original - License: GNU LGPL v3.0, which effectively means:
```
Permission is granted to use this code, with or without modification, in any website or application provided that credit is given to the original work with a link back to PrayTimes.org.
```