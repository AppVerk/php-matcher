<?php

declare(strict_types=1);

namespace Appverk\PHPMatcher\Tests\PHPUnit;

use Appverk\PHPMatcher\Backtrace\InMemoryBacktrace;
use Appverk\PHPMatcher\PHPUnit\PHPMatcherAssertions;
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
#1 Matcher Appverk\PHPMatcher\Matcher matching value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#2 Matcher Appverk\PHPMatcher\Matcher\ChainMatcher (all) matching value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#3 Matcher Appverk\PHPMatcher\Matcher\ChainMatcher (scalars) can match pattern "{"foo":"@integer@"}"
#4 Matcher Appverk\PHPMatcher\Matcher\ChainMatcher (scalars) matching value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#5 Matcher Appverk\PHPMatcher\Matcher\CallbackMatcher can't match pattern "{"foo":"@integer@"}"
#6 Matcher Appverk\PHPMatcher\Matcher\ExpressionMatcher can't match pattern "{"foo":"@integer@"}"
#7 Matcher Appverk\PHPMatcher\Matcher\NullMatcher can't match pattern "{"foo":"@integer@"}"
#8 Matcher Appverk\PHPMatcher\Matcher\StringMatcher can't match pattern "{"foo":"@integer@"}"
#9 Matcher Appverk\PHPMatcher\Matcher\IntegerMatcher can't match pattern "{"foo":"@integer@"}"
#10 Matcher Appverk\PHPMatcher\Matcher\BooleanMatcher can't match pattern "{"foo":"@integer@"}"
#11 Matcher Appverk\PHPMatcher\Matcher\DoubleMatcher can't match pattern "{"foo":"@integer@"}"
#12 Matcher Appverk\PHPMatcher\Matcher\NumberMatcher can't match pattern "{"foo":"@integer@"}"
#13 Matcher Appverk\PHPMatcher\Matcher\TimeMatcher can't match pattern "{"foo":"@integer@"}"
#14 Matcher Appverk\PHPMatcher\Matcher\DateMatcher can't match pattern "{"foo":"@integer@"}"
#15 Matcher Appverk\PHPMatcher\Matcher\DateTimeMatcher can't match pattern "{"foo":"@integer@"}"
#16 Matcher Appverk\PHPMatcher\Matcher\TimeZoneMatcher can't match pattern "{"foo":"@integer@"}"
#17 Matcher Appverk\PHPMatcher\Matcher\ScalarMatcher can match pattern "{"foo":"@integer@"}"
#18 Matcher Appverk\PHPMatcher\Matcher\ScalarMatcher matching value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#19 Matcher Appverk\PHPMatcher\Matcher\ScalarMatcher failed to match value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#20 Matcher Appverk\PHPMatcher\Matcher\ScalarMatcher error: "{"foo":"bar"}" does not match "{"foo":"@integer@"}".
#21 Matcher Appverk\PHPMatcher\Matcher\WildcardMatcher can't match pattern "{"foo":"@integer@"}"
#22 Matcher Appverk\PHPMatcher\Matcher\UuidMatcher can't match pattern "{"foo":"@integer@"}"
#23 Matcher Appverk\PHPMatcher\Matcher\UlidMatcher can't match pattern "{"foo":"@integer@"}"
#24 Matcher Appverk\PHPMatcher\Matcher\JsonObjectMatcher can't match pattern "{"foo":"@integer@"}"
#25 Matcher Appverk\PHPMatcher\Matcher\EnumMatcher can't match pattern "{"foo":"@integer@"}"
#26 Matcher Appverk\PHPMatcher\Matcher\ChainMatcher (scalars) failed to match value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#27 Matcher Appverk\PHPMatcher\Matcher\ChainMatcher (scalars) error: "{"foo":"bar"}" does not match "{"foo":"@integer@"}".
#28 Matcher Appverk\PHPMatcher\Matcher\JsonMatcher can match pattern "{"foo":"@integer@"}"
#29 Matcher Appverk\PHPMatcher\Matcher\JsonMatcher matching value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#30 Matcher Appverk\PHPMatcher\Matcher\ArrayMatcher matching value "Array(1)" with "Array(1)" pattern
#31 Matcher Appverk\PHPMatcher\Matcher\ChainMatcher (array) can match pattern "@integer@"
#32 Matcher Appverk\PHPMatcher\Matcher\ChainMatcher (array) matching value "bar" with "@integer@" pattern
#33 Matcher Appverk\PHPMatcher\Matcher\OrMatcher can't match pattern "@integer@"
#34 Matcher Appverk\PHPMatcher\Matcher\ChainMatcher (scalars) can match pattern "@integer@"
#35 Matcher Appverk\PHPMatcher\Matcher\ChainMatcher (scalars) matching value "bar" with "@integer@" pattern
#36 Matcher Appverk\PHPMatcher\Matcher\CallbackMatcher can't match pattern "@integer@"
#37 Matcher Appverk\PHPMatcher\Matcher\ExpressionMatcher can't match pattern "@integer@"
#38 Matcher Appverk\PHPMatcher\Matcher\NullMatcher can't match pattern "@integer@"
#39 Matcher Appverk\PHPMatcher\Matcher\StringMatcher can't match pattern "@integer@"
#40 Matcher Appverk\PHPMatcher\Matcher\IntegerMatcher can match pattern "@integer@"
#41 Matcher Appverk\PHPMatcher\Matcher\IntegerMatcher matching value "bar" with "@integer@" pattern
#42 Matcher Appverk\PHPMatcher\Matcher\IntegerMatcher failed to match value "bar" with "@integer@" pattern
#43 Matcher Appverk\PHPMatcher\Matcher\IntegerMatcher error: string "bar" is not a valid integer.
#44 Matcher Appverk\PHPMatcher\Matcher\BooleanMatcher can't match pattern "@integer@"
#45 Matcher Appverk\PHPMatcher\Matcher\DoubleMatcher can't match pattern "@integer@"
#46 Matcher Appverk\PHPMatcher\Matcher\NumberMatcher can't match pattern "@integer@"
#47 Matcher Appverk\PHPMatcher\Matcher\TimeMatcher can't match pattern "@integer@"
#48 Matcher Appverk\PHPMatcher\Matcher\DateMatcher can't match pattern "@integer@"
#49 Matcher Appverk\PHPMatcher\Matcher\DateTimeMatcher can't match pattern "@integer@"
#50 Matcher Appverk\PHPMatcher\Matcher\TimeZoneMatcher can't match pattern "@integer@"
#51 Matcher Appverk\PHPMatcher\Matcher\ScalarMatcher can match pattern "@integer@"
#52 Matcher Appverk\PHPMatcher\Matcher\ScalarMatcher matching value "bar" with "@integer@" pattern
#53 Matcher Appverk\PHPMatcher\Matcher\ScalarMatcher failed to match value "bar" with "@integer@" pattern
#54 Matcher Appverk\PHPMatcher\Matcher\ScalarMatcher error: "bar" does not match "@integer@".
#55 Matcher Appverk\PHPMatcher\Matcher\WildcardMatcher can't match pattern "@integer@"
#56 Matcher Appverk\PHPMatcher\Matcher\UuidMatcher can't match pattern "@integer@"
#57 Matcher Appverk\PHPMatcher\Matcher\UlidMatcher can't match pattern "@integer@"
#58 Matcher Appverk\PHPMatcher\Matcher\JsonObjectMatcher can't match pattern "@integer@"
#59 Matcher Appverk\PHPMatcher\Matcher\EnumMatcher can't match pattern "@integer@"
#60 Matcher Appverk\PHPMatcher\Matcher\ChainMatcher (scalars) failed to match value "bar" with "@integer@" pattern
#61 Matcher Appverk\PHPMatcher\Matcher\ChainMatcher (scalars) error: "bar" does not match "@integer@".
#62 Matcher Appverk\PHPMatcher\Matcher\TextMatcher can match pattern "@integer@"
#63 Matcher Appverk\PHPMatcher\Matcher\TextMatcher matching value "bar" with "@integer@" pattern
#64 Matcher Appverk\PHPMatcher\Matcher\TextMatcher failed to match value "bar" with "@integer@" pattern
#65 Matcher Appverk\PHPMatcher\Matcher\TextMatcher error: "bar" does not match "@integer@" pattern
#66 Matcher Appverk\PHPMatcher\Matcher\ChainMatcher (array) failed to match value "bar" with "@integer@" pattern
#67 Matcher Appverk\PHPMatcher\Matcher\ChainMatcher (array) error: "bar" does not match "@integer@" pattern
#68 Matcher Appverk\PHPMatcher\Matcher\ArrayMatcher failed to match value "Array(1)" with "Array(1)" pattern
#69 Matcher Appverk\PHPMatcher\Matcher\ArrayMatcher error: Value "bar" does not match pattern "@integer@" at path: "[foo]"
#70 Matcher Appverk\PHPMatcher\Matcher\JsonMatcher failed to match value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#71 Matcher Appverk\PHPMatcher\Matcher\JsonMatcher error: Value "bar" does not match pattern "@integer@" at path: "[foo]"
#72 Matcher Appverk\PHPMatcher\Matcher\XmlMatcher can't match pattern "{"foo":"@integer@"}"
#73 Matcher Appverk\PHPMatcher\Matcher\OrMatcher can't match pattern "{"foo":"@integer@"}"
#74 Matcher Appverk\PHPMatcher\Matcher\TextMatcher can't match pattern "{"foo":"@integer@"}"
#75 Matcher Appverk\PHPMatcher\Matcher\ChainMatcher (all) failed to match value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#76 Matcher Appverk\PHPMatcher\Matcher\ChainMatcher (all) error: Value "bar" does not match pattern "@integer@" at path: "[foo]"
#77 Matcher Appverk\PHPMatcher\Matcher failed to match value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#78 Matcher Appverk\PHPMatcher\Matcher error: Value "bar" does not match pattern "@integer@" at path: "[foo]".
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
         *  #1 Matcher Appverk\PHPMatcher\Matcher matching value "42" with "@string@" pattern
         *  #2 Matcher Appverk\PHPMatcher\Matcher\ChainMatcher (all) matching value "42" with "@string@" pattern
         *  #3 Matcher Appverk\PHPMatcher\Matcher\ChainMatcher (scalars) can match pattern "@string@"
         *  #...
         *  #35 Matcher Appverk\PHPMatcher\Matcher error: integer "42" is not a valid string.
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
