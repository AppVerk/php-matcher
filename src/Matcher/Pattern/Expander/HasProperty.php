<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\Pattern\Expander;

use AppVerk\PHPMatcher\Matcher\Pattern\Assert\Json;
use AppVerk\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

use function array_key_exists;
use function is_array;
use function sprintf;

final class HasProperty implements PatternExpander
{
    use BacktraceBehavior;

    public const string NAME = 'hasProperty';

    private ?string $error = null;

    public function __construct(private string $propertyName)
    {
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

        if (is_array($value)) {
            $hasProperty = array_key_exists($this->propertyName, $value);

            if (!$hasProperty) {
                $this->error = sprintf('"json" object "%s" does not have "%s" propety.', new StringConverter($value), new StringConverter($this->propertyName));
                $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

                return false;
            }

            $this->backtrace->expanderSucceed(self::NAME, $value);

            return true;
        }

        if (!Json::isValid($value)) {
            $this->error = sprintf('HasProperty expander require valid "json" string, got "%s".', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        $jsonArray = json_decode(Json::reformat($value), true);

        $hasProperty = array_key_exists($this->propertyName, $jsonArray);

        if (!$hasProperty) {
            $this->error = sprintf('"json" object "%s" does not have "%s" propety.', new StringConverter($value), new StringConverter($this->propertyName));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        $this->backtrace->expanderSucceed(self::NAME, $value);

        return true;
    }

    #[\Override]
    public function getError(): ?string
    {
        return $this->error;
    }
}
