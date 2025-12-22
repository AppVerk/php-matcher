<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Lexer;
use AppVerk\PHPMatcher\Matcher\TimeZoneMatcher;
use AppVerk\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;
use stdClass;

class TimeZoneMatcherTest extends TestCase
{
    private TimeZoneMatcher $matcher;

    public static function positiveCanMatchData()
    {
        return [
            ['@timezone@'],
            ['@tz@'],
        ];
    }

    public static function positiveMatchData()
    {
        return [
            ['Europe/Warsaw', '@tz@'],
            ['Europe/Warsaw', '@timezone@'],
            ['GMT', '@timezone@'],
            ['UTC', '@timezone@'],
            ['+00:00', '@timezone@'],
            ['01:00', '@timezone@'],
            ['01:00', '@timezone@.isTzOffset()'],
            ['GMT', '@timezone@.isTzAbbreviation()'],
            ['Europe/Warsaw', '@timezone@.isTzIdentifier()'],
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
            [1, '@timezone@'],
            [0,  '@timezone@'],
            ['blablabla',  '@timezone@'],
            ['GMT', '@timezone@.isTzOffset()'],
            ['GMT', '@timezone@.isTzIdentifier()'],
            ['00:00', '@timezone@.isTzAbbreviation()'],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [new stdClass(),  '@dat@', 'object "\\stdClass" is not a valid string.'],
            [1.1, '@integer@', 'double "1.1" is not a valid string.'],
            [false, '@double@', 'boolean "false" is not a valid string.'],
            [1, '@array@', 'integer "1" is not a valid string.'],
            ['lorem ipsum', "@array@.startsWith('ipsum')", 'lorem ipsum "lorem ipsum" is not a valid timezone.'],
        ];
    }

    protected function setUp(): void
    {
        $this->matcher = new TimeZoneMatcher(
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
