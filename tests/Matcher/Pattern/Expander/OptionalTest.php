<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher\Pattern\Expander;

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

    /**
     * @dataProvider examplesProvider
     */
    public function test_optional_expander_match($value, $expectedResult) : void
    {
        $expander = new Optional();
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($value));
    }
}
