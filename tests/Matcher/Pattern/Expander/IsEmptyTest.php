<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher\Pattern\Expander;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\IsEmpty;
use PHPUnit\Framework\TestCase;

class IsEmptyTest extends TestCase
{
    public static function examplesProvider()
    {
        return [
            [[], true],
            [['data'], false],
            ['', true],
            [0, true],
            [null, true],
        ];
    }

    #[DataProvider('examplesProvider')]
    public function testIsEmptyExpanderMatch($value, $expectedResult): void
    {
        $expander = new IsEmpty();
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($value));
    }
}
