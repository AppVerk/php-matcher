<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher\Pattern\Expander;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\NotContains;
use DateTime;
use PHPUnit\Framework\TestCase;

class NotContainsTest extends TestCase
{
    public static function examplesCaseSensitiveProvider()
    {
        return [
            ['ipsum', 'lorem ipsum', false],
            ['wor', 'this is my hello world string', false],
            ['lol', 'lorem ipsum', true],
            ['NO', 'norbert', true],
        ];
    }

    public static function examplesCaseInsensitiveProvider()
    {
        return [
            ['IpSum', 'lorem ipsum', false],
            ['wor', 'this is my hello WORLD string', false],
            ['lol', 'LOREM ipsum', true],
            ['NO', 'NORBERT', false],
        ];
    }

    public static function invalidCasesProvider()
    {
        return [
            ['ipsum', 'lorem ipsum', 'String "lorem ipsum" contains "ipsum".'],
            ['lorem', new DateTime(), 'Not contains expander require "string", got "\\DateTime".'],
        ];
    }

    #[DataProvider('examplesCaseSensitiveProvider')]
    public function testMatchingValuesCaseSensitive($needle, $haystack, $expectedResult): void
    {
        $expander = new NotContains($needle);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($haystack));
    }

    #[DataProvider('examplesCaseInsensitiveProvider')]
    public function testMatchingValuesCaseInsensitive($needle, $haystack, $expectedResult): void
    {
        $expander = new NotContains($needle, true);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($haystack));
    }

    #[DataProvider('invalidCasesProvider')]
    public function testErrorWhenMatchingFail($string, $value, $errorMessage): void
    {
        $expander = new NotContains($string);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }
}
