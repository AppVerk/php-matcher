<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher\Pattern\Expander;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\InArray;
use DateTime;
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
            ['lorem', new DateTime(), 'InArray expander require "array", got "\\DateTime".'],
        ];
    }

    #[DataProvider('examplesProvider')]
    public function testMatchingValues($needle, $haystack, $expectedResult): void
    {
        $expander = new InArray($needle);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($haystack));
    }

    #[DataProvider('invalidCasesProvider')]
    public function testErrorWhenMatchingFail($boundary, $value, $errorMessage): void
    {
        $expander = new InArray($boundary);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }
}
