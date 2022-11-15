<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Lexer;
use AppVerk\PHPMatcher\Matcher\EnumMatcher;
use AppVerk\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;

class EnumMatcherTest extends TestCase
{
    private ?EnumMatcher $matcher = null;

    public static function positiveCanMatchData()
    {
        return [
            ['@enum@'],
        ];
    }

    public static function positiveMatchData()
    {
        return [
            ['lorem ipsum', '@enum@'],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ['@enum'],
            ['enum'],
            [1],
        ];
    }

    public static function negativeMatchData()
    {
        return [
            [1, '@enum@'],
            [0, '@enum@'],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [new \stdClass,  '@enum@', 'object "\\stdClass" is not a valid string.'],
            [1.1, '@integer@', 'double "1.1" is not a valid string.'],
            [false, '@double@', 'boolean "false" is not a valid string.'],
            [1, '@array@', 'integer "1" is not a valid string.'],
        ];
    }

    public function setUp() : void
    {
        $this->matcher = new EnumMatcher(
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
