<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\ArrayMatcher;

use function count;

final readonly class Diff
{
    
    private array $differences;

    public function __construct(Difference ...$difference)
    {
        $this->differences = $difference;
    }

    public function add(Difference $difference): self
    {
        return new self(...array_merge($this->differences, [$difference]));
    }

    
    public function all(): array
    {
        return $this->differences;
    }

    public function count(): int
    {
        return count($this->differences);
    }
}
