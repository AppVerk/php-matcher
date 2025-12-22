<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\Pattern\Expander;

use AppVerk\PHPMatcher\Factory\MatcherFactory;
use AppVerk\PHPMatcher\Matcher;
use AppVerk\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;
use InvalidArgumentException;

use function array_key_exists;
use function count;
use function is_array;
use function is_string;
use function sprintf;

use const JSON_ERROR_NONE;

final class Repeat implements PatternExpander
{
    use BacktraceBehavior;

    public const string NAME = 'repeat';

    private ?string $error = null;

    private bool $isScalar = true;

    
    public function __construct(private $pattern, private bool $isStrict = true)
    {
        if (is_string($this->pattern)) {
            $json = json_decode($this->pattern, true);

            if ($json !== null && json_last_error() === JSON_ERROR_NONE) {
                $this->pattern = $json;
                $this->isScalar = false;
            }
        } elseif (is_array($this->pattern)) {
            $this->isScalar = false;
        } else {
            throw new InvalidArgumentException('Repeat pattern must be a string or an array.');
        }
    }

    #[\Override]
    public static function is(string $name): bool
    {
        return self::NAME === $name;
    }

    #[\Override]
    public function match(mixed $value): bool
    {
        $this->backtrace->expanderEntrance(self::NAME, $value);

        if (!is_array($value)) {
            $this->error = sprintf('Repeat expander require "array", got "%s".', new StringConverter($value));
            $this->backtrace->expanderSucceed(self::NAME, $value);

            return false;
        }

        $factory = new MatcherFactory();
        $matcher = $factory->createMatcher($this->backtrace);

        if ($this->isScalar) {
            $result = $this->matchScalar($value, $matcher);

            if ($result) {
                $this->backtrace->expanderSucceed(self::NAME, $value);
            } else {
                $this->backtrace->expanderFailed(self::NAME, $value, '');
            }

            return $result;
        }

        $result = $this->matchJson($value, $matcher);

        if ($result) {
            $this->backtrace->expanderSucceed(self::NAME, $value);
        } else {
            $this->backtrace->expanderFailed(self::NAME, $value, '');
        }

        return $result;
    }

    #[\Override]
    public function getError(): ?string
    {
        return $this->error;
    }

    private function matchScalar(array $values, Matcher $matcher): bool
    {
        foreach ($values as $index => $value) {
            $match = $matcher->match($value, $this->pattern);

            if (!$match) {
                $this->error = sprintf('Repeat expander, entry n°%d, find error : %s', $index, $matcher->getError());

                return false;
            }
        }

        return true;
    }

    private function matchJson(array $values, Matcher $matcher): bool
    {
        $patternKeys = array_keys($this->pattern);
        $patternKeysLength = count($patternKeys);

        foreach ($values as $index => $value) {
            $valueKeys = array_keys($value);
            $valueKeysLength = count($valueKeys);

            if ($this->isStrict && $patternKeysLength !== $valueKeysLength) {
                $this->error = sprintf('Repeat expander expect to have %d keys in array but get : %d', $patternKeysLength, $valueKeysLength);

                return false;
            }

            foreach ($patternKeys as $key) {
                if (!array_key_exists($key, $value)) {
                    $this->error = sprintf('Repeat expander, entry n°%d, require "array" to have key "%s".', $index, $key);

                    return false;
                }

                $match = $matcher->match($value[$key], $this->pattern[$key]);

                if (!$match) {
                    $this->error = sprintf('Repeat expander, entry n°%d, key "%s", find error : %s', $index, $key, $matcher->getError());

                    return false;
                }
            }
        }

        return true;
    }
}
