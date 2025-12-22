<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher;

use AppVerk\PHPMatcher\Backtrace\VoidBacktrace;
use AppVerk\PHPMatcher\Factory\MatcherFactory;

final class PHPMatcher
{
    private ?Matcher $matcher = null;

    private readonly Backtrace $backtrace;

    public function __construct(?Backtrace $backtrace = null)
    {
        $this->backtrace = $backtrace ?? new VoidBacktrace();
    }

    public function match($value, $pattern): bool
    {
        $this->matcher = null;

        return $this->getMatcher()->match($value, $pattern);
    }

    
    public function backtrace(): Backtrace
    {
        return $this->backtrace;
    }

    
    public function error(): ?string
    {
        return $this->getMatcher()->getError();
    }

    private function getMatcher(): Matcher
    {
        if (!$this->matcher instanceof Matcher) {
            $this->matcher = (new MatcherFactory())->createMatcher($this->backtrace());
        }

        return $this->matcher;
    }
}
