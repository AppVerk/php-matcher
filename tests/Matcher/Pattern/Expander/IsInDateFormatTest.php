<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher\Pattern\Expander;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\IsInDateFormat;
use PHPUnit\Framework\TestCase;

class IsInDateFormatTest extends TestCase
{
    #[DataProvider('examplesDatesProvider')]
    public function testDates(string $date, string $format, bool $result): void
    {
        $expander = new IsInDateFormat($format);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($result, $expander->match($date));
    }

    public static function examplesDatesProvider(): array
    {
        return [
            ['2010-01-01', 'Y-m-d', true],
            ['2010-01-01 00:00:01', 'Y-m-d H:i:s', true],
            ['2010-01-01 00:00:01', 'Y-m-d', false],
            ['20100101', 'Ymd', true],
            ['Y-m-d', 'Y-m-d', false],
        ];
    }
}
