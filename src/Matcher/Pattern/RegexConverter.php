<?php

declare(strict_types=1);

namespace Appverk\PHPMatcher\Matcher\Pattern;

use Appverk\PHPMatcher\Exception\UnknownTypeException;
use Appverk\PHPMatcher\Matcher\UlidMatcher;
use Appverk\PHPMatcher\Matcher\UuidMatcher;

final class RegexConverter
{
    public function toRegex(TypePattern $typePattern) : string
    {
        switch ($typePattern->getType()) {
            case 'string':
            case 'wildcard':
            case '*':
                return '(.+)';
            case 'number':
                return '(\\-?[0-9]*[\\.|\\,]?[0-9]*)';
            case 'integer':
                return '(\\-?[0-9]*)';
            case 'double':
                return '(\\-?[0-9]*[\\.|\\,][0-9]*)';
            case 'uuid':
                return '(' . UuidMatcher::UUID_PATTERN . ')';
            case 'ulid':
                return '(' . UlidMatcher::ULID_PATTERN . ')';

            default:
                throw new UnknownTypeException($typePattern->getType());
        }
    }
}
