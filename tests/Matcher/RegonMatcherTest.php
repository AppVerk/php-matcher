<?php

declare(strict_types=1);

namespace Appverk\PHPMatcher\Tests\Matcher;

use Appverk\PHPMatcher\Backtrace;
use Appverk\PHPMatcher\Lexer;
use Appverk\PHPMatcher\Matcher\RegonMatcher;
use Appverk\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;

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
            [new \stdClass,  '@regon@', 'object "\\stdClass" is not a valid regon number.'],
            [1.1, '@integer@', '"1.1" is not a valid regon number.'],
            [false, '@double@', 'boolean "false" is not a valid regon number.'],
            [1, '@array@', '"1" is not a valid regon number.'],
        ];
    }

    public function setUp() : void
    {
        $this->matcher = new RegonMatcher(
            $backtrace = new Backtrace\InMemoryBacktrace(),
            new Parser(new Lexer(), new Parser\ExpanderInitializer($backtrace))
        );
    }

    /**
     * @dataProvider positiveCanMatchData
     */
    public function test_positive_can_matches($pattern) : void
    {
        $this->assertTrue($this->matcher->canMatch($pattern));
    }

    /**
     * @dataProvider negativeCanMatchData
     */
    public function test_negative_can_matches($pattern) : void
    {
        $this->assertFalse($this->matcher->canMatch($pattern));
    }

    /**
     * @dataProvider positiveMatchData
     */
    public function test_positive_match($value, $pattern) : void
    {
        $this->assertTrue($this->matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchData
     */
    public function test_negative_match($value, $pattern) : void
    {
        $this->assertFalse($this->matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchDescription
     */
    public function test_negative_match_description($value, $pattern, $error) : void
    {
        $this->matcher->match($value, $pattern);
        $this->assertEquals($error, $this->matcher->getError());
    }
}
