<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher\Pattern\Expander;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\LowerThan;
use PHPUnit\Framework\TestCase;

class LowerThanTest extends TestCase
{
    public static function examplesProvider()
    {
        return [
            [10.5, 10, true],
            [-10.5, -20, true],
            [1, 10, false],
            [1, 1, false],
        ];
    }

    public static function invalidCasesProvider()
    {
        return [
            [1, 'ipsum lorem', 'Value "ipsum lorem" is not a valid number.'],
            [5, 10, 'Value "10" is not lower than "5".'],
            [5, 5, 'Value "5" is not lower than "5".'],
        ];
    }

    #[DataProvider('examplesProvider')]
    public function testExamples($boundary, $value, $expectedResult): void
    {
        $expander = new LowerThan($boundary);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($value));
    }

    #[DataProvider('invalidCasesProvider')]
    public function testErrorWhenMatchingFail($boundary, $value, $errorMessage): void
    {
        $expander = new LowerThan($boundary);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }
}
