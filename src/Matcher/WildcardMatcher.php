<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher;

use AppVerk\PHPMatcher\Backtrace;

use function is_string;

final class WildcardMatcher extends Matcher
{
    public const string MATCH_PATTERN = "/^@(\*|wildcard)@$/";

    public function __construct(private readonly Backtrace $backtrace)
    {
    }

    #[\Override]
    public function match(mixed $value, mixed $pattern): bool
    {
        $this->backtrace->matcherSucceed(self::class, $value, $pattern);

        return true;
    }

    #[\Override]
    public function canMatch(mixed $pattern): bool
    {
        $result = is_string($pattern) && 0 !== preg_match(self::MATCH_PATTERN, $pattern);
        $this->backtrace->matcherCanMatch(self::class, $pattern, $result);

        return $result;
    }
}
