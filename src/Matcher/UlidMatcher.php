<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Parser;
use Coduo\ToString\StringConverter;
use Symfony\Component\Validator\Constraints\Ulid;

use function gettype;
use function is_string;
use function sprintf;
use function strlen;

final class UlidMatcher extends Matcher
{
    public const string PATTERN = 'ulid';

    
    public const string ULID_PATTERN = '[01234567]{1}[0123456789ABCDEFGHJKMNPQRSTVWXYZabcdefghjkmnpqrstvwxyz]{25}';

    public function __construct(
        private readonly Backtrace $backtrace,
        private readonly Parser $parser
    ) {
    }

    #[\Override]
    public function match(mixed $value, mixed $pattern): bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        if (!is_string($value)) {
            $this->error = sprintf(
                '%s "%s" is not a valid ULID: not a string.',
                gettype($value),
                new StringConverter($value),
            );
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        if (strlen($value) !== strspn($value, '0123456789ABCDEFGHJKMNPQRSTVWXYZabcdefghjkmnpqrstvwxyz')) {
            $this->error = sprintf(
                '%s "%s" is not a valid ULID: invalid characters.',
                gettype($value),
                new StringConverter($value),
            );
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        if (26 < strlen($value)) {
            $this->error = sprintf(
                '%s "%s" is not a valid ULID: too long.',
                gettype($value),
                new StringConverter($value),
            );
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        if (26 > strlen($value)) {
            $this->error = sprintf(
                '%s "%s" is not a valid ULID: too short.',
                gettype($value),
                new StringConverter($value),
            );
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        if ($value[0] > '7') {
            $this->error = sprintf(
                '%s "%s" is not a valid ULID: overflow.',
                gettype($value),
                new StringConverter($value),
            );
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

        $result = $this->parser->hasValidSyntax($pattern) && $this->parser->parse($pattern)->is(self::PATTERN);
        $this->backtrace->matcherCanMatch(self::class, $pattern, $result);

        return $result;
    }
}
