<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\Pattern\Expander;

use AppVerk\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

use function is_string;
use function sprintf;

final class Contains implements PatternExpander
{
    use BacktraceBehavior;

    public const string NAME = 'contains';

    private ?string $error = null;

    public function __construct(private string $string, private bool $ignoreCase = false)
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

        if (!is_string($value)) {
            $this->error = sprintf('Contains expander require "string", got "%s".', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        $contains = $this->ignoreCase
            ? mb_stripos($value, $this->string)
            : mb_strpos($value, $this->string);

        if ($contains === false) {
            $this->error = sprintf("String \"%s\" doesn't contains \"%s\".", $value, $this->string);
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
