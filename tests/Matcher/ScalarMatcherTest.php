<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\ScalarMatcher;
use PHPUnit\Framework\TestCase;
use stdClass;

class ScalarMatcherTest extends TestCase
{
    public static function negativeMatches()
    {
        return [
            [false, 'false'],
            [false, 0],
            [true, 1],
            ['array', []],
        ];
    }

    public static function positiveMatches()
    {
        return [
            [1, 1],
            ['michal', 'michal'],
            [false, false],
            [6.66, 6.66],
        ];
    }

    public static function positiveCanMatches()
    {
        return [
            [1],
            ['michal'],
            [true],
            [false],
            [6.66],
        ];
    }

    public static function negativeCanMatches()
    {
        return [
            [new stdClass()],
            [[]],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            ['test', 'norbert', '"test" does not match "norbert".'],
            [new stdClass(),  1, '"\\stdClass" does not match "1".'],
            [1.1, false, '"1.1" does not match "false".'],
            [false, ['foo', 'bar'], '"false" does not match "Array(2)".'],
        ];
    }

    #[DataProvider('positiveCanMatches')]
    public function testPositiveCanMatches($pattern): void
    {
        $matcher = new ScalarMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertTrue($matcher->canMatch($pattern));
    }

    #[DataProvider('negativeCanMatches')]
    public function testNegativeCanMatches($pattern): void
    {
        $matcher = new ScalarMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($matcher->canMatch($pattern));
    }

    #[DataProvider('positiveMatches')]
    public function testPositiveMatches($value, $pattern): void
    {
        $matcher = new ScalarMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertTrue($matcher->match($value, $pattern));
    }

    #[DataProvider('negativeMatches')]
    public function testNegativeMatches($value, $pattern): void
    {
        $matcher = new ScalarMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($matcher->match($value, $pattern));
    }

    #[DataProvider('negativeMatchDescription')]
    public function testNegativeMatchDescription($value, $pattern, $error): void
    {
        $matcher = new ScalarMatcher(new Backtrace\InMemoryBacktrace());
        $matcher->match($value, $pattern);
        $this->assertEquals($error, $matcher->getError());
    }
}
