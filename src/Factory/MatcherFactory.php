<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Factory;

use AppVerk\PHPMatcher\Matcher\ChainMatcher;
use AppVerk\PHPMatcher\Matcher\JsonMatcher;
use LSS\XML2Array;
use AppVerk\PHPMatcher\Matcher\XmlMatcher;
use AppVerk\PHPMatcher\Matcher\TextMatcher;
use AppVerk\PHPMatcher\Matcher\ArrayMatcher;
use AppVerk\PHPMatcher\Matcher\OrMatcher;
use AppVerk\PHPMatcher\Matcher\CallbackMatcher;
use AppVerk\PHPMatcher\Matcher\ExpressionMatcher;
use AppVerk\PHPMatcher\Matcher\NullMatcher;
use AppVerk\PHPMatcher\Matcher\StringMatcher;
use AppVerk\PHPMatcher\Matcher\IntegerMatcher;
use AppVerk\PHPMatcher\Matcher\BooleanMatcher;
use AppVerk\PHPMatcher\Matcher\DoubleMatcher;
use AppVerk\PHPMatcher\Matcher\NumberMatcher;
use AppVerk\PHPMatcher\Matcher\TimeMatcher;
use AppVerk\PHPMatcher\Matcher\DateMatcher;
use AppVerk\PHPMatcher\Matcher\DateTimeMatcher;
use AppVerk\PHPMatcher\Matcher\TimeZoneMatcher;
use AppVerk\PHPMatcher\Matcher\ScalarMatcher;
use AppVerk\PHPMatcher\Matcher\WildcardMatcher;
use AppVerk\PHPMatcher\Matcher\UuidMatcher;
use AppVerk\PHPMatcher\Matcher\UlidMatcher;
use AppVerk\PHPMatcher\Matcher\JsonObjectMatcher;
use AppVerk\PHPMatcher\Matcher\EnumMatcher;
use AppVerk\PHPMatcher\Matcher\TaxNumberMatcher;
use AppVerk\PHPMatcher\Matcher\RegonMatcher;
use AppVerk\PHPMatcher\Parser\ExpanderInitializer;
use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Factory;
use AppVerk\PHPMatcher\Lexer;
use AppVerk\PHPMatcher\Matcher;
use AppVerk\PHPMatcher\Parser;

final class MatcherFactory implements Factory
{
    #[\Override]
    public function createMatcher(Backtrace $backtrace): Matcher
    {
        return new Matcher($this->buildMatchers($this->buildParser($backtrace), $backtrace), $backtrace);
    }

    private function buildMatchers(Parser $parser, Backtrace $backtrace): ChainMatcher
    {
        $scalarMatchers = $this->buildScalarMatchers($parser, $backtrace);
        $arrayMatcher = $this->buildArrayMatcher($scalarMatchers, $parser, $backtrace);

        $matchers = [$scalarMatchers];
        $matchers[] = new JsonMatcher($arrayMatcher, $backtrace);

        if (class_exists(XML2Array::class)) {
            $matchers[] = new XmlMatcher($arrayMatcher, $backtrace);
        }

        $matchers[] = $arrayMatcher;
        $matchers[] = $this->buildOrMatcher($backtrace, $matchers);
        $matchers[] = new TextMatcher($backtrace, $parser);

        return new ChainMatcher(
            'all',
            $backtrace,
            $matchers,
        );
    }

    private function buildArrayMatcher(ChainMatcher $scalarMatchers, Parser $parser, Backtrace $backtrace): ArrayMatcher
    {
        $orMatcher = new OrMatcher($backtrace, $scalarMatchers);

        return new ArrayMatcher(
            new ChainMatcher(
                'array',
                $backtrace,
                [
                    $orMatcher,
                    $scalarMatchers,
                    new TextMatcher($backtrace, $parser),
                ],
            ),
            $backtrace,
            $parser,
        );
    }

    private function buildScalarMatchers(Parser $parser, Backtrace $backtrace): ChainMatcher
    {
        return new ChainMatcher(
            'scalars',
            $backtrace,
            [
                new CallbackMatcher($backtrace),
                new ExpressionMatcher($backtrace),
                new NullMatcher($backtrace),
                new StringMatcher($backtrace, $parser),
                new IntegerMatcher($backtrace, $parser),
                new BooleanMatcher($backtrace, $parser),
                new DoubleMatcher($backtrace, $parser),
                new NumberMatcher($backtrace, $parser),
                new TimeMatcher($backtrace, $parser),
                new DateMatcher($backtrace, $parser),
                new DateTimeMatcher($backtrace, $parser),
                new TimeZoneMatcher($backtrace, $parser),
                new ScalarMatcher($backtrace),
                new WildcardMatcher($backtrace),
                new UuidMatcher($backtrace, $parser),
                new UlidMatcher($backtrace, $parser),
                new JsonObjectMatcher($backtrace, $parser),
                new EnumMatcher($backtrace, $parser),
                new TaxNumberMatcher($backtrace),
                new RegonMatcher($backtrace, $parser),
            ],
        );
    }

    private function buildOrMatcher(Backtrace $backtrace, array $orMatchers): OrMatcher
    {
        return new OrMatcher(
            $backtrace,
            new ChainMatcher(
                'or',
                $backtrace,
                $orMatchers,
            ),
        );
    }

    private function buildParser(Backtrace $backtrace): Parser
    {
        return new Parser(new Lexer(), new ExpanderInitializer($backtrace));
    }
}
