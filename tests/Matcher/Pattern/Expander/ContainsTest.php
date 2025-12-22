<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher\Pattern\Expander;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\Contains;
use DateTime;
use PHPUnit\Framework\TestCase;

class ContainsTest extends TestCase
{
    public static function examplesCaseSensitiveProvider()
    {
        return [
            ['ipsum', 'lorem ipsum', true],
            ['wor', 'this is my hello world string', true],
            ['lol', 'lorem ipsum', false],
            ['NO', 'norbert', false],
        ];
    }

    public static function examplesCaseInsensitiveProvider()
    {
        return [
            ['IpSum', 'lorem ipsum', true],
            ['wor', 'this is my hello WORLD string', true],
            ['lol', 'LOREM ipsum', false],
            ['NO', 'NORBERT', true],
        ];
    }

    public static function invalidCasesProvider()
    {
        return [
            ['ipsum', 'hello world', "String \"hello world\" doesn't contains \"ipsum\"."],
            ['lorem', new DateTime(), 'Contains expander require "string", got "\\DateTime".'],
        ];
    }

    #[DataProvider('examplesCaseSensitiveProvider')]
    public function testMatchingValuesCaseSensitive($needle, $haystack, $expectedResult): void
    {
        $expander = new Contains($needle);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($haystack));
    }

    #[DataProvider('examplesCaseInsensitiveProvider')]
    public function testMatchingValuesCaseInsensitive($needle, $haystack, $expectedResult): void
    {
        $expander = new Contains($needle, true);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($haystack));
    }

    #[DataProvider('invalidCasesProvider')]
    public function testErrorWhenMatchingFail($string, $value, $errorMessage): void
    {
        $expander = new Contains($string);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }
}
