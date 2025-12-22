<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\ExpressionMatcher;
use DateTime;
use PHPUnit\Framework\TestCase;
use stdClass;

class ExpressionMatcherTest extends TestCase
{
    public static function positiveCanMatchData()
    {
        return [
            ['expr(1 > 2)'],
            ["expr(value == 'foo')"],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ['@integer'],
            ['expr('],
            ['@string'],
            [new stdClass()],
            [['foobar']],
        ];
    }

    public static function positiveMatchData()
    {
        return [
            [4, 'expr(value > 2)'],
            ['foo', "expr(value == 'foo')"],
            [new DateTime('2014-04-01'), "expr(value.format('Y-m-d') == '2014-04-01')"],
        ];
    }

    public static function negativeMatchData()
    {
        return [
            [4, 'expr(value < 2)'],
            ['foo', "expr(value != 'foo')"],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [4, 'expr(value < 2)', '"expr(value < 2)" expression fails for value "4".'],
            [
                new DateTime('2014-04-01'),
                "expr(value.format('Y-m-d') == '2014-04-02')",
                "\"expr(value.format('Y-m-d') == '2014-04-02')\" expression fails for value \"\\DateTime\".",
            ],
        ];
    }

    public static function positiveRegexMatchData()
    {
        return [
            ['Cakper', 'expr(value matches "/Cakper/")'],
            ['Cakper', 'expr(not(value matches "/Yaboomaster/"))'],
        ];
    }

    public static function negativeRegexMatchData()
    {
        return [
            ['Cakper', 'expr(not(value matches "/Cakper/"))'],
            ['Cakper', 'expr(value matches "/Yaboomaster/")'],
        ];
    }

    #[DataProvider('positiveCanMatchData')]
    public function testPositiveCanMatches($pattern): void
    {
        $matcher = new ExpressionMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertTrue($matcher->canMatch($pattern));
    }

    #[DataProvider('negativeCanMatchData')]
    public function testNegativeCanMatches($pattern): void
    {
        $matcher = new ExpressionMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($matcher->canMatch($pattern));
    }

    #[DataProvider('positiveMatchData')]
    public function testPositiveMatch($value, $pattern): void
    {
        $matcher = new ExpressionMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertTrue($matcher->match($value, $pattern));
    }

    #[DataProvider('negativeMatchData')]
    public function testNegativeMatch($value, $pattern): void
    {
        $matcher = new ExpressionMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($matcher->match($value, $pattern));
    }

    #[DataProvider('negativeMatchDescription')]
    public function testNegativeMatchDescription($value, $pattern, $error): void
    {
        $matcher = new ExpressionMatcher(new Backtrace\InMemoryBacktrace());
        $matcher->match($value, $pattern);
        $this->assertEquals($error, $matcher->getError());
    }

    #[DataProvider('positiveRegexMatchData')]
    public function testPositiveRegexMatches($value, $pattern): void
    {
        $matcher = new ExpressionMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertTrue($matcher->match($value, $pattern));
    }

    #[DataProvider('negativeRegexMatchData')]
    public function testNegativeRegexMatches($value, $pattern): void
    {
        $matcher = new ExpressionMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($matcher->match($value, $pattern));
    }
}
