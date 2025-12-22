<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Lexer;
use AppVerk\PHPMatcher\Matcher\DoubleMatcher;
use AppVerk\PHPMatcher\Parser;
use DateTime;
use PHPUnit\Framework\TestCase;
use stdClass;

class DoubleMatcherTest extends TestCase
{
    private ?DoubleMatcher $matcher = null;

    public static function positiveCanMatchData()
    {
        return [
            ['@double@'],
        ];
    }

    public static function positiveMatchData()
    {
        return [
            [10.1, '@double@'],
            [10.1, '@double@.lowerThan(50.12).greaterThan(10)'],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ['@double'],
            ['double'],
            [1],
        ];
    }

    public static function negativeMatchData()
    {
        return [
            ['1', '@double@'],
            [new DateTime(),  '@double@'],
            [10,  '@double@'],
            [4.9, '@double@.greaterThan(5)'],
            [4.9, '@double@.lowerThan(20).greaterThan(5)'],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [new stdClass(),  '@integer@', 'object "\\stdClass" is not a valid double.'],
            [25, '@integer@', 'integer "25" is not a valid double.'],
            [false, '@integer@', 'boolean "false" is not a valid double.'],
            [['test'], '@integer@', 'array "Array(1)" is not a valid double.'],
        ];
    }

    protected function setUp(): void
    {
        $this->matcher = new DoubleMatcher(
            $backtrace = new Backtrace\InMemoryBacktrace(),
            new Parser(new Lexer(), new Parser\ExpanderInitializer($backtrace)),
        );
    }

    #[DataProvider('positiveCanMatchData')]
    public function testPositiveCanMatches($pattern): void
    {
        $this->assertTrue($this->matcher->canMatch($pattern));
    }

    #[DataProvider('negativeCanMatchData')]
    public function testNegativeCanMatches($pattern): void
    {
        $this->assertFalse($this->matcher->canMatch($pattern));
    }

    #[DataProvider('positiveMatchData')]
    public function testPositiveMatch($value, $pattern): void
    {
        $this->assertTrue($this->matcher->match($value, $pattern));
    }

    #[DataProvider('negativeMatchData')]
    public function testNegativeMatch($value, $pattern): void
    {
        $this->assertFalse($this->matcher->match($value, $pattern));
    }

    #[DataProvider('negativeMatchDescription')]
    public function testNegativeMatchDescription($value, $pattern, $error): void
    {
        $this->matcher->match($value, $pattern);
        $this->assertEquals($error, $this->matcher->getError());
    }
}
