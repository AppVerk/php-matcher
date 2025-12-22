<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\Pattern;

final class TypePattern implements Pattern
{
    
    private array $expanders = [];

    private ?string $error = null;

    public function __construct(private readonly string $type)
    {
    }

    public function is(string $type): bool
    {
        return strtolower($this->type) === strtolower($type);
    }

    public function getType(): string
    {
        return strtolower($this->type);
    }

    #[\Override]
    public function addExpander(PatternExpander $expander): void
    {
        $this->expanders[] = $expander;
    }

    #[\Override]
    public function matchExpanders(mixed $value): bool
    {
        foreach ($this->expanders as $expander) {
            if (!$expander->match($value)) {
                $this->error = $expander->getError();

                return false;
            }
        }

        return true;
    }

    #[\Override]
    public function getError(): ?string
    {
        return $this->error;
    }

    #[\Override]
    public function hasExpander(string $expanderName): bool
    {
        foreach ($this->expanders as $expander) {
            if ($expander::is($expanderName)) {
                return true;
            }
        }

        return false;
    }
}
