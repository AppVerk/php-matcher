<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\AST;

use function count;

final class Pattern implements Node
{
    
    private array $expanders = [];

    public function __construct(private readonly Type $type)
    {
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function hasExpanders(): bool
    {
        return (bool) count($this->expanders);
    }

    
    public function getExpanders(): array
    {
        return $this->expanders;
    }

    public function addExpander(Expander $expander): void
    {
        $this->expanders[] = $expander;
    }
}
