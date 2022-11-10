<?php

declare(strict_types=1);

namespace Appverk\PHPMatcher\AST;

final class Type implements Node
{
    private string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function __toString() : string
    {
        return $this->type;
    }
}
