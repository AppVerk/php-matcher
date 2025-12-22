<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Backtrace;

use AppVerk\PHPMatcher\Backtrace;

final class VoidBacktrace implements Backtrace
{
    #[\Override]
    public function __toString(): string
    {
        return 'Empty';
    }

    #[\Override]
    public function matcherCanMatch(string $name, $value, bool $result): void
    {
    }

    #[\Override]
    public function matcherEntrance(string $name, $value, $pattern): void
    {
    }

    #[\Override]
    public function matcherSucceed(string $name, $value, $pattern): void
    {
    }

    #[\Override]
    public function matcherFailed(string $name, $value, $pattern, string $error): void
    {
    }

    #[\Override]
    public function expanderEntrance(string $name, $value): void
    {
    }

    #[\Override]
    public function expanderSucceed(string $name, $value): void
    {
    }

    #[\Override]
    public function expanderFailed(string $name, $value, string $error): void
    {
    }

    #[\Override]
    public function isEmpty(): bool
    {
        return true;
    }

    
    #[\Override]
    public function raw(): array
    {
        return [];
    }

    #[\Override]
    public function last(): ?string
    {
        return null;
    }
}
