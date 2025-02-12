<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Lexer;
use AppVerk\PHPMatcher\Matcher\UuidMatcher;
use AppVerk\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;

class UuidMatcherTest extends TestCase
{
    private ?UuidMatcher $matcher = null;

    public static function positiveCanMatchData()
    {
        return [
            ['@uuid@'],
        ];
    }

    public static function positiveMatchData()
    {
        return [
            ['21627164-acb7-11e6-80f5-76304dec7eb7', '@uuid@'],
            ['d9c04bc2-173f-2cb7-ad4e-e4ca3b2c273f', '@uuid@'],
            ['7b368038-a5ca-3aa3-b0db-1177d1761c9e', '@uuid@'],
            ['9f4db639-0e87-4367-9beb-d64e3f42ae18', '@uuid@'],
            ['1f2b1a18-81a0-5685-bca7-f23022ed7c7b', '@uuid@'],
            ['1ebb5050-b028-616a-9180-0a00ac070060', '@uuid@'],
            ['00000000-0000-0000-0000-000000000000', '@uuid@'],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ['@uuid'],
            ['uuid'],
            [1],
        ];
    }

    public static function negativeMatchData()
    {
        return [
            [1, '@uuid@'],
            [0, '@uuid@'],
            ['9f4d-b639-0e87-4367-9beb-d64e3f42ae18', '@uuid@'],
            ['9f4db639-0e87-4367-9beb-d64e3f42ae1', '@uuid@'],
            ['9f4db639-0e87-4367-9beb-d64e3f42ae181', '@uuid@'],
            ['9f4db6390e8743679bebd64e3f42ae18', '@uuid@'],
            ['9f4db6390e87-4367-9beb-d64e-3f42ae18', '@uuid@'],
            ['9f4db639-0e87-4367-9beb-d64e3f42ae1g', '@uuid@'],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [new \stdClass,  '@uuid@', 'object "\\stdClass" is not a valid UUID: not a string.'],
            [1.1, '@uuid@', 'double "1.1" is not a valid UUID: not a string.'],
            [false, '@uuid@', 'boolean "false" is not a valid UUID: not a string.'],
            [1, '@uuid@', 'integer "1" is not a valid UUID: not a string.'],
            ['lorem ipsum', '@uuid@', 'string "lorem ipsum" is not a valid UUID: invalid format.'],
            ['9f4db639-0e87-4367-9beb-d64e3f42ae1z', '@uuid@', 'string "9f4db639-0e87-4367-9beb-d64e3f42ae1z" is not a valid UUID: invalid format.'],
        ];
    }

    public function setUp() : void
    {
        $this->matcher = new UuidMatcher(
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
