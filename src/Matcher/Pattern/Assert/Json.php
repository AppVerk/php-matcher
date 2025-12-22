<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\Pattern\Assert;

use function is_float;
use function is_string;

use const JSON_ERROR_CTRL_CHAR;
use const JSON_ERROR_DEPTH;
use const JSON_ERROR_NONE;
use const JSON_ERROR_STATE_MISMATCH;
use const JSON_ERROR_SYNTAX;
use const JSON_ERROR_UTF8;

final class Json
{
    private const string TRANSFORM_NEW_LINES = '/\r?\n|\r/';

    private const string TRANSFORM_QUOTATION_PATTERN = '/([^"])@([a-zA-Z0-9\.]+)@([^"])/';

    private const string TRANSFORM_QUOTATION_REPLACEMENT = '$1"@$2@"$3';

    public static function isValid($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        $result = json_decode($value);

        if (is_float($result) && is_infinite($result)) {
            return false;
        }

        if (null === $result && JSON_ERROR_NONE !== json_last_error()) {
            return false;
        }

        return true;
    }

    public static function isValidPattern($value): bool
    {
        if (!is_string($value)) {
            return false;
        }
        if (self::isValid($value)) {
            return true;
        }
        return self::isValid(self::transformPattern($value));
    }

    public static function transformPattern(string $pattern): ?string
    {
        return preg_replace(
            self::TRANSFORM_NEW_LINES,
            '',
            (string) preg_replace(
                self::TRANSFORM_QUOTATION_PATTERN,
                self::TRANSFORM_QUOTATION_REPLACEMENT,
                $pattern,
            ),
        );
    }

    public static function reformat(string $json): string
    {
        return json_encode(json_decode($json, true));
    }

    public static function getErrorMessage(): string
    {
        return match (json_last_error()) {
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
            JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded',
            default => 'Unknown error',
        };
    }
}
