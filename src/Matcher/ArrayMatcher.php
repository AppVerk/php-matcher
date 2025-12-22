<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Exception\Exception;
use AppVerk\PHPMatcher\Matcher\ArrayMatcher\Diff;
use AppVerk\PHPMatcher\Matcher\ArrayMatcher\Difference;
use AppVerk\PHPMatcher\Matcher\ArrayMatcher\StringDifference;
use AppVerk\PHPMatcher\Matcher\ArrayMatcher\ValuePatternDifference;
use AppVerk\PHPMatcher\Parser;
use Coduo\ToString\StringConverter;
use Throwable;

use function array_key_exists;
use function count;
use function in_array;
use function is_array;
use function is_string;
use function sprintf;

use const ARRAY_FILTER_USE_BOTH;

final class ArrayMatcher extends Matcher
{
    public const string PATTERN = 'array';

    public const string UNBOUNDED_PATTERN = '@...@';

    public const string UNIVERSAL_KEY = '@*@';

    public const string ARRAY_PREVIOUS_PATTERN = '@array_previous@';

    public const string ARRAY_PREVIOUS_PATTERN_REPEAT = '@array_previous_repeat@';

    public function __construct(private readonly ValueMatcher $propertyMatcher, private readonly Backtrace $backtrace, private readonly Parser $parser, private Diff $diff = new Diff())
    {
    }

    #[\Override]
    public function match(mixed $value, mixed $pattern): bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        if (parent::match($value, $pattern)) {
            $this->backtrace->matcherSucceed(self::class, $value, $pattern);

            return true;
        }

        if (!is_array($value)) {
            $this->addValuePatternDifference($value, $pattern);

            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->getError());

            return false;
        }

        if ($this->isArrayPattern($pattern)) {
            return $this->allExpandersMatch($value, $pattern);
        }

        if (!$this->iterateMatch($value, $pattern)) {
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->getError());

            return false;
        }

        $this->backtrace->matcherSucceed(self::class, $value, $pattern);

        return true;
    }

    #[\Override]
    public function canMatch(mixed $pattern): bool
    {
        return is_array($pattern) || $this->isArrayPattern($pattern);
    }

    #[\Override]
    public function getError(): ?string
    {
        if ($this->diff->count() === 0) {
            return null;
        }

        return implode("\n", array_map(static fn(Difference $difference): string => $difference->format(), $this->diff->all()));
    }

    #[\Override]
    public function clearError(): void
    {
        $this->diff = new Diff();
    }

    private function isArrayPattern($pattern): bool
    {
        if (!is_string($pattern)) {
            return false;
        }

        return $this->parser->hasValidSyntax($pattern) && $this->parser->parse($pattern)->is(self::PATTERN);
    }

    private function iterateMatch(array $values, array $patterns, string $parentPath = ''): bool
    {
        $pattern = null;
        $previousPattern = null;

        
        if (in_array(self::ARRAY_PREVIOUS_PATTERN_REPEAT, $patterns, true)) {
            $patterns = array_merge(
                array_replace($patterns, [array_search(self::ARRAY_PREVIOUS_PATTERN_REPEAT, $patterns, true) => self::ARRAY_PREVIOUS_PATTERN]),
                array_fill(0, count($values) - count($patterns), self::ARRAY_PREVIOUS_PATTERN),
            );
        }

        foreach ($values as $key => $value) {
            $path = $this->formatAccessPath($key);

            if ($this->shouldSkipValueMatchingFor($pattern)) {
                continue;
            }

            if ($this->valueExist($path, $patterns)) {
                $pattern = $this->getValueByPath($patterns, $path);
            } elseif (isset($patterns[self::UNIVERSAL_KEY])) {
                $pattern = $patterns[self::UNIVERSAL_KEY];
            } else {
                $this->setMissingElementInError('pattern', $this->formatFullPath($parentPath, $path));

                return false;
            }

            if ($pattern === self::ARRAY_PREVIOUS_PATTERN) {
                $pattern = $previousPattern;
            }

            if ($this->shouldSkipValueMatchingFor($pattern)) {
                continue;
            }

            if ($this->valueMatchPattern($value, $pattern, $this->formatFullPath($parentPath, $path))) {
                continue;
            }

            if (!is_array($value)) {
                return false;
            }

            if (!$this->canMatch($pattern)) {
                $this->addValuePatternDifference($value, $parentPath, $this->formatFullPath($parentPath, $path));

                return false;
            }

            if ($this->isArrayPattern($pattern)) {
                if (!$this->allExpandersMatch($value, $pattern, $parentPath)) {
                    $this->addValuePatternDifference($value, $parentPath, $this->formatFullPath($parentPath, $path));

                    return false;
                }

                continue;
            }

            if (!$this->iterateMatch($value, $pattern, $this->formatFullPath($parentPath, $path))) {
                return false;
            }

            $previousPattern = $pattern;
        }

        return $this->isPatternValid($patterns, $values, $parentPath);
    }

    private function isPatternValid(array $pattern, array $values, string $parentPath): bool
    {
        $skipPattern = self::UNBOUNDED_PATTERN;

        $pattern = array_filter(
            $pattern,
            static fn($item): bool => $item !== $skipPattern,
        );

        $notExistingKeys = $this->findNotExistingKeys($pattern, $values);

        if ($notExistingKeys !== []) {
            $keyNames = array_keys($notExistingKeys);
            $path = $this->formatFullPath($parentPath, $this->formatAccessPath($keyNames[0]));
            $this->setMissingElementInError('value', $path);

            return false;
        }

        return true;
    }

    
    private function findNotExistingKeys(array $patterns, array $values): array
    {
        $notExistingKeys = array_diff_key($patterns, $values);

        return array_filter($notExistingKeys, function($pattern, $key) use ($values): bool {
            if ($key === self::UNIVERSAL_KEY) {
                return false;
            }

            if (is_array($pattern)) {
                return $pattern === [] || !$this->match($values, $pattern);
            }

            try {
                $typePattern = $this->parser->parse($pattern);
            } catch (Exception|Throwable) {
                return true;
            }

            return !$typePattern->hasExpander('optional');
        }, ARRAY_FILTER_USE_BOTH);
    }

    private function valueMatchPattern($value, $pattern, string $parentPath): bool
    {
        $match = $this->propertyMatcher->canMatch($pattern)
            && $this->propertyMatcher->match($value, $pattern);

        if (!$match && !is_array($value)) {
            $this->addValuePatternDifference($value, $pattern, $parentPath);
        }

        return $match;
    }

    private function valueExist(string $path, array $haystack): bool
    {
        return $this->arrayPropertyExists($this->getKeyFromAccessPath($path), $haystack);
    }

    private function arrayPropertyExists(string $property, array $objectOrArray): bool
    {
        return isset($objectOrArray[$property])
            || array_key_exists($property, $objectOrArray);
    }

    private function getValueByPath(array $array, string $path)
    {
        return $array[$this->getKeyFromAccessPath($path)];
    }

    private function setMissingElementInError(string $place, string $path): void
    {
        $this->diff = $this->diff->add(new StringDifference(sprintf('There is no element under path %s in %s.', $path, $place)));
    }

    private function formatAccessPath(int|string $key): string
    {
        return sprintf('[%s]', $key);
    }

    private function getKeyFromAccessPath(string $path): string
    {
        return substr($path, 1, -1);
    }

    private function formatFullPath(string $parentPath, string $path): string
    {
        return sprintf('%s%s', $parentPath, $path);
    }

    private function shouldSkipValueMatchingFor($lastPattern): bool
    {
        return $lastPattern === self::UNBOUNDED_PATTERN;
    }

    private function allExpandersMatch(array $value, $pattern, string $parentPath = ''): bool
    {
        $typePattern = $this->parser->parse($pattern);

        if (!$typePattern->matchExpanders($value)) {
            $this->addValuePatternDifference($value, $pattern, $parentPath);

            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->getError());

            return false;
        }

        $this->backtrace->matcherSucceed(self::class, $value, $pattern);

        return true;
    }

    private function addValuePatternDifference($value, $pattern, string $path = ''): void
    {
        $this->diff = $this->diff->add(new ValuePatternDifference(
            (string) new StringConverter($value),
            (string) new StringConverter($pattern),
            $path ?: 'root',
        ));
    }
}
