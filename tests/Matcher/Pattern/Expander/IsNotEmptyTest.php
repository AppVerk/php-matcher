<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher\Pattern\Expander;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\IsNotEmpty;
use PHPUnit\Framework\TestCase;

class IsNotEmptyTest extends TestCase
{
    public static function examplesProvider()
    {
        return [
            ['lorem', true],
            ['0', true],
            [new \DateTime(), true],
            ['', false],
            [null, false],
            [[], false],
        ];
    }

    /**
     * @dataProvider examplesProvider
     */
    public function test_examples_not_ignoring_case($value, $expectedResult) : void
    {
        $expander = new IsNotEmpty();
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($value));
    }
}
