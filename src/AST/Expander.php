<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\AST;

use function count;

final class Expander implements Node
{
    
    private array $arguments = [];

    public function __construct(private readonly string $name)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addArgument($argument): void
    {
        $this->arguments[] = $argument;
    }

    public function hasArguments(): bool
    {
        return (bool) count($this->arguments);
    }

    
    public function getArguments(): array
    {
        return $this->arguments;
    }
}
