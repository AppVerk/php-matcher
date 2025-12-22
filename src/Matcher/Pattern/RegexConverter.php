<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\Pattern;

use AppVerk\PHPMatcher\Exception\UnknownTypeException;
use AppVerk\PHPMatcher\Matcher\UlidMatcher;
use AppVerk\PHPMatcher\Matcher\UuidMatcher;

final class RegexConverter
{
    public function toRegex(TypePattern $typePattern): string
    {
        return match ($typePattern->getType()) {
            'string', 'wildcard', '*' => '(.+)',
            'number' => '(\-?\d*[\.|\,]?\d*)',
            'integer' => '(\-?\d*)',
            'double' => '(\-?\d*[\.|\,]\d*)',
            'uuid' => '(' . UuidMatcher::UUID_PATTERN . ')',
            'ulid' => '(' . UlidMatcher::ULID_PATTERN . ')',
            default => throw new UnknownTypeException($typePattern->getType()),
        };
    }
}
