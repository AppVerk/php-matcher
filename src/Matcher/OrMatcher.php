<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher;

use AppVerk\PHPMatcher\Backtrace;

use function is_string;

final class OrMatcher extends Matcher
{
    public const string MATCH_PATTERN = "/\|\|/";

    public function __construct(private readonly Backtrace $backtrace, private readonly ChainMatcher $chainMatcher)
    {
    }

    #[\Override]
    public function match(mixed $value, mixed $pattern): bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        $patterns = explode('||', (string) $pattern);
        $patterns = array_map('trim', $patterns);

        foreach ($patterns as $childPattern) {
            if ($this->matchChild($value, $childPattern)) {
                $this->backtrace->matcherSucceed(self::class, $value, $pattern);

                return true;
            }
        }

        $this->backtrace->matcherFailed(self::class, $value, $pattern, (string) $this->error);

        return false;
    }

    #[\Override]
    public function canMatch(mixed $pattern): bool
    {
        $result = is_string($pattern) && 0 !== preg_match_all(self::MATCH_PATTERN, $pattern, $matches);
        $this->backtrace->matcherCanMatch(self::class, $pattern, $result);

        return $result;
    }

    private function matchChild($value, $pattern): bool
    {
        if (!$this->chainMatcher->canMatch($pattern)) {
            return false;
        }

        return $this->chainMatcher->match($value, $pattern);
    }
}
