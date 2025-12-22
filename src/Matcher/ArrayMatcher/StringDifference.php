<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\ArrayMatcher;

final readonly class StringDifference implements Difference
{
    public function __construct(private string $description)
    {
    }

    #[\Override]
    public function format(): string
    {
        return $this->description;
    }
}
