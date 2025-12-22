<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher;

interface ValueMatcher
{
    
    public function match(mixed $value, mixed $pattern): bool;

    
    public function canMatch(mixed $pattern): bool;

    
    public function getError(): ?string;

    
    public function clearError(): void;
}
