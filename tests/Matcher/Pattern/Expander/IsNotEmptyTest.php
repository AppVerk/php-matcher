<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher\Pattern\Expander;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\IsNotEmpty;
use DateTime;
use PHPUnit\Framework\TestCase;

class IsNotEmptyTest extends TestCase
{
    public static function examplesProvider()
    {
        return [
            ['lorem', true],
            ['0', true],
            [new DateTime(), true],
            ['', false],
            [null, false],
            [[], false],
        ];
    }

    #[DataProvider('examplesProvider')]
    public function testExamplesNotIgnoringCase($value, $expectedResult): void
    {
        $expander = new IsNotEmpty();
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($value));
    }
}
