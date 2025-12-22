<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher\Pattern\Expander;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\Optional;
use PHPUnit\Framework\TestCase;

class OptionalTest extends TestCase
{
    public static function examplesProvider()
    {
        return [
            [[], true],
            [['data'], true],
            ['', true],
            [0, true],
            [10.1, true],
            [null, true],
            [true, true],
            ['Lorem ipsum', true],
        ];
    }

    #[DataProvider('examplesProvider')]
    public function testOptionalExpanderMatch($value, $expectedResult): void
    {
        $expander = new Optional();
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($value));
    }
}
