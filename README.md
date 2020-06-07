# Date Time

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
