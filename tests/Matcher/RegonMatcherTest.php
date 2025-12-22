<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Lexer;
use AppVerk\PHPMatcher\Matcher\RegonMatcher;
use AppVerk\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;
use stdClass;

class RegonMatcherTest extends TestCase
{
    private ?RegonMatcher $matcher = null;

    public static function positiveCanMatchData()
    {
        return [
            ['@regon@'],
        ];
    }

    public static function positiveMatchData()
    {
        return [
            ['631378098', '@regon@'],
            ['23511332857188', '@regon@'],
            [631378098, '@regon@'],
            [23511332857188, '@regon@'],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ['@regon'],
            ['regon'],
            [1],
        ];
    }

    public static function negativeMatchData()
    {
        return [
            [65898, '@regon@'],
            [523256587, '@regon@'],
            [52325658823029, '@regon@'],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [new stdClass(),  '@regon@', 'object "\\stdClass" is not a valid regon number.'],
            [1.1, '@integer@', '"1.1" is not a valid regon number.'],
            [false, '@double@', 'boolean "false" is not a valid regon number.'],
            [1, '@array@', '"1" is not a valid regon number.'],
        ];
    }

    protected function setUp(): void
    {
        $this->matcher = new RegonMatcher(
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
