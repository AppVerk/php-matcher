<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\PHPUnit;

use AppVerk\PHPMatcher\PHPUnit\PHPMatcherTestCase;
use PHPUnit\Framework\AssertionFailedError;

class PHPMatcherTestCaseTest extends PHPMatcherTestCase
{
    public function testItAssertsIfAValueMatchesThePattern(): void
    {
        $this->assertMatchesPattern('@string@', 'foo');
    }

    public function testItThrowsAnExpectationFailedExceptionIfAValueDoesNotMatchThePattern(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessageMatches('/Failed asserting that Value "bar" does not match pattern "@integer@" at path: "\\[foo\\]"./');

        $this->assertMatchesPattern('{"foo": "@integer@"}', json_encode(['foo' => 'bar']));
    }

    public function testItCreatesAConstraintForStubs(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessageMatches('/Failed asserting that integer "42" is not a valid string../');

        $mock = $this->getMockBuilder('stdClass')
            ->addMethods(['getTitle'])
            ->getMock();

        $mock->method('getTitle')
            ->with($this->matchesPattern('@string@'))
            ->willReturn('foo');

        $mock->getTitle(42);
    }

    public function testItAssertsIfAValueMatchesTheArrayPattern(): void
    {
        $this->assertMatchesPattern(['foo' => '@string@'], ['foo' => 'bar']);
    }
}
