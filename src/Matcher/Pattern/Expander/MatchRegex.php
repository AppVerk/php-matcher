<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\Pattern\Expander;

use AppVerk\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;
use InvalidArgumentException;

use function is_string;
use function sprintf;

final class MatchRegex implements PatternExpander
{
    use BacktraceBehavior;

    public const string NAME = 'matchRegex';

    private ?string $error = null;

    private string $pattern;

    public function __construct(string $pattern)
    {
        if (!is_string($pattern)) {
            throw new InvalidArgumentException('Regex pattern must be a string.');
        }

        if (@preg_match($pattern, '') === false) {
            throw new InvalidArgumentException('Regex pattern must be a valid one.');
        }

        $this->pattern = $pattern;
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

        if (!is_string($value)) {
            $this->error = sprintf('Match expander require "string", got "%s".', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        if (1 !== preg_match($this->pattern, $value)) {
            $this->error = sprintf("string \"%s\" don't match pattern %s.", $value, $this->pattern);
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
