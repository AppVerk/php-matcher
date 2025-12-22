<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher;

use Doctrine\Common\Lexer\AbstractLexer;

use function in_array;
use function is_string;
use function strlen;

final class Lexer extends AbstractLexer
{
    public const int T_NONE = 1;

    public const int T_EXPANDER_NAME = 2;

    public const int T_CLOSE_PARENTHESIS = 3;

    public const int T_OPEN_CURLY_BRACE = 4;

    public const int T_CLOSE_CURLY_BRACE = 5;

    public const int T_STRING = 6;

    public const int T_NUMBER = 7;

    public const int T_BOOLEAN = 8;

    public const int T_NULL = 9;

    public const int T_COMMA = 10;

    public const int T_COLON = 11;

    public const int T_TYPE_PATTERN = 12;

    
    #[\Override]
    protected function getCatchablePatterns(): array
    {
        return [
            '\\.?[a-zA-Z0-9_]+\\(', 
            '[a-zA-Z0-9.]*', 
            '\\-?[0-9]*\\.?[0-9]*', 
            "'(?:[^']|'')*'", 
            '"(?:[^"]|"")*"', 
            '@[a-zA-Z0-9\\*.]+@', 
        ];
    }

    
    #[\Override]
    protected function getNonCatchablePatterns(): array
    {
        return [
            '\\s+',
        ];
    }

    
    #[\Override]
    protected function getType(string &$value): int
    {
        $type = self::T_NONE;

        if (')' === $value) {
            return self::T_CLOSE_PARENTHESIS;
        }

        if ('{' === $value) {
            return self::T_OPEN_CURLY_BRACE;
        }

        if ('}' === $value) {
            return self::T_CLOSE_CURLY_BRACE;
        }

        if (':' === $value) {
            return self::T_COLON;
        }

        if (',' === $value) {
            return self::T_COMMA;
        }

        if ($this->isTypePatternToken($value)) {
            $value = trim($value, '@');

            return self::T_TYPE_PATTERN;
        }

        if ($this->isStringToken($value)) {
            $value = $this->extractStringValue($value);

            return self::T_STRING;
        }

        if ($this->isBooleanToken($value)) {
            $value = strtolower($value);

            return self::T_BOOLEAN;
        }

        if ($this->isNullToken($value)) {
            $value = 'null';

            return self::T_NULL;
        }

        if (is_numeric($value)) {
            $value = is_string($value) && !str_contains($value, '.') && $value !== '' && $value !== '-' ? (int) $value : $value;
            return self::T_NUMBER;
        }

        if ($this->isExpanderNameToken($value)) {
            $value = rtrim(ltrim($value, '.'), '(');

            return self::T_EXPANDER_NAME;
        }

        return $type;
    }

    protected function isStringToken(string $value): bool
    {
        return in_array(substr($value, 0, 1), ['"', "'"], true);
    }

    protected function isBooleanToken(string $value): bool
    {
        return in_array(strtolower($value), ['true', 'false'], true);
    }

    protected function isNullToken(string $value): bool
    {
        return strtolower($value) === 'null';
    }

    protected function extractStringValue(string $value): string
    {
        return trim(trim($value, "'"), '"');
    }

    protected function isExpanderNameToken(string $value): bool
    {
        return str_ends_with($value, '(') && strlen($value) > 1;
    }

    protected function isTypePatternToken(string $value): bool
    {
        return str_starts_with($value, '@') && substr($value, strlen($value) - 1, 1) === '@' && strlen($value) > 1;
    }
}
