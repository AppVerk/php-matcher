<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher;

abstract class Matcher implements ValueMatcher
{
    protected ?string $error = null;

    #[\Override]
    public function getError(): ?string
    {
        return $this->error;
    }

    #[\Override]
    public function match(mixed $value, mixed $pattern): bool
    {
        return $value === $pattern;
    }

    #[\Override]
    public function canMatch(mixed $pattern): bool
    {
        return false;
    }

    #[\Override]
    public function clearError(): void
    {
        $this->error = null;
    }
}
