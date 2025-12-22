<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\AST;

final readonly class Type implements Node, \Stringable
{
    public function __construct(private string $type)
    {
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->type;
    }
}
