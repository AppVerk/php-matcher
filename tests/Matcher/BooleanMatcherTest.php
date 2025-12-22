<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Lexer;
use AppVerk\PHPMatcher\Matcher\BooleanMatcher;
use AppVerk\PHPMatcher\Parser;
use DateTime;
use PHPUnit\Framework\TestCase;
use stdClass;

class BooleanMatcherTest extends TestCase
{
    private BooleanMatcher $matcher;

    private Backtrace $backtrace;

    public static function positiveCanMatchData()
    {
        return [
            ['@boolean@'],
        ];
    }

    public static function positiveMatchData()
    {
        return [
            [true, '@boolean@'],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ['@boolean'],
            ['boolean'],
            [1],
        ];
    }

    public static function negativeMatchData()
    {
        return [
            ['1', '@boolean@'],
            [new DateTime(),  '@boolean@'],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [new stdClass(),  '@boolean@', 'object "\\stdClass" is not a valid boolean.'],
            [1.1, '@boolean@', 'double "1.1" is not a valid boolean.'],
            ['true', '@string@', 'string "true" is not a valid boolean.'],
            [['test'], '@boolean@', 'array "Array(1)" is not a valid boolean.'],
        ];
    }

    protected function setUp(): void
    {
        $this->matcher = new BooleanMatcher(
            $this->backtrace = new Backtrace\InMemoryBacktrace(),
            new Parser(new Lexer(), new Parser\ExpanderInitializer($this->backtrace)),
        );
    }

    #[DataProvider('positiveCanMatchData')]
    public function testPositiveCanMatches($pattern): void
    {
        $this->assertTrue($this->matcher->canMatch($pattern));
        $this->assertFalse($this->backtrace->isEmpty());
    }

    #[DataProvider('negativeCanMatchData')]
    public function testNegativeCanMatches($pattern): void
    {
        $this->assertFalse($this->matcher->canMatch($pattern));
        $this->assertFalse($this->backtrace->isEmpty());
    }

    #[DataProvider('positiveMatchData')]
    public function testPositiveMatch($value, $pattern): void
    {
        $this->assertTrue($this->matcher->match($value, $pattern));
        $this->assertFalse($this->backtrace->isEmpty());
    }

    #[DataProvider('negativeMatchData')]
    public function testNegativeMatch($value, $pattern): void
    {
        $this->assertFalse($this->matcher->match($value, $pattern));
        $this->assertFalse($this->backtrace->isEmpty());
    }

    #[DataProvider('negativeMatchDescription')]
    public function testNegativeMatchDescription($value, $pattern, $error): void
    {
        $this->matcher->match($value, $pattern);
        $this->assertEquals($error, $this->matcher->getError());
        $this->assertFalse($this->backtrace->isEmpty());
    }
}
