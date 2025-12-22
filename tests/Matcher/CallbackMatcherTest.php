<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\CallbackMatcher;
use DateTime;
use PHPUnit\Framework\TestCase;

class CallbackMatcherTest extends TestCase
{
    public function testPositiveCanMatch(): void
    {
        $matcher = new CallbackMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertTrue($matcher->canMatch(fn () => true));
    }

    public function testNegativeCanMatch(): void
    {
        $matcher = new CallbackMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($matcher->canMatch(new DateTime()));
        $this->assertFalse($matcher->canMatch('SIN'));
    }

    public function testPositiveMatches(): void
    {
        $matcher = new CallbackMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertTrue($matcher->match(2, fn ($value) => true));
        $this->assertTrue($matcher->match('true', fn ($value) => $value));
    }

    public function testNegativeMatches(): void
    {
        $matcher = new CallbackMatcher(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($matcher->match(2, fn ($value) => false));
        $this->assertFalse($matcher->match(0, fn ($value) => $value));
        $this->assertFalse($matcher->match(null, fn ($value) => $value));
        $this->assertFalse($matcher->match([], fn ($value) => $value));
    }
}
