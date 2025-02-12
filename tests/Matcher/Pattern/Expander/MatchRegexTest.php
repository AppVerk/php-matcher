<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher\Pattern\Expander;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\MatchRegex;
use PHPUnit\Framework\TestCase;

class MatchRegexTest extends TestCase
{
    public static function examplesProvider()
    {
        return [
            [true, null, '/^\w$/', 'a'],
            [false, 'string "aa" don\'t match pattern /^\w$/.', '/^\w$/', 'aa'],
            [false, 'Match expander require "string", got "Array(0)".', '/^\w$/', []],
        ];
    }

    /**
     * @dataProvider examplesProvider
     */
    public function test_match_expander($expectedResult, $expectedError, $pattern, $value) : void
    {
        $expander = new MatchRegex($pattern);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($value));
        $this->assertSame($expectedError, $expander->getError());
    }

    public function test_that_it_only_work_with_valid_pattern() : void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Regex pattern must be a valid one.');

        new MatchRegex('///');
    }
}
