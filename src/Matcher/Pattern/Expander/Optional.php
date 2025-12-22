<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\Pattern\Expander;

use AppVerk\PHPMatcher\Matcher\Pattern\PatternExpander;

final class Optional implements PatternExpander
{
    use BacktraceBehavior;

    public const string NAME = 'optional';

    #[\Override]
    public static function is(string $name): bool
    {
        return self::NAME === $name;
    }

    #[\Override]
    public function match(mixed $value): bool
    {
        $this->backtrace->expanderEntrance(self::NAME, $value);
        $this->backtrace->expanderSucceed(self::NAME, $value);

        return true;
    }

    #[\Override]
    public function getError(): ?string
    {
        return null;
    }
}
