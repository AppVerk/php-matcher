<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\NullMatcher;
use PHPUnit\Framework\TestCase;
use stdClass;

class NullMatcherTest extends TestCase
{
    private ?NullMatcher $matcher = null;

    public static function positiveCanMatchData()
    {
        return [
            ['@null@'],
            [null],
        ];
    }

    public static function positiveMatchData()
    {
        return [
            [null, '@null@'],
            [null, null],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ['@null'],
            ['null'],
            [0],
        ];
    }

    public static function negativeMatchData()
    {
        return [
            ['null', '@null@'],
            [0,  '@null@'],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            ['test', '@null@', 'string "test" does not match null.'],
            [new stdClass(),  '@null@', 'object "\\stdClass" does not match null.'],
            [1.1, '@null@', 'double "1.1" does not match null.'],
            [false, '@null@', 'boolean "false" does not match null.'],
            [1, '@null@', 'integer "1" does not match null.'],
        ];
    }

    protected function setUp(): void
    {
        $this->matcher = new NullMatcher(new Backtrace\InMemoryBacktrace());
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
