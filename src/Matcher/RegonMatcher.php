<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Parser;
use Coduo\ToString\StringConverter;

use function gettype;
use function is_string;
use function sprintf;
use function strlen;

class RegonMatcher extends Matcher
{
    public const PATTERN = 'regon';
    private const string REGEX = '/^\d{9,14}$/';
    private const int SHORT_REGON_LENGTH = 9;
    private const int LONG_REGON_LENGTH = 14;

    public function __construct(private readonly Backtrace $backtrace)
    {
    }

    #[\Override]
    public function match(mixed $value, mixed $pattern): bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        if (!is_numeric($value)) {
            $this->error = sprintf('%s "%s" is not a valid regon number.', gettype($value), new StringConverter($value));
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }
        $value = (string) $value;

        if (1 !== preg_match(self::REGEX, $value)) {
            $this->error = sprintf('"%s" is not a valid regon number.', $value);
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        if (self::SHORT_REGON_LENGTH === strlen($value) && false === $this->validateShortRegonnumber($value)) {
            $this->error = sprintf('"%s" is not a valid regon number.', $value);
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        if (self::LONG_REGON_LENGTH === strlen($value) && false === $this->validateLongRegonnumber($value)) {
            $this->error = sprintf('"%s" is not a valid regon number.', $value);
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        $this->backtrace->matcherSucceed(self::class, $value, $pattern);

        return true;
    }

    #[\Override]
    public function canMatch(mixed $pattern): bool
    {
        if (!is_string($pattern)) {
            $this->backtrace->matcherCanMatch(self::class, $pattern, false);

            return false;
        }

        return sprintf('@%s@', self::PATTERN) === $pattern;
    }

    private function validateShortRegonnumber(string $regon): bool
    {
        $digits = str_split($regon);
        $checksum = (
            8 * (int) $digits[0] +
            9 * (int) $digits[1] +
            2 * (int) $digits[2] +
            3 * (int) $digits[3] +
            4 * (int) $digits[4] +
            5 * (int) $digits[5] +
            6 * (int) $digits[6] +
            7 * (int) $digits[7]
        ) % 11;
        $checksum = 10 === $checksum ? 0 : $checksum;

        return (int) $digits[8] == $checksum;
    }

    private function validateLongRegonnumber(string $regon): bool
    {
        $digits = str_split($regon);
        $checksum = (
            2 * (int) $digits[0] +
                4 * (int) $digits[1] +
                8 * (int) $digits[2] +
                5 * (int) $digits[3] +
                9 * (int) $digits[5] +
                7 * (int) $digits[6] +
                3 * (int) $digits[7] +
                6 * (int) $digits[8] +
                (int) $digits[9] +
                2 * (int) $digits[10] +
                4 * (int) $digits[11] +
                8 * (int) $digits[12]
        ) % 11;
        $checksum = 10 === $checksum ? 0 : $checksum;

        return (int) $digits[13] == $checksum;
    }
}
