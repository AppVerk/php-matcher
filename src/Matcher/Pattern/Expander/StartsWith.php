<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\Pattern\Expander;

use AppVerk\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;
use InvalidArgumentException;

use function is_string;
use function sprintf;

final class StartsWith implements PatternExpander
{
    use BacktraceBehavior;

    public const string NAME = 'startsWith';

    private string $stringBeginning;

    private ?string $error = null;

    public function __construct(string $stringBeginning, private bool $ignoreCase = false)
    {
        if (!is_string($stringBeginning)) {
            throw new InvalidArgumentException('String beginning must be a valid string.');
        }

        $this->stringBeginning = $stringBeginning;
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
            $this->error = sprintf('StartsWith expander require "string", got "%s".', new StringConverter($value));
            $this->backtrace->expanderSucceed(self::NAME, $value);

            return false;
        }

        if ($this->stringBeginning === '' || $this->stringBeginning === '0') {
            $this->backtrace->expanderSucceed(self::NAME, $value);

            return true;
        }

        if ($this->matchValue($value)) {
            $this->error = sprintf("string \"%s\" doesn't starts with string \"%s\".", $value, $this->stringBeginning);
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
        return $this->ignoreCase
            ? mb_stripos($value, $this->stringBeginning) !== 0
            : mb_strpos($value, $this->stringBeginning) !== 0;
    }
}
