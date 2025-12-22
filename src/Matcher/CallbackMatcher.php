<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher;

use AppVerk\PHPMatcher\Backtrace;
use Coduo\ToString\StringConverter;

use function is_callable;
use function is_object;
use function sprintf;

final class CallbackMatcher extends Matcher
{
    public function __construct(private readonly Backtrace $backtrace)
    {
    }

    #[\Override]
    public function match(mixed $value, mixed $pattern): bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);
        $result = (bool) $pattern->__invoke($value);

        if ($result) {
            $this->backtrace->matcherSucceed(self::class, $value, $pattern);
        } else {
            $this->error = sprintf('Callback matcher failed for value %s', new StringConverter($value));
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);
        }

        return $result;
    }

    #[\Override]
    public function canMatch(mixed $pattern): bool
    {
        $result = is_object($pattern) && is_callable($pattern);
        $this->backtrace->matcherCanMatch(self::class, $pattern, $result);

        return $result;
    }
}
