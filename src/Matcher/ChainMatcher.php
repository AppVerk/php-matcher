<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\Pattern\Assert\Json;
use AppVerk\PHPMatcher\Value\SingleLineString;
use Coduo\ToString\StringConverter;

use function is_array;
use function sprintf;

final class ChainMatcher extends Matcher
{
    
    private array $matcherErrors = [];

    
    public function __construct(
        private readonly string $name,
        private readonly Backtrace $backtrace,
        private readonly array $matchers = []
    ) {
    }

    #[\Override]
    public function match(mixed $value, mixed $pattern): bool
    {
        $this->backtrace->matcherEntrance($this->matcherName(), $value, $pattern);

        foreach ($this->matchers as $propertyMatcher) {
            if ($propertyMatcher->canMatch($pattern)) {
                if ($propertyMatcher->match($value, $pattern)) {
                    $this->backtrace->matcherSucceed($this->matcherName(), $value, $pattern);

                    return true;
                }

                $this->matcherErrors[$propertyMatcher::class] = (string) $propertyMatcher->getError();
                $this->error = $propertyMatcher->getError();
            }
        }

        if ($this->error === null) {
            if (is_array($value) && isset($this->matcherErrors[ArrayMatcher::class])) {
                $this->error = $this->matcherErrors[ArrayMatcher::class];
            } elseif (Json::isValidPattern($pattern) && isset($this->matcherErrors[JsonMatcher::class])) {
                $this->error = $this->matcherErrors[JsonMatcher::class];
            } else {
                $this->error = sprintf(
                    'Any matcher from chain can\'t match value "%s" to pattern "%s"',
                    new SingleLineString((string) new StringConverter($value)),
                    new SingleLineString((string) new StringConverter($pattern)),
                );
            }
        }

        $this->backtrace->matcherFailed($this->matcherName(), $value, $pattern, $this->error);

        return false;
    }

    #[\Override]
    public function canMatch(mixed $pattern): bool
    {
        $this->backtrace->matcherCanMatch($this->matcherName(), $pattern, true);

        return true;
    }

    private function matcherName(): string
    {
        return sprintf('%s (%s)', self::class, $this->name);
    }
}
