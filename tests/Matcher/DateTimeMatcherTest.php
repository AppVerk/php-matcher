<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Lexer;
use AppVerk\PHPMatcher\Matcher\DateTimeMatcher;
use AppVerk\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;
use stdClass;

class DateTimeMatcherTest extends TestCase
{
    private DateTimeMatcher $matcher;

    private Backtrace $backtrace;

    public static function positiveCanMatchData()
    {
        return [
            ['@datetime@'],
        ];
    }

    public static function positiveMatchData()
    {
        return [
            ['2020-01-01', '@datetime@'],
            ['2020-01-01 00:00:00', '@datetime@'],
            ['2020-01-01 00:00:00', '@datetime@.isDateTime()'],
            ['2020-01-01 01:00:00', '@datetime@.after("2020-01-01 00:00:00")'],
            ['2020-01-01 01:00:00', '@datetime@.before("2021-01-01 00:00:00")'],
            ['yesterday', '@datetime@.after("-5 days")'],
            ['tomorrow', '@datetime@.before("+2 days")'],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ['@datetime'],
            ['datetime'],
            [1],
        ];
    }

    public static function negativeMatchData()
    {
        return [
            [1, '@datetime@'],
            [0,  '@datetime@'],
            ['not a date time', '@datetime@'],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [new stdClass(),  '@datetime@', 'object "\\stdClass" is not a valid string.'],
            [1.1, '@integer@', 'double "1.1" is not a valid string.'],
            [false, '@double@', 'boolean "false" is not a valid string.'],
            [1, '@array@', 'integer "1" is not a valid string.'],
            ['lorem ipsum', "@array@.startsWith('ipsum')", 'lorem ipsum "lorem ipsum" is not a valid date time.'],
        ];
    }

    protected function setUp(): void
    {
        $this->matcher = new DateTimeMatcher(
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
