<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher;

use AppVerk\PHPMatcher\Matcher\ValueMatcher;

final readonly class Matcher
{
    public function __construct(
        private ValueMatcher $valueMatcher,
        private Backtrace $backtrace
    ) {
    }

    public function match(mixed $value, mixed $pattern): bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        $result = $this->valueMatcher->match($value, $pattern);

        if ($result) {
            $this->backtrace->matcherSucceed(self::class, $value, $pattern);
            $this->valueMatcher->clearError();
        } else {
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->valueMatcher->getError());
        }

        return $result;
    }

    public function getError(): ?string
    {
        return $this->valueMatcher->getError();
    }

    public function backtrace(): Backtrace
    {
        return $this->backtrace;
    }
}
