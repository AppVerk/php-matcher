<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\WildcardMatcher;
use PHPUnit\Framework\TestCase;
use stdClass;

class WildcardMatcherTest extends TestCase
{
    public static function data()
    {
        return [
            ['@integer@'],
            ['foobar'],
            [true],
            [6.66],
            [['bar']],
            [new stdClass()],
        ];
    }

    public static function positivePatterns()
    {
        return [
            ['@*@'],
            ['@wildcard@'],
        ];
    }

    #[DataProvider('data')]
    public function testPositiveMatch($pattern): void
    {
        $matcher = new WildcardMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertTrue($matcher->match('*', $pattern));
    }

    #[DataProvider('positivePatterns')]
    public function testPositiveCanMatch($pattern): void
    {
        $matcher = new WildcardMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertTrue($matcher->canMatch($pattern));
    }

    public function testNegativeCanMatch(): void
    {
        $matcher = new WildcardMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($matcher->canMatch('*'));
    }
}
