<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\Pattern\Expander;

use AppVerk\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

class IsOneOf implements PatternExpander
{
    use BacktraceBehavior;

    public const NAME = 'isOneOf';
    private const MINIMUM_VERSION_ID = 80100;

    private string $class;
    private ?string $error = null;

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public static function is(string $name): bool
    {
        return self::NAME === $name;
    }

    public function match($value): bool
    {
        if (\PHP_VERSION_ID < self::MINIMUM_VERSION_ID) {
            $this->error = 'IsOneOf expander require minimum PHP 8.1';
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        $this->backtrace->expanderEntrance(self::NAME, $value);

        if (!\is_string($value)) {
            $this->error = \sprintf('IsOneOf expander require "string", got "%s".', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        if (!$this->matchValue($value)) {
            $this->error = \sprintf("string \"%s\" is not part of enum \"%s\".", $value, $this->class);
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        $this->backtrace->expanderSucceed(self::NAME, $value);

        return true;
    }

    public function getError() : ?string
    {
        return $this->error;
    }

    private function matchValue(string $value) : bool
    {
        return null !== $this->class::tryFrom($value);
    }
}
