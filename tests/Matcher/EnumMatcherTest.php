<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Lexer;
use AppVerk\PHPMatcher\Matcher\EnumMatcher;
use AppVerk\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;
use stdClass;

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
            [new stdClass(),  '@enum@', 'object "\\stdClass" is not a valid string.'],
            [1.1, '@integer@', 'double "1.1" is not a valid string.'],
            [false, '@double@', 'boolean "false" is not a valid string.'],
            [1, '@array@', 'integer "1" is not a valid string.'],
        ];
    }

    protected function setUp(): void
    {
        $this->matcher = new EnumMatcher(
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
