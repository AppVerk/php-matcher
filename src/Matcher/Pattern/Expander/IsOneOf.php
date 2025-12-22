<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\Pattern\Expander;

use AppVerk\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

use function is_string;
use function sprintf;

use const PHP_VERSION_ID;

class IsOneOf implements PatternExpander
{
    use BacktraceBehavior;

    public const NAME = 'isOneOf';
    private const int MINIMUM_VERSION_ID = 80100;

    private ?string $error = null;

    public function __construct(private string $class)
    {
    }

    #[\Override]
    public static function is(string $name): bool
    {
        return self::NAME === $name;
    }

    #[\Override]
    public function match(mixed $value): bool
    {
        if (PHP_VERSION_ID < self::MINIMUM_VERSION_ID) {
            $this->error = 'IsOneOf expander require minimum PHP 8.1';
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        $this->backtrace->expanderEntrance(self::NAME, $value);

        if (!is_string($value)) {
            $this->error = sprintf('IsOneOf expander require "string", got "%s".', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        if (!$this->matchValue($value)) {
            $this->error = sprintf('string "%s" is not part of enum "%s".', $value, $this->class);
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
        return null !== $this->class::tryFrom($value);
    }
}
