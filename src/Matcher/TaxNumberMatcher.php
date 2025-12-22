<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher;

use AppVerk\PHPMatcher\Backtrace;
use Coduo\ToString\StringConverter;

use function gettype;
use function is_string;
use function sprintf;

class TaxNumberMatcher extends Matcher
{
    public const PATTERN = 'tax_number';
    private const string REGEX = '/^\d{10}$/';

    public function __construct(private readonly Backtrace $backtrace)
    {
    }

    #[\Override]
    public function match(mixed $value, mixed $pattern): bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        if (!is_string($value)) {
            $this->error = sprintf('%s "%s" is not a valid tax number.', gettype($value), new StringConverter($value));
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }
        $value = preg_replace(['/-/', '/ /'], '', $value);

        if (1 !== preg_match(self::REGEX, (string) $value)) {
            $this->error = sprintf('"%s" is not a valid tax number.', $value);
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        if (false === $this->validateTaxNumber($value)) {
            $this->error = sprintf('"%s" is not a valid tax number.', $value);
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

    private function validateTaxNumber(string $taxNumber): bool
    {
        $digits = str_split($taxNumber);
        $checksum = (
            6 * (int) $digits[0] +
            5 * (int) $digits[1] +
            7 * (int) $digits[2] +
            2 * (int) $digits[3] +
            3 * (int) $digits[4] +
            4 * (int) $digits[5] +
            5 * (int) $digits[6] +
            6 * (int) $digits[7] +
            7 * (int) $digits[8]
        ) % 11;

        return (int) $digits[9] == $checksum;
    }
}
