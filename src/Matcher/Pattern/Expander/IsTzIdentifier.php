<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\Pattern\Expander;

use Aeon\Calendar\Gregorian\TimeZone;
use AppVerk\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class IsTzIdentifier implements PatternExpander
{
    use BacktraceBehavior;

    /**
     * @var string
     */
    public const NAME = 'isTzIdentifier';

    private ?string $error;

    public function __construct()
    {
        $this->error = null;
    }

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    public function match($value) : bool
    {
        $this->backtrace->expanderEntrance(self::NAME, $value);

        if (!\is_string($value)) {
            $this->error = \sprintf('Match expander require "string", got "%s".', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        try {
            $timezone = TimeZone::fromString($value);

            if ($result = $timezone->isIdentifier()) {
                $this->backtrace->expanderSucceed(self::NAME, $value);
            } else {
                $this->error = \sprintf('Timezone "%s" is not an identifier type.', $value);
                $this->backtrace->expanderFailed(self::NAME, $value, $this->error);
            }

            return $result;
        } catch (\Exception $exception) {
            $this->error = \sprintf('Timezone expander require valid timezone, got "%s".', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }
    }

    public function getError() : ?string
    {
        return $this->error;
    }
}
