<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher;

use AppVerk\PHPMatcher\Backtrace;
use Coduo\ToString\StringConverter;

use function gettype;
use function is_string;
use function sprintf;

final class NullMatcher extends Matcher
{
    public const string MATCH_PATTERN = '/^@null@$/';

    public function __construct(private readonly Backtrace $backtrace)
    {
    }

    #[\Override]
    public function match(mixed $value, mixed $pattern): bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        if (null !== $value) {
            $this->error = sprintf('%s "%s" does not match null.', gettype($value), new StringConverter($value));
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        $this->backtrace->matcherSucceed(self::class, $value, $pattern);

        return true;
    }

    #[\Override]
    public function canMatch(mixed $pattern): bool
    {
        $result = null === $pattern || (is_string($pattern) && 0 !== preg_match(self::MATCH_PATTERN, $pattern));
        $this->backtrace->matcherCanMatch(self::class, $pattern, $result);

        return $result;
    }
}
