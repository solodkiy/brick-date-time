<?php

declare(strict_types=1);

namespace Brick\DateTime;

use Brick\DateTime\Parser\DateTimeParser;
use Brick\DateTime\Parser\DateTimeParseResult;

/**
 * @method UtcDateTime withDate(LocalDate $date)
 * @method UtcDateTime withTime(LocalTime $time)
 * @method UtcDateTime withYear(int $year)
 * @method UtcDateTime withMonth(int $month)
 * @method UtcDateTime withDay(int $day)
 * @method UtcDateTime withHour(int $hour)
 * @method UtcDateTime withMinute(int $minute)
 * @method UtcDateTime withSecond(int $second)
 * @method UtcDateTime withNano(int $nano)
 * @method UtcDateTime withFixedOffsetTimeZone()
 *
 * @method UtcDateTime plusYears(int $years)
 * @method UtcDateTime plusMonths(int $months)
 * @method UtcDateTime plusWeeks(int $weeks)
 * @method UtcDateTime plusDays(int $days)
 * @method UtcDateTime plusHours(int $hours)
 * @method UtcDateTime plusMinutes(int $minutes)
 * @method UtcDateTime plusSeconds(int $seconds)
 * @method UtcDateTime plusPeriod(Period $period)
 * @method UtcDateTime plusDuration(Duration $duration)
 *
 * @method UtcDateTime minusYears(int $years)
 * @method UtcDateTime minusMonths(int $months)
 * @method UtcDateTime minusWeeks(int $weeks)
 * @method UtcDateTime minusDays(int $days)
 * @method UtcDateTime minusHours(int $hours)
 * @method UtcDateTime minusMinutes(int $minutes)
 * @method UtcDateTime minusSeconds(int $seconds)
 * @method UtcDateTime minusPeriod(Period $period)
 * @method UtcDateTime minusDuration(Duration $duration)
 */
final class UtcDateTime extends ZonedDateTime
{
    public static function of(LocalDateTime $dateTime, TimeZone $timeZone = null): ZonedDateTime
    {
        if ($timeZone === null) {
            $timeZone = TimeZone::utc();
        }
        if (!$timeZone->isEqualTo(TimeZone::utc())) {
            throw new \InvalidArgumentException('Create UtcDateTime with not UTC timezone is not supported');
        }
        return parent::of($dateTime, $timeZone);
    }

    public static function ofInstant(Instant $instant, TimeZone $timeZone = null): ZonedDateTime
    {
        if ($timeZone === null) {
            $timeZone = TimeZone::utc();
        }
        if (!$timeZone->isEqualTo(TimeZone::utc())) {
            throw new \InvalidArgumentException('Create UtcDateTime with not UTC timezone is not supported');
        }
        return parent::ofInstant($instant, $timeZone);
    }

    public static function now(TimeZone $timeZone = null, ?Clock $clock = null): ZonedDateTime
    {
        if ($timeZone === null) {
            $timeZone = TimeZone::utc();
        }
        if (!$timeZone->isEqualTo(TimeZone::utc())) {
            throw new \InvalidArgumentException('Create UtcDateTime with not UTC timezone is not supported');
        }
        return parent::now($timeZone, $clock);
    }

    public static function from(DateTimeParseResult $result): ZonedDateTime
    {
        $result = parent::from($result);
        if (!$result->getTimeZone()->isEqualTo(TimeZone::utc())) {
            $result = $result->withTimeZoneSameInstant(TimeZone::utc());
        }
        return $result;
    }

    public static function parse(string $text, ?DateTimeParser $parser = null): ZonedDateTime
    {
        $result = parent::parse($text, $parser);
        if (!$result->getTimeZone()->isEqualTo(TimeZone::utc())) {
            $result = $result->withTimeZoneSameInstant(TimeZone::utc());
        }
        return $result;
    }

    public static function fromDateTime(\DateTimeInterface $dateTime): ZonedDateTime
    {
        $result = parent::fromDateTime($dateTime);
        if (!$result->getTimeZone()->isEqualTo(TimeZone::utc())) {
            $result = $result->withTimeZoneSameInstant(TimeZone::utc());
        }
        return $result;
    }

    /**
     * @param string $input "Y-m-d H:i:s.u" or "Y-m-d H:i:s"
     * @param TimeZone|null $timeZone
     * @return UtcDateTime
     */
    public static function fromSqlFormat(string $input, TimeZone $timeZone = null): ZonedDateTime
    {
        if ($timeZone === null) {
            $timeZone = TimeZone::utc();
        }
        if (!$timeZone->isEqualTo(TimeZone::utc())) {
            throw new \InvalidArgumentException('Create UtcDateTime with not UTC timezone is not supported');
        }

        $result = parent::fromSqlFormat($input, $timeZone);

        /**
         * @var UtcDateTime
         */
        return $result;
    }

    /**
     * Convert to RFC 3339 compatible format (2022-03-30T21:00:00.000000Z)
     * @param int $precision
     * @return string
     */
    public function toCanonicalFormat(int $precision = 6): string
    {
        if ($precision < 0 || $precision > 9) {
            throw new \InvalidArgumentException(
                'Incorrect precision. Expected value between 0 and 9, got: ' . $precision
            );
        }
        $result = $this->toPhpFormat('Y-m-d\TH:i:s');

        if ($precision > 0) {
            $nano = str_pad((string)$this->getNano(), 9, '0', STR_PAD_LEFT);
            $result .= '.' . substr($nano, 0, $precision);
        }
        $result .= 'Z';

        return $result;
    }
}