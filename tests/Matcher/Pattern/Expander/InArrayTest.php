<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher\Pattern\Expander;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\InArray;
use PHPUnit\Framework\TestCase;

class InArrayTest extends TestCase
{
    public static function examplesProvider()
    {
        return [
            ['ipsum', ['ipsum'], true],
            [1, ['foo', 1], true],
            [['foo' => 'bar'], [['foo' => 'bar']], true],
        ];
    }

    public static function invalidCasesProvider()
    {
        return [
            ['ipsum', ['ipsum lorem'], "Array(1) doesn't have \"ipsum\" element."],
            ['lorem', new \DateTime(), 'InArray expander require "array", got "\\DateTime".'],
        ];
    }

    /**
     * @dataProvider examplesProvider
     */
    public function test_matching_values($needle, $haystack, $expectedResult) : void
    {
        $expander = new InArray($needle);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($haystack));
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($boundary, $value, $errorMessage) : void
    {
        $expander = new InArray($boundary);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }
}
