# Date Time
[![Latest Stable Version](https://poser.pugx.org/lightsource/date-time/v)](//packagist.org/packages/lightsource/date-time)
[![Total Downloads](https://poser.pugx.org/lightsource/date-time/downloads)](//packagist.org/packages/lightsource/date-time)
[![Monthly Downloads](https://poser.pugx.org/lightsource/date-time/d/monthly)](//packagist.org/packages/lightsource/date-time)
[![Daily Downloads](https://poser.pugx.org/lightsource/date-time/d/daily)](//packagist.org/packages/lightsource/date-time)
[![License](https://poser.pugx.org/lightsource/date-time/license)](//packagist.org/packages/lightsource/date-time)

## What is it
Helper for working with a standard DateTime class, provide functions like adding DateInterval, etc..

## Installation
```
composer require lightsource/date-time
```

## Example of usage

```
use LightSource\DateTime\DATE_TIME;

require_once __DIR__ . '/vendor/autoload.php';

$futureDateTime = DATE_TIME::AddPeriod( DATE_TIME::HOURS, 3 );
$nowTimestamp   = DATE_TIME::ToTimestamp();
$isFuture       = DATE_TIME::IsFuture( $futureDateTime );
```
