<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Lexer;
use AppVerk\PHPMatcher\Matcher\DateMatcher;
use AppVerk\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;
use stdClass;

class DateMatcherTest extends TestCase
{
    private DateMatcher $matcher;

    private Backtrace $backtrace;

    public static function positiveCanMatchData()
    {
        return [
            ['@date@'],
        ];
    }

    public static function positiveMatchData()
    {
        return [
            ['2020-01-01', '@date@'],
            ['2020-01-01', '@date@'],
            ['2020-01-01', '@date@.isDateTime()'],
            ['2020-01-02', '@date@.after("2020-01-01")'],
            ['2020-01-01', '@date@.before("2021-01-03")'],
            ['-2 hours', '@date@.after("-5 hours")'],
            ['+3 hours', '@date@.before("+1 day")'],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ['@string'],
            ['string'],
            [1],
        ];
    }

    public static function negativeMatchData()
    {
        return [
            [1, '@date@'],
            [0,  '@date@'],
            ['not a date',  '@date@'],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [new stdClass(),  '@dat@', 'object "\\stdClass" is not a valid string.'],
            [1.1, '@integer@', 'double "1.1" is not a valid string.'],
            [false, '@double@', 'boolean "false" is not a valid string.'],
            [1, '@array@', 'integer "1" is not a valid string.'],
            ['lorem ipsum', "@array@.startsWith('ipsum')", 'lorem ipsum "lorem ipsum" is not a valid date.'],
        ];
    }

    protected function setUp(): void
    {
        $this->matcher = new DateMatcher(
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
    }
}
