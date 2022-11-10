<?php

declare(strict_types=1);

namespace Appverk\PHPMatcher\Matcher;

use Appverk\PHPMatcher\Backtrace;
use Appverk\PHPMatcher\Parser;
use Coduo\ToString\StringConverter;

class RegonMatcher extends Matcher
{
    public const PATTERN = 'regon';
    private const REGEX = '/^[0-9]{9,14}$/';
    private const SHORT_REGON_LENGTH = 9;
    private const LONG_REGON_LENGTH = 14;

    private Backtrace $backtrace;

    private Parser $parser;

    public function __construct(Backtrace $backtrace, Parser $parser)
    {
        $this->backtrace = $backtrace;
        $this->parser = $parser;
    }

    public function match($value, $pattern): bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        if (!\is_numeric($value)) {
            $this->error = \sprintf('%s "%s" is not a valid regon number.', \gettype($value), new StringConverter($value));
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }
        $value = (string) $value;

        if (1 !== preg_match(self::REGEX, $value)) {
            $this->error = \sprintf('"%s" is not a valid regon number.', $value);
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        if (self::SHORT_REGON_LENGTH === strlen($value) && false === $this->validateShortRegonnumber($value)) {
            $this->error = \sprintf('"%s" is not a valid regon number.', $value);
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        if (self::LONG_REGON_LENGTH === strlen($value) && false === $this->validateLongRegonnumber($value)) {
            $this->error = \sprintf('"%s" is not a valid regon number.', $value);
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        $this->backtrace->matcherSucceed(self::class, $value, $pattern);

        return true;
    }

    public function canMatch($pattern): bool
    {
        if (!\is_string($pattern)) {
            $this->backtrace->matcherCanMatch(self::class, $pattern, false);

            return false;
        }

        return sprintf('@%s@', self::PATTERN) === $pattern;
    }

    private function validateShortRegonnumber(string $regon): bool
    {
        $digits = str_split($regon);
        $checksum = (
            8 * intval($digits[0]) +
            9 * intval($digits[1]) +
            2 * intval($digits[2]) +
            3 * intval($digits[3]) +
            4 * intval($digits[4]) +
            5 * intval($digits[5]) +
            6 * intval($digits[6]) +
            7 * intval($digits[7])
        ) % 11;
        $checksum = 10 === $checksum ? 0 : $checksum;

        return intval($digits[8]) == $checksum;
    }

    private function validateLongRegonnumber(string $regon): bool
    {
        $digits = str_split($regon);
        $checksum = (
                2 * intval($digits[0]) +
                4 * intval($digits[1]) +
                8 * intval($digits[2]) +
                5 * intval($digits[3]) +
                0 * intval($digits[4]) +
                9 * intval($digits[5]) +
                7 * intval($digits[6]) +
                3 * intval($digits[7]) +
                6 * intval($digits[8]) +
                1 * intval($digits[9]) +
                2 * intval($digits[10]) +
                4 * intval($digits[11]) +
                8 * intval($digits[12])
            ) % 11;
        $checksum = 10 === $checksum ? 0 : $checksum;

        return (intval($digits[13]) == $checksum);
    }
}
