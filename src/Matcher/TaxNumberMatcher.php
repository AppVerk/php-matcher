<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher;

use AppVerk\PHPMatcher\Backtrace;
use Coduo\ToString\StringConverter;

class TaxNumberMatcher extends Matcher
{
    public const PATTERN = 'tax_number';
    private const REGEX = '/^[0-9]{10}$/';

    private Backtrace $backtrace;

    public function __construct(Backtrace $backtrace)
    {
        $this->backtrace = $backtrace;
    }

    public function match($value, $pattern): bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        if (!\is_string($value)) {
            $this->error = \sprintf('%s "%s" is not a valid tax number.', \gettype($value), new StringConverter($value));
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }
        $value = preg_replace(['/-/', '/ /'], '', $value);

        if (1 !== preg_match(self::REGEX, $value)) {
            $this->error = \sprintf('"%s" is not a valid tax number.', $value);
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        if (false === $this->validateTaxNumber($value)) {
            $this->error = \sprintf('"%s" is not a valid tax number.', $value);
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        $this->backtrace->matcherSucceed(self::class, $value, $pattern);

        return true;
    }

    public function canMatch($pattern): bool
    {
        if (!is_string($pattern)) {
            $this->backtrace->matcherCanMatch(self::class, $pattern, false);

            return false;
        }

        return sprintf('@%s@', self::PATTERN) === $pattern;
    }

    private function validateTaxNumber(string $taxNumber): bool
    {
        $digits = str_split($taxNumber);
        $checksum = (
            6 * intval($digits[0]) +
            5 * intval($digits[1]) +
            7 * intval($digits[2]) +
            2 * intval($digits[3]) +
            3 * intval($digits[4]) +
            4 * intval($digits[5]) +
            5 * intval($digits[6]) +
            6 * intval($digits[7]) +
            7 * intval($digits[8])
            ) % 11;

        return intval($digits[9]) == $checksum;
    }
}
