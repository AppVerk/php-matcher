<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher\Pattern\Expander;

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

    /**
     * @dataProvider examplesProvider
     */
    public function test_examples($boundary, $value, $expectedResult) : void
    {
        $expander = new GreaterThan($boundary);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($value));
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($boundary, $value, $errorMessage) : void
    {
        $expander = new GreaterThan($boundary);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }
}
