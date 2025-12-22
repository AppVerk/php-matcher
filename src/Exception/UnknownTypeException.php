<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Exception;

use function sprintf;

class UnknownTypeException extends Exception
{
    private readonly string $type;

    public function __construct(string $type)
    {
        $this->type = '@' . $type . '@';
        parent::__construct(sprintf('Type pattern "%s" is not supported.', $this->type), 0, null);
    }

    public function getType(): string
    {
        return $this->type;
    }
}
