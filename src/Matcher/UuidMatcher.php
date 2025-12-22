<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Parser;
use Coduo\ToString\StringConverter;

use function gettype;
use function is_string;
use function sprintf;

final class UuidMatcher extends Matcher
{
    public const string PATTERN = 'uuid';

    public const string UUID_PATTERN = '[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}';

    public const string UUID_FORMAT_PATTERN = '|^' . self::UUID_PATTERN . '$|';

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
                '%s "%s" is not a valid UUID: not a string.',
                gettype($value),
                new StringConverter($value),
            );
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        if (1 !== preg_match(self::UUID_FORMAT_PATTERN, $value)) {
            $this->error = sprintf(
                '%s "%s" is not a valid UUID: invalid format.',
                gettype($value),
                $value,
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
