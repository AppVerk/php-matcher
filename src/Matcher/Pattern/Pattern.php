<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\Pattern;

interface Pattern
{
    public function addExpander(PatternExpander $expander);

    public function matchExpanders(mixed $value): bool;

    public function getError(): ?string;

    public function hasExpander(string $expanderName): bool;
}
