<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\Pattern\Expander;

use AppVerk\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;
use InvalidArgumentException;

use function is_float;
use function is_int;
use function sprintf;

final class GreaterThan implements PatternExpander
{
    use BacktraceBehavior;

    public const string NAME = 'greaterThan';

    private ?string $error = null;

    public function __construct(private readonly float|int $boundary)
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

        if (!is_float($value) && !is_int($value) && !is_numeric($value)) {
            $this->error = sprintf('Value "%s" is not a valid number.', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        if ($value <= $this->boundary) {
            $this->error = sprintf('Value "%s" is not greater than "%s".', new StringConverter($value), new StringConverter($this->boundary));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        $result = $value > $this->boundary;

        if ($result) {
            $this->backtrace->expanderSucceed(self::NAME, $value);
        } else {
            $this->backtrace->expanderFailed(self::NAME, $value, '');
        }

        return $result;
    }

    #[\Override]
    public function getError(): ?string
    {
        return $this->error;
    }
}
