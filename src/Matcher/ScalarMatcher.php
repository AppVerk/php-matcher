<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Value\SingleLineString;
use Coduo\ToString\StringConverter;

final class ScalarMatcher extends Matcher
{
    private Backtrace $backtrace;

    public function __construct(Backtrace $backtrace)
    {
        $this->backtrace = $backtrace;
    }

    public function match($value, $pattern) : bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        if ($value !== $pattern) {
            $this->error = \sprintf(
                '"%s" does not match "%s".',
                new SingleLineString((string) new StringConverter($value)),
                new SingleLineString((string) new StringConverter($pattern))
            );
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        $this->backtrace->matcherSucceed(self::class, $value, $pattern);

        return true;
    }

    public function canMatch($pattern) : bool
    {
        $result = \is_scalar($pattern);
        $this->backtrace->matcherCanMatch(self::class, $pattern, $result);

        return $result;
    }
}
