<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\Pattern\Expander;

use Aeon\Calendar\Gregorian\TimeZone;
use AppVerk\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;
use Exception;

use function is_string;
use function sprintf;

final class IsTzOffset implements PatternExpander
{
    use BacktraceBehavior;

    public const string NAME = 'isTzOffset';

    private ?string $error = null;

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
            $this->error = sprintf('Match expander require "string", got "%s".', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        try {
            $timezone = TimeZone::fromString($value);

            if ($result = $timezone->isOffset()) {
                $this->backtrace->expanderSucceed(self::NAME, $value);
            } else {
                $this->error = sprintf('Timezone "%s" is not an offset type.', $value);
                $this->backtrace->expanderFailed(self::NAME, $value, $this->error);
            }

            return $result;
        } catch (Exception) {
            $this->error = sprintf('Timezone expander require valid timezone, got "%s".', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }
    }

    #[\Override]
    public function getError(): ?string
    {
        return $this->error;
    }
}
