<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Lexer;
use AppVerk\PHPMatcher\Matcher\NumberMatcher;
use AppVerk\PHPMatcher\Parser;
use DateTime;
use PHPUnit\Framework\TestCase;
use stdClass;

class NumberMatcherTest extends TestCase
{
    private ?NumberMatcher $matcher = null;

    public static function positiveCanMatchData()
    {
        return [
            ['@number@'],
        ];
    }

    public static function positiveMatchData()
    {
        return [
            [10.1, '@number@'],
            [10, '@number@'],
            ['25', '@number@'],
            [10, '@number@.lowerThan(50).greaterThan(1)'],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ['@number'],
            ['number'],
            [1],
        ];
    }

    public static function negativeMatchData()
    {
        return [
            [['test'], '@number@'],
            [new DateTime(),  '@number@'],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [new stdClass(),  '@number@', 'object "\\stdClass" is not a valid number.'],
            [false, '@number@', 'boolean "false" is not a valid number.'],
            [['test'], '@number@', 'array "Array(1)" is not a valid number.'],
        ];
    }

    protected function setUp(): void
    {
        $this->matcher = new NumberMatcher(
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
