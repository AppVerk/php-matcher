<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Parser;

use AppVerk\PHPMatcher\Matcher\Pattern\Expander\After;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\Before;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\Contains;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\NotContains;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\Count;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\EndsWith;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\GreaterThan;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\InArray;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\IsDateTime;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\IsInDateFormat;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\IsEmail;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\IsEmpty;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\IsNotEmpty;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\IsUrl;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\IsIp;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\IsTzOffset;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\IsTzAbbreviation;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\IsTzIdentifier;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\LowerThan;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\MatchRegex;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\OneOf;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\Optional;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\StartsWith;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\Repeat;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\ExpanderMatch;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\HasProperty;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\IsOneOf;
use AppVerk\PHPMatcher\AST\Expander;
use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Exception\InvalidArgumentException;
use AppVerk\PHPMatcher\Exception\InvalidExpanderTypeException;
use AppVerk\PHPMatcher\Exception\UnknownExpanderClassException;
use AppVerk\PHPMatcher\Exception\UnknownExpanderException;
use AppVerk\PHPMatcher\Matcher\Pattern;
use AppVerk\PHPMatcher\Matcher\Pattern\PatternExpander;
use ReflectionClass;

use function array_key_exists;
use function sprintf;

final class ExpanderInitializer
{
    
    private array $expanderDefinitions = [
        After::NAME            => After::class,
        Before::NAME           => Before::class,
        Contains::NAME         => Contains::class,
        NotContains::NAME      => NotContains::class,
        Count::NAME            => Count::class,
        EndsWith::NAME         => EndsWith::class,
        GreaterThan::NAME      => GreaterThan::class,
        InArray::NAME          => InArray::class,
        IsDateTime::NAME       => IsDateTime::class,
        IsInDateFormat::NAME   => IsInDateFormat::class,
        IsEmail::NAME          => IsEmail::class,
        IsEmpty::NAME          => IsEmpty::class,
        IsNotEmpty::NAME       => IsNotEmpty::class,
        IsUrl::NAME            => IsUrl::class,
        IsIp::NAME             => IsIp::class,
        IsTzOffset::NAME       => IsTzOffset::class,
        IsTzAbbreviation::NAME => IsTzAbbreviation::class,
        IsTzIdentifier::NAME   => IsTzIdentifier::class,
        LowerThan::NAME        => LowerThan::class,
        MatchRegex::NAME       => MatchRegex::class,
        OneOf::NAME            => OneOf::class,
        Optional::NAME         => Optional::class,
        StartsWith::NAME       => StartsWith::class,
        Repeat::NAME           => Repeat::class,
        ExpanderMatch::NAME    => ExpanderMatch::class,
        HasProperty::NAME      => HasProperty::class,
        IsOneOf::NAME          => IsOneOf::class,
    ];

    public function __construct(private readonly Backtrace $backtrace)
    {
    }

    public function setExpanderDefinition(string $expanderName, string $expanderFQCN): void
    {
        if (!class_exists($expanderFQCN)) {
            throw new UnknownExpanderClassException(sprintf('Class "%s" does not exists.', $expanderFQCN));
        }

        $this->expanderDefinitions[$expanderName] = $expanderFQCN;
    }

    public function hasExpanderDefinition(string $expanderName): bool
    {
        return array_key_exists($expanderName, $this->expanderDefinitions);
    }

    public function getExpanderDefinition(string $expanderName): string
    {
        if (!$this->hasExpanderDefinition($expanderName)) {
            throw new InvalidArgumentException(sprintf('Definition for "%s" expander does not exists.', $expanderName));
        }

        return $this->expanderDefinitions[$expanderName];
    }

    public function initialize(Expander $expanderNode): PatternExpander
    {
        if (!array_key_exists($expanderNode->getName(), $this->expanderDefinitions)) {
            throw new UnknownExpanderException(sprintf('Unknown expander "%s"', $expanderNode->getName()));
        }

        $reflection = new ReflectionClass($this->expanderDefinitions[$expanderNode->getName()]);

        if ($expanderNode->hasArguments()) {
            $arguments = [];

            foreach ($expanderNode->getArguments() as $argument) {
                $arguments[] = ($argument instanceof Expander)
                    ? $this->initialize($argument)
                    : $argument;
            }

            $expander = $reflection->newInstanceArgs($arguments);
        } else {
            $expander = $reflection->newInstance();
        }

        if (!$expander instanceof PatternExpander) {
            throw new InvalidExpanderTypeException();
        }

        $expander->setBacktrace($this->backtrace);

        return $expander;
    }
}
