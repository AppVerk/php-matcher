<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\Pattern\Expander;

use AppVerk\PHPMatcher\Matcher\Pattern\PatternExpander;
use AppVerk\PHPMatcher\Factory\MatcherFactory;
use AppVerk\PHPMatcher\Matcher;

final class ExpanderMatch implements PatternExpander
{
    use BacktraceBehavior;

    public const string NAME = 'match';

    private ?Matcher $matcher = null;

    
    public function __construct(private $pattern)
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

        if (!$this->matcher instanceof Matcher) {
            $this->matcher = (new MatcherFactory())->createMatcher($this->backtrace);
        }

        $result = $this->matcher->match($value, $this->pattern);

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
        return $this->matcher->getError();
    }
}
