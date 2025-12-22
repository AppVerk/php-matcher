<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\ArrayMatcher;

final readonly class ValuePatternDifference implements Difference
{
    public function __construct(private string $value, private string $pattern, private string $path)
    {
    }

    #[\Override]
    public function format(): string
    {
        return "Value \"{$this->value}\" does not match pattern \"{$this->pattern}\" at path: \"{$this->path}\"";
    }
}
