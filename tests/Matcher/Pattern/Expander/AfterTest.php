<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher\Pattern\Expander;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\After;
use PHPUnit\Framework\TestCase;

class AfterTest extends TestCase
{
    public static function examplesProvider()
    {
        return [
            ['+ 2 day', 'today', false],
            ['2018-02-06T04:20:33', '2017-02-06T04:20:33', false],
            ['2017-02-06T04:20:33', '2018-02-06T04:20:33', true],
        ];
    }

    public static function invalidCasesProvider()
    {
        return [
            ['today', 'ipsum lorem', 'Value "ipsum lorem" is not a valid date.'],
            ['2017-02-06T04:20:33', 'ipsum lorem', 'Value "ipsum lorem" is not a valid date.'],
            ['today', 5, 'After expander require "string", got "5".'],
        ];
    }

    #[DataProvider('examplesProvider')]
    public function testExamples($boundary, $value, $expectedResult): void
    {
        $expander = new After($boundary);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($value));
    }

    #[DataProvider('invalidCasesProvider')]
    public function testErrorWhenMatchingFail($boundary, $value, $errorMessage): void
    {
        $expander = new After($boundary);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }
}
