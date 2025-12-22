<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher\Pattern\Expander;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\IsDateTime;
use PHPUnit\Framework\TestCase;

class IsDateTimeTest extends TestCase
{
    public static function examplesDatesProvider()
    {
        return [
            ['201-20-44', false],
            ['2012-10-11', true],
            ['invalid', false],
            ['Monday, 15-Aug-2005 15:52:01 UTC', true],
        ];
    }

    #[DataProvider('examplesDatesProvider')]
    public function testDates($date, $expectedResult): void
    {
        $expander = new IsDateTime();
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($date));
    }
}
