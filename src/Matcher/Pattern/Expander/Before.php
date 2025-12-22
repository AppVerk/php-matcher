<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\Pattern\Expander;

use Aeon\Calendar\Gregorian\DateTime;
use Aeon\Calendar\Gregorian\Time;
use AppVerk\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;
use Exception;
use InvalidArgumentException;

use function is_string;
use function sprintf;

final class Before implements PatternExpander
{
    use BacktraceBehavior;

    public const string NAME = 'before';

    private ?DateTime $boundaryDateTime = null;

    private ?Time $boundaryTime = null;

    private ?string $error = null;

    public function __construct(string $boundary)
    {
        if (!is_string($boundary)) {
            throw new InvalidArgumentException(sprintf('Before expander require "string", got "%s".', new StringConverter($boundary)));
        }

        try {
            $this->boundaryDateTime = DateTime::fromString($boundary);
        } catch (Exception $exception) {
            try {
                $this->boundaryTime = Time::fromString($boundary);
            } catch (Exception) {
                throw new InvalidArgumentException(sprintf('Boundary value "%s" is not a valid date, date time or time.', new StringConverter($boundary)), $exception->getCode(), $exception);
            }
        }
    }

    #[\Override]
    public static function is(string $name): bool
    {
        return self::NAME === $name;
    }

    #[\Override]
    public function match(mixed $value): bool
    {
        $this->backtrace->expanderEntrance(self::NAME, $value);

        if (!is_string($value)) {
            $this->error = sprintf('Before expander require "string", got "%s".', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        if ($this->boundaryDateTime instanceof DateTime) {
            return $this->compareDateTime($value);
        }

        return $this->compareTime($value);
    }

    #[\Override]
    public function getError(): ?string
    {
        return $this->error;
    }

    private function compareDateTime(string $value): bool
    {
        try {
            $datetime = DateTime::fromString($value);

            if ($datetime->isAfter($this->boundaryDateTime)) {
                $this->error = sprintf('Value "%s" is before "%s".', new StringConverter($value), new StringConverter($this->boundaryDateTime));
                $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

                return false;
            }

            $result = $datetime->isBefore($this->boundaryDateTime);

            if ($result) {
                $this->backtrace->expanderSucceed(self::NAME, $value);
            } else {
                $this->backtrace->expanderFailed(self::NAME, $value, '');
            }

            return $result;
        } catch (Exception) {
            $this->error = sprintf('Value "%s" is not a valid date.', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }
    }

    private function compareTime(string $value): bool
    {
        try {
            $datetime = Time::fromString($value);

            if ($datetime->isGreaterThan($this->boundaryTime)) {
                $this->error = sprintf('Value "%s" is before "%s".', new StringConverter($value), new StringConverter($this->boundaryTime));
                $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

                return false;
            }

            $result = $datetime->isLessThan($this->boundaryTime);

            if ($result) {
                $this->backtrace->expanderSucceed(self::NAME, $value);
            } else {
                $this->backtrace->expanderFailed(self::NAME, $value, '');
            }

            return $result;
        } catch (Exception) {
            $this->error = sprintf('Value "%s" is not a valid time.', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }
    }
}
