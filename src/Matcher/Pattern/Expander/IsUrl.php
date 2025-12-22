<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\Pattern\Expander;

use AppVerk\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;
use Exception;

use function is_string;
use function sprintf;

use const FILTER_VALIDATE_URL;

final class IsUrl implements PatternExpander
{
    use BacktraceBehavior;

    public const string NAME = 'isUrl';

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
            $this->error = sprintf('IsUrl expander require "string", got "%s".', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        if (!$this->matchValue($value)) {
            $this->error = sprintf('string "%s" is not a valid URL.', $value);
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        $this->backtrace->expanderSucceed(self::NAME, $value);

        return true;
    }

    #[\Override]
    public function getError(): ?string
    {
        return $this->error;
    }

    private function matchValue(string $value): bool
    {
        try {
            return false !== filter_var($value, FILTER_VALIDATE_URL);
        } catch (Exception) {
            return false;
        }
    }
}
