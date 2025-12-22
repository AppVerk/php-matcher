<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\ChainMatcher;
use AppVerk\PHPMatcher\Matcher\ValueMatcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ChainMatcherTest extends TestCase
{
    private ?ChainMatcher $matcher = null;

    
    private $firstMatcher;

    
    private $secondMatcher;

    protected function setUp(): void
    {
        $this->firstMatcher = $this->createMock(ValueMatcher::class);
        $this->secondMatcher = $this->createMock(ValueMatcher::class);

        $this->matcher = new ChainMatcher(
            self::class,
            new Backtrace\InMemoryBacktrace(),
            [
                $this->firstMatcher,
                $this->secondMatcher,
            ],
        );
    }

    public function testOnlyOneMatcherCanMatchButNoneMatchersMatch(): void
    {
        $this->firstMatcher->expects($this->once())->method('canMatch')->willReturn(false);
        $this->firstMatcher->expects($this->never())->method('match');
        $this->secondMatcher->expects($this->once())->method('canMatch')->willReturn(true);
        $this->secondMatcher->expects($this->once())->method('match')->willReturn(false);

        $this->assertEquals($this->matcher->match('foo', 'foo_pattern'), false);
    }

    public function testNoneMatchersCanMatch(): void
    {
        $this->firstMatcher->expects($this->once())->method('canMatch')->willReturn(false);
        $this->firstMatcher->expects($this->never())->method('match');
        $this->secondMatcher->expects($this->once())->method('canMatch')->willReturn(false);
        $this->secondMatcher->expects($this->never())->method('match');

        $this->assertEquals($this->matcher->match('foo', 'foo_pattern'), false);
    }

    public function testFirstMatcherMatch(): void
    {
        $this->firstMatcher->expects($this->once())->method('canMatch')->willReturn(true);
        $this->firstMatcher->expects($this->once())->method('match')->willReturn(true);
        $this->secondMatcher->expects($this->never())->method('canMatch');
        $this->secondMatcher->expects($this->never())->method('match');

        $this->assertEquals($this->matcher->match('foo', 'foo_pattern'), true);
    }

    public function testIfThereIsErrorDescriptionOnlyFromLastMatcherThatFails(): void
    {
        $this->firstMatcher->expects($this->any())->method('canMatch')->willReturn(true);
        $this->firstMatcher->expects($this->any())->method('match')->willReturn(false);
        $this->firstMatcher->expects($this->any())->method('getError')
            ->willReturn('First matcher error');

        $this->secondMatcher->expects($this->any())->method('canMatch')->willReturn(true);
        $this->secondMatcher->expects($this->any())->method('match')->willReturn(false);
        $this->secondMatcher->expects($this->any())->method('getError')
            ->willReturn('Second matcher error');

        $this->assertEquals($this->matcher->match('foo', 'foo_pattern'), false);
        $this->assertEquals($this->matcher->getError(), 'Second matcher error');
    }

    public function testErrorDescriptionWhenAnyMatcherCanMatch(): void
    {
        $this->firstMatcher->expects($this->once())->method('canMatch')->willReturn(false);
        $this->secondMatcher->expects($this->once())->method('canMatch')->willReturn(false);

        $this->assertEquals($this->matcher->match('foo', 'foo_pattern'), false);
        $this->assertEquals($this->matcher->getError(), 'Any matcher from chain can\'t match value "foo" to pattern "foo_pattern"');
    }
}
