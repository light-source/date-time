<?php

namespace LightSource\DateTime;

use DateTime;
use Exception;
use DateInterval;
use DateTimeZone;

/**
 * Class DATE_TIME
 * @package LightSource\DateTime
 */
abstract class DATE_TIME {


	//////// constants


	const SECONDS = 'seconds';
	const HOURS = 'hours';
	const DAYS = 'days';


	//////// static fields


	/**
	 * @var string
	 */
	public static $Format = 'Y-m-d H:i:s';


	//////// static methods


	/**
	 * @return DateTime|null
	 */
	final public static function DateTimeNow() {

		$dateTimeNow = null;

		try {
			$dateTimeNow = new DateTime();
		} catch ( Exception $ex ) {
			$dateTimeNow = null;
		}

		return $dateTimeNow;
	}

	/**
	 * @param string $intervalInfo See DateInterval construct info
	 * @param bool $isInvert True if negative interval (-time, not +)
	 *
	 * @return DateInterval|null
	 */
	final public static function DateInterval( $intervalInfo, $isInvert ) {

		$dateInterval = null;

		try {
			$dateInterval         = new DateInterval( $intervalInfo );
			$dateInterval->invert = $isInvert;
		} catch ( Exception $e ) {

			$dateInterval = null;

		}

		return $dateInterval;
	}

	/**
	 * Convert current|specified DateTime to string
	 *
	 * @param null|DateTime $dateTime
	 * @param string|null $format
	 *
	 * @return string
	 */
	final public static function DateTimeToString( $dateTime = null, $format = null ) {

		$format = ! is_null( $format ) ?
			$format :
			self::$Format;

		$dateTime = is_null( $dateTime ) ?
			self::DateTimeNow() :
			clone $dateTime;

		return ! is_null( $dateTime ) ?
			$dateTime->format( $format ) :
			'';
	}

	/**
	 * Convert current|specified DateTime to timestamp
	 *
	 * @param null|DateTime $dateTime
	 *
	 * @return int
	 */
	final public static function DateTimeToTimestamp( $dateTime = null ) {

		$dateTime = is_null( $dateTime ) ?
			self::DateTimeNow() :
			clone $dateTime;

		return ( ! is_null( $dateTime ) ?
			$dateTime->getTimestamp() :
			0 );
	}

	/**
	 * Add period to current|specified DateTime, auto-support negative
	 *
	 * @param string $periodType const self::DT__*
	 * @param int $value
	 * @param null|DateTime $dateTime
	 *
	 * @return DateTime|null New DateTime (clone)
	 */
	final public static function DateTimeAddPeriod( $periodType, $value, $dateTime = null ) {

		// used clone, because add is modify DateTime

		$newDateTime = null;

		$intervalInfo = '';
		$isInvert     = $value < 0;
		// required abs value, used '-' in value it logic does not correct
		$value = abs( $value );

		switch ( $periodType ) {
			case self::SECONDS:
				$intervalInfo = "PT{$value}S";
				break;
			case self::HOURS:
				$intervalInfo = "PT{$value}H";
				break;
			case self::DAYS:
				$intervalInfo = "P{$value}D";
				break;
			default:

				return $newDateTime;
				break;
		}

		// used clone because ->add modify object

		$newDateTime = is_null( $dateTime ) ? self::DateTimeNow() : clone $dateTime;

		$dateInterval = self::DateInterval( $intervalInfo, $isInvert );

		return ( ( ! is_null( $newDateTime ) && ! is_null( $dateInterval ) ) ? $newDateTime->add( $dateInterval ) : null );
	}

	/**
	 * @param DateTime $dateTime
	 *
	 * @return bool
	 */
	final public static function DateTimeIsFuture( $dateTime ) {
		return $dateTime > self::DateTimeNow();
	}

	/**
	 * Create DateTime from string
	 *
	 * @param string $string
	 * @param string|null $format
	 *
	 * @return DateTime|null
	 */
	final public static function DateTimeFromString( $string, $format = null ) {

		$format = ! is_null( $format ) ?
			$format :
			self::$Format;

		$dateTime = DateTime::createFromFormat( $format, $string );

		if ( false === $dateTime ) {
			$dateTime = null;
		}

		return $dateTime;
	}

	/**
	 * Get difference between two dates.
	 * If used $periodType HOURS | DAYS in division used floor to return CLEAR difference,
	 * so ex. 2.9 hours it's 2 hours, rest is ignored
	 *
	 * @param string $periodType
	 * @param DateTime $dateTimeMax
	 * @param null|DateTime $dateTimeMin
	 *
	 * @return int
	 */
	final public static function DateTimeDifference( $periodType, $dateTimeMax, $dateTimeMin = null ) {

		$dateTimeMin = is_null( $dateTimeMin ) ? self::DateTimeNow() : clone $dateTimeMin;

		$differenceInSeconds = ! is_null( $dateTimeMin ) ?
			( $dateTimeMax->getTimestamp() - $dateTimeMin->getTimestamp() ) : 0;

		$differenceInPeriodType = 0;

		switch ( $periodType ) {
			case self::SECONDS:
				$differenceInPeriodType = $differenceInSeconds;
				break;
			case self::HOURS:
				// used (int) because floor return number as float
				$differenceInPeriodType = (int) floor( $differenceInSeconds / 60 / 60 );
				break;
			case self::DAYS:
				// used (int) because floor return number as float
				$differenceInPeriodType = (int) floor( $differenceInSeconds / 60 / 60 / 24 );
				break;
			default:

				break;
		}


		return $differenceInPeriodType;
	}

	/**
	 * UTC timezones info
	 *
	 * @return array [ secondsOffset => +xx:xx|-xx:xx ] Ordered by keys ASC, xx:xx is user-friendly hours:minutes offset
	 */
	final public static function TimezonesInfo() {

		$timezonesInfo = [];

		try {
			$utcDateTimeNow = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
		} catch ( Exception $ex ) {

			return $timezonesInfo;
		}

		$timezoneNames = DateTimeZone::listIdentifiers( DateTimeZone::ALL );

		foreach ( $timezoneNames as $timezoneName ) {

			$dateTimeZone  = new DateTimeZone( $timezoneName );
			$secondsOffset = $dateTimeZone->getOffset( $utcDateTimeNow );

			if ( key_exists( $secondsOffset, $timezonesInfo ) ) {
				continue;
			}

			$offset = strval( $secondsOffset / 60 / 60 );
			$offset = explode( '.', $offset );

			if ( 1 === count( $offset ) ) {
				$offset[] = 0;
			}

			$absHours = abs( intval( $offset[0] ) );
			$minutes  = floatval( '0.' . strval( $offset[1] ) );
			$minutes  = intval( $minutes * 60 );

			// + must be include 0
			$strOffset = $secondsOffset < 0 ? '-' : '+';
			$strOffset .= $absHours < 10 ? '0' : '';

			$strOffset .= strval( $absHours );
			$strOffset .= ':';
			$strOffset .= $minutes < 10 ? '0' : '';
			$strOffset .= strval( $minutes );

			$timezonesInfo[ $secondsOffset ] = $strOffset;

		}

		ksort( $timezonesInfo );

		return $timezonesInfo;
	}

}
