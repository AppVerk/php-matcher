<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher\Pattern\Expander;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\GreaterThan;
use PHPUnit\Framework\TestCase;

class GreaterThanTest extends TestCase
{
    public static function examplesProvider()
    {
        return [
            [10, 10.5, true],
            [-20, -10.5, true],
            [10, 1, false],
            [1, 1, false],
            [10, '20', true],
        ];
    }

    public static function invalidCasesProvider()
    {
        return [
            [1, 'ipsum lorem', 'Value "ipsum lorem" is not a valid number.'],
            [10, 5, 'Value "5" is not greater than "10".'],
            [5, 5, 'Value "5" is not greater than "5".'],
        ];
    }

    #[DataProvider('examplesProvider')]
    public function testExamples($boundary, $value, $expectedResult): void
    {
        $expander = new GreaterThan($boundary);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($value));
    }

    #[DataProvider('invalidCasesProvider')]
    public function testErrorWhenMatchingFail($boundary, $value, $errorMessage): void
    {
        $expander = new GreaterThan($boundary);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }
}
