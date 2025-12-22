<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\Pattern\Expander;

use AppVerk\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

use function in_array;
use function is_array;
use function sprintf;

final class InArray implements PatternExpander
{
    use BacktraceBehavior;

    public const string NAME = 'inArray';

    private ?string $error = null;

    public function __construct(private $value)
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
        $this->backtrace->expanderEntrance(self::NAME, $value);

        if (!is_array($value)) {
            $this->error = sprintf('InArray expander require "array", got "%s".', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        if (!in_array($this->value, $value, true)) {
            $this->error = sprintf("%s doesn't have \"%s\" element.", new StringConverter($value), new StringConverter($this->value));
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
}
