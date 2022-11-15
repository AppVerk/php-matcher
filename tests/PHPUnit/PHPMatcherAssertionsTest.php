<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\PHPUnit;

use AppVerk\PHPMatcher\Backtrace\InMemoryBacktrace;
use AppVerk\PHPMatcher\PHPUnit\PHPMatcherAssertions;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;

class PHPMatcherAssertionsTest extends TestCase
{
    use PHPMatcherAssertions;

    public function test_it_asserts_if_a_value_matches_the_pattern() : void
    {
        $this->assertMatchesPattern('@string@', 'foo');
    }

    public function test_it_throws_an_expectation_failed_exception_if_a_value_does_not_match_the_pattern() : void
    {
        try {
            $this->assertMatchesPattern('{"foo": "@integer@"}', \json_encode(['foo' => 'bar']));
        } catch (\Exception $e) {
            $this->assertSame(
                <<<'ERROR'
Failed asserting that Value "bar" does not match pattern "@integer@" at path: "[foo]".
ERROR,
                $e->getMessage()
            );
        }
    }

    public function test_it_throws_an_expectation_failed_exception_if_a_value_does_not_match_the_pattern_with_backtrace() : void
    {
        $this->setBacktrace($backtrace = new InMemoryBacktrace());

        try {
            $this->assertMatchesPattern('{"foo": "@integer@"}', \json_encode(['foo' => 'bar']));
        } catch (\Exception $e) {
            $this->assertSame(
                <<<ERROR
Failed asserting that Value "bar" does not match pattern "@integer@" at path: "[foo]"
Backtrace:
#1 Matcher AppVerk\PHPMatcher\Matcher matching value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#2 Matcher AppVerk\PHPMatcher\Matcher\ChainMatcher (all) matching value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#3 Matcher AppVerk\PHPMatcher\Matcher\ChainMatcher (scalars) can match pattern "{"foo":"@integer@"}"
#4 Matcher AppVerk\PHPMatcher\Matcher\ChainMatcher (scalars) matching value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#5 Matcher AppVerk\PHPMatcher\Matcher\CallbackMatcher can't match pattern "{"foo":"@integer@"}"
#6 Matcher AppVerk\PHPMatcher\Matcher\ExpressionMatcher can't match pattern "{"foo":"@integer@"}"
#7 Matcher AppVerk\PHPMatcher\Matcher\NullMatcher can't match pattern "{"foo":"@integer@"}"
#8 Matcher AppVerk\PHPMatcher\Matcher\StringMatcher can't match pattern "{"foo":"@integer@"}"
#9 Matcher AppVerk\PHPMatcher\Matcher\IntegerMatcher can't match pattern "{"foo":"@integer@"}"
#10 Matcher AppVerk\PHPMatcher\Matcher\BooleanMatcher can't match pattern "{"foo":"@integer@"}"
#11 Matcher AppVerk\PHPMatcher\Matcher\DoubleMatcher can't match pattern "{"foo":"@integer@"}"
#12 Matcher AppVerk\PHPMatcher\Matcher\NumberMatcher can't match pattern "{"foo":"@integer@"}"
#13 Matcher AppVerk\PHPMatcher\Matcher\TimeMatcher can't match pattern "{"foo":"@integer@"}"
#14 Matcher AppVerk\PHPMatcher\Matcher\DateMatcher can't match pattern "{"foo":"@integer@"}"
#15 Matcher AppVerk\PHPMatcher\Matcher\DateTimeMatcher can't match pattern "{"foo":"@integer@"}"
#16 Matcher AppVerk\PHPMatcher\Matcher\TimeZoneMatcher can't match pattern "{"foo":"@integer@"}"
#17 Matcher AppVerk\PHPMatcher\Matcher\ScalarMatcher can match pattern "{"foo":"@integer@"}"
#18 Matcher AppVerk\PHPMatcher\Matcher\ScalarMatcher matching value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#19 Matcher AppVerk\PHPMatcher\Matcher\ScalarMatcher failed to match value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#20 Matcher AppVerk\PHPMatcher\Matcher\ScalarMatcher error: "{"foo":"bar"}" does not match "{"foo":"@integer@"}".
#21 Matcher AppVerk\PHPMatcher\Matcher\WildcardMatcher can't match pattern "{"foo":"@integer@"}"
#22 Matcher AppVerk\PHPMatcher\Matcher\UuidMatcher can't match pattern "{"foo":"@integer@"}"
#23 Matcher AppVerk\PHPMatcher\Matcher\UlidMatcher can't match pattern "{"foo":"@integer@"}"
#24 Matcher AppVerk\PHPMatcher\Matcher\JsonObjectMatcher can't match pattern "{"foo":"@integer@"}"
#25 Matcher AppVerk\PHPMatcher\Matcher\EnumMatcher can't match pattern "{"foo":"@integer@"}"
#26 Matcher AppVerk\PHPMatcher\Matcher\ChainMatcher (scalars) failed to match value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#27 Matcher AppVerk\PHPMatcher\Matcher\ChainMatcher (scalars) error: "{"foo":"bar"}" does not match "{"foo":"@integer@"}".
#28 Matcher AppVerk\PHPMatcher\Matcher\JsonMatcher can match pattern "{"foo":"@integer@"}"
#29 Matcher AppVerk\PHPMatcher\Matcher\JsonMatcher matching value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#30 Matcher AppVerk\PHPMatcher\Matcher\ArrayMatcher matching value "Array(1)" with "Array(1)" pattern
#31 Matcher AppVerk\PHPMatcher\Matcher\ChainMatcher (array) can match pattern "@integer@"
#32 Matcher AppVerk\PHPMatcher\Matcher\ChainMatcher (array) matching value "bar" with "@integer@" pattern
#33 Matcher AppVerk\PHPMatcher\Matcher\OrMatcher can't match pattern "@integer@"
#34 Matcher AppVerk\PHPMatcher\Matcher\ChainMatcher (scalars) can match pattern "@integer@"
#35 Matcher AppVerk\PHPMatcher\Matcher\ChainMatcher (scalars) matching value "bar" with "@integer@" pattern
#36 Matcher AppVerk\PHPMatcher\Matcher\CallbackMatcher can't match pattern "@integer@"
#37 Matcher AppVerk\PHPMatcher\Matcher\ExpressionMatcher can't match pattern "@integer@"
#38 Matcher AppVerk\PHPMatcher\Matcher\NullMatcher can't match pattern "@integer@"
#39 Matcher AppVerk\PHPMatcher\Matcher\StringMatcher can't match pattern "@integer@"
#40 Matcher AppVerk\PHPMatcher\Matcher\IntegerMatcher can match pattern "@integer@"
#41 Matcher AppVerk\PHPMatcher\Matcher\IntegerMatcher matching value "bar" with "@integer@" pattern
#42 Matcher AppVerk\PHPMatcher\Matcher\IntegerMatcher failed to match value "bar" with "@integer@" pattern
#43 Matcher AppVerk\PHPMatcher\Matcher\IntegerMatcher error: string "bar" is not a valid integer.
#44 Matcher AppVerk\PHPMatcher\Matcher\BooleanMatcher can't match pattern "@integer@"
#45 Matcher AppVerk\PHPMatcher\Matcher\DoubleMatcher can't match pattern "@integer@"
#46 Matcher AppVerk\PHPMatcher\Matcher\NumberMatcher can't match pattern "@integer@"
#47 Matcher AppVerk\PHPMatcher\Matcher\TimeMatcher can't match pattern "@integer@"
#48 Matcher AppVerk\PHPMatcher\Matcher\DateMatcher can't match pattern "@integer@"
#49 Matcher AppVerk\PHPMatcher\Matcher\DateTimeMatcher can't match pattern "@integer@"
#50 Matcher AppVerk\PHPMatcher\Matcher\TimeZoneMatcher can't match pattern "@integer@"
#51 Matcher AppVerk\PHPMatcher\Matcher\ScalarMatcher can match pattern "@integer@"
#52 Matcher AppVerk\PHPMatcher\Matcher\ScalarMatcher matching value "bar" with "@integer@" pattern
#53 Matcher AppVerk\PHPMatcher\Matcher\ScalarMatcher failed to match value "bar" with "@integer@" pattern
#54 Matcher AppVerk\PHPMatcher\Matcher\ScalarMatcher error: "bar" does not match "@integer@".
#55 Matcher AppVerk\PHPMatcher\Matcher\WildcardMatcher can't match pattern "@integer@"
#56 Matcher AppVerk\PHPMatcher\Matcher\UuidMatcher can't match pattern "@integer@"
#57 Matcher AppVerk\PHPMatcher\Matcher\UlidMatcher can't match pattern "@integer@"
#58 Matcher AppVerk\PHPMatcher\Matcher\JsonObjectMatcher can't match pattern "@integer@"
#59 Matcher AppVerk\PHPMatcher\Matcher\EnumMatcher can't match pattern "@integer@"
#60 Matcher AppVerk\PHPMatcher\Matcher\ChainMatcher (scalars) failed to match value "bar" with "@integer@" pattern
#61 Matcher AppVerk\PHPMatcher\Matcher\ChainMatcher (scalars) error: "bar" does not match "@integer@".
#62 Matcher AppVerk\PHPMatcher\Matcher\TextMatcher can match pattern "@integer@"
#63 Matcher AppVerk\PHPMatcher\Matcher\TextMatcher matching value "bar" with "@integer@" pattern
#64 Matcher AppVerk\PHPMatcher\Matcher\TextMatcher failed to match value "bar" with "@integer@" pattern
#65 Matcher AppVerk\PHPMatcher\Matcher\TextMatcher error: "bar" does not match "@integer@" pattern
#66 Matcher AppVerk\PHPMatcher\Matcher\ChainMatcher (array) failed to match value "bar" with "@integer@" pattern
#67 Matcher AppVerk\PHPMatcher\Matcher\ChainMatcher (array) error: "bar" does not match "@integer@" pattern
#68 Matcher AppVerk\PHPMatcher\Matcher\ArrayMatcher failed to match value "Array(1)" with "Array(1)" pattern
#69 Matcher AppVerk\PHPMatcher\Matcher\ArrayMatcher error: Value "bar" does not match pattern "@integer@" at path: "[foo]"
#70 Matcher AppVerk\PHPMatcher\Matcher\JsonMatcher failed to match value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#71 Matcher AppVerk\PHPMatcher\Matcher\JsonMatcher error: Value "bar" does not match pattern "@integer@" at path: "[foo]"
#72 Matcher AppVerk\PHPMatcher\Matcher\XmlMatcher can't match pattern "{"foo":"@integer@"}"
#73 Matcher AppVerk\PHPMatcher\Matcher\OrMatcher can't match pattern "{"foo":"@integer@"}"
#74 Matcher AppVerk\PHPMatcher\Matcher\TextMatcher can't match pattern "{"foo":"@integer@"}"
#75 Matcher AppVerk\PHPMatcher\Matcher\ChainMatcher (all) failed to match value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#76 Matcher AppVerk\PHPMatcher\Matcher\ChainMatcher (all) error: Value "bar" does not match pattern "@integer@" at path: "[foo]"
#77 Matcher AppVerk\PHPMatcher\Matcher failed to match value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#78 Matcher AppVerk\PHPMatcher\Matcher error: Value "bar" does not match pattern "@integer@" at path: "[foo]".
ERROR,
                $e->getMessage()
            );
        }

        $this->assertFalse($backtrace->isEmpty());
    }

    public function test_it_creates_a_constraint_for_stubs() : void
    {
        $this->expectException(AssertionFailedError::class);

        /*
         *  Expected console output:
         *
         *  Expectation failed for method name is "getTitle" when invoked zero or more time s
         *  Parameter 0 for invocation stdClass::getTitle(42) does not match expected value.
         *  Failed asserting that 42 matches given pattern.
         *  Pattern: '@string@'
         *  Error: integer "42" is not a valid string.
         *  Backtrace:
         *  #1 Matcher AppVerk\PHPMatcher\Matcher matching value "42" with "@string@" pattern
         *  #2 Matcher AppVerk\PHPMatcher\Matcher\ChainMatcher (all) matching value "42" with "@string@" pattern
         *  #3 Matcher AppVerk\PHPMatcher\Matcher\ChainMatcher (scalars) can match pattern "@string@"
         *  #...
         *  #35 Matcher AppVerk\PHPMatcher\Matcher error: integer "42" is not a valid string.
         */
        $this->expectExceptionMessageMatches("/Expectation failed for method name is \"getTitle\" when invoked zero or more times\nParameter 0 for invocation stdClass::getTitle\(42\) does not match expected value.\nFailed asserting that integer \"42\" is not a valid string../");

        $mock = $this->getMockBuilder('stdClass')
            ->setMethods(['getTitle'])
            ->getMock();

        $mock->method('getTitle')
            ->with($this->matchesPattern('@string@'))
            ->willReturn('foo');

        $mock->getTitle(42);
    }
}
