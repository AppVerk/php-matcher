<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\Pattern\Assert\Json;
use AppVerk\PHPMatcher\Parser;
use Coduo\ToString\StringConverter;

use function gettype;
use function is_array;
use function is_string;
use function sprintf;

final class JsonObjectMatcher extends Matcher
{
    public const string JSON_PATTERN = 'json';

    public function __construct(private readonly Backtrace $backtrace, private readonly Parser $parser)
    {
    }

    #[\Override]
    public function match(mixed $value, mixed $pattern): bool
    {
        if (!$this->isJsonPattern($pattern)) {
            $this->error = sprintf('%s "%s" is not a valid json.', gettype($value), new StringConverter($value));
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        if (!Json::isValid($value) && null !== $value && !is_array($value)) {
            $this->error = sprintf('Invalid given JSON of value. %s', Json::getErrorMessage());
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        return $this->allExpandersMatch($value, $pattern);
    }

    #[\Override]
    public function canMatch(mixed $pattern): bool
    {
        $result = is_string($pattern) && $this->isJsonPattern($pattern);
        $this->backtrace->matcherCanMatch(self::class, $pattern, $result);

        return $result;
    }

    private function isJsonPattern($pattern): bool
    {
        if (!is_string($pattern)) {
            return false;
        }

        return $this->parser->hasValidSyntax($pattern) && $this->parser->parse($pattern)->is(self::JSON_PATTERN);
    }

    private function allExpandersMatch($value, $pattern): bool
    {
        $typePattern = $this->parser->parse($pattern);

        if (!$typePattern->matchExpanders($value)) {
            $this->error = $typePattern->getError();
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        $this->backtrace->matcherSucceed(self::class, $value, $pattern);

        return true;
    }
}
