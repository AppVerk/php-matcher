<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Exception\UnknownTypeException;
use AppVerk\PHPMatcher\Matcher\Pattern\Assert\Json;
use AppVerk\PHPMatcher\Matcher\Pattern\Assert\Xml;
use AppVerk\PHPMatcher\Matcher\Pattern\RegexConverter;
use AppVerk\PHPMatcher\Matcher\Pattern\TypePattern;
use AppVerk\PHPMatcher\Parser;
use Coduo\ToString\StringConverter;

use function count;
use function gettype;
use function is_string;
use function preg_quote;
use function sprintf;

final class TextMatcher extends Matcher
{
    public const string PATTERN_REGEXP = "/@[a-zA-Z\\.]+@(\\.\\w+\\([a-zA-Z0-9{},:@\\.\"'\\(\\)]*\\))*/";

    public const string PATTERN_REGEXP_PLACEHOLDER_TEMPLATE = '__PLACEHOLDER%d__';

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
            $this->error = sprintf('%s "%s" is not a valid string.', gettype($value), new StringConverter($value));
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        $patternRegex = $pattern;
        $patternsReplacedWithRegex = $this->replaceTypePatternsWithPlaceholders($patternRegex);
        $patternRegex = $this->prepareRegex($patternRegex);

        try {
            $patternRegex = $this->replacePlaceholderWithPatternRegexes($patternRegex, $patternsReplacedWithRegex);
        } catch (UnknownTypeException $unknownTypeException) {
            $this->error = sprintf('Type pattern "%s" is not supported by TextMatcher.', $unknownTypeException->getType());
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        if (!preg_match($patternRegex, $value, $matchedValues)) {
            $this->error = sprintf('"%s" does not match "%s" pattern', $value, $pattern);
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        array_shift($matchedValues); 

        if (count($patternsReplacedWithRegex) !== count($matchedValues)) {
            $this->error = 'Unexpected TextMatcher error.';
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        foreach ($patternsReplacedWithRegex as $index => $typePattern) {
            if (!$typePattern->matchExpanders($matchedValues[$index])) {
                $this->error = $typePattern->getError();
                $this->backtrace->matcherFailed(self::class, $value, $pattern, (string) $this->error);

                return false;
            }
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

        if (Json::isValidPattern($pattern)) {
            $this->backtrace->matcherCanMatch(self::class, $pattern, false);

            return false;
        }

        if (Xml::isValid($pattern)) {
            $this->backtrace->matcherCanMatch(self::class, $pattern, false);

            return false;
        }

        $this->backtrace->matcherCanMatch(self::class, $pattern, true);

        return true;
    }

    
    private function replaceTypePatternsWithPlaceholders(string &$patternRegex): array
    {
        $patternsReplacedWithRegex = [];
        preg_match_all(self::PATTERN_REGEXP, $patternRegex, $matches);

        foreach ($matches[0] as $index => $typePatternString) {
            $typePattern = $this->parser->parse($typePatternString);
            $patternsReplacedWithRegex[] = $typePattern;
            $patternRegex = str_replace(
                $typePatternString,
                sprintf(self::PATTERN_REGEXP_PLACEHOLDER_TEMPLATE, $index),
                $patternRegex,
            );
        }

        return $patternsReplacedWithRegex;
    }

    
    private function replacePlaceholderWithPatternRegexes(string $patternRegex, array $patternsReplacedWithRegex): string
    {
        $regexConverter = new RegexConverter();

        foreach ($patternsReplacedWithRegex as $index => $typePattern) {
            $patternRegex = str_replace(
                sprintf(self::PATTERN_REGEXP_PLACEHOLDER_TEMPLATE, $index),
                $regexConverter->toRegex($typePattern),
                $patternRegex,
            );
        }

        return $patternRegex;
    }

    
    private function prepareRegex(string $patternRegex): string
    {
        return '/^' . preg_quote($patternRegex, '/') . '$/';
    }
}
