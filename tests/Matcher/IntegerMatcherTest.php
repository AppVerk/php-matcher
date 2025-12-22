<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Lexer;
use AppVerk\PHPMatcher\Matcher\IntegerMatcher;
use AppVerk\PHPMatcher\Parser;
use DateTime;
use PHPUnit\Framework\TestCase;
use stdClass;

class IntegerMatcherTest extends TestCase
{
    private ?IntegerMatcher $matcher = null;

    public static function positiveCanMatchData()
    {
        return [
            ['@integer@'],
        ];
    }

    public static function positiveMatchData()
    {
        return [
            [10, '@integer@'],
            [10, '@integer@.lowerThan(50).greaterThan(1)'],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ['@integer'],
            ['integer'],
            [1],
        ];
    }

    public static function negativeMatchData()
    {
        return [
            ['1', '@integer@'],
            [new DateTime(),  '@integer@'],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [new stdClass(),  '@integer@', 'object "\\stdClass" is not a valid integer.'],
            [1.1, '@integer@', 'double "1.1" is not a valid integer.'],
            [false, '@integer@', 'boolean "false" is not a valid integer.'],
            [['test'], '@integer@', 'array "Array(1)" is not a valid integer.'],
        ];
    }

    protected function setUp(): void
    {
        $this->matcher = new IntegerMatcher(
            $backtrace = new Backtrace\InMemoryBacktrace(),
            new Parser(new Lexer(), new Parser\ExpanderInitializer($backtrace)),
        );
    }

    #[DataProvider('positiveCanMatchData')]
    public function testPositiveCanMatches($pattern): void
    {
        $this->assertTrue($this->matcher->canMatch($pattern));
    }

    #[DataProvider('negativeCanMatchData')]
    public function testNegativeCanMatches($pattern): void
    {
        $this->assertFalse($this->matcher->canMatch($pattern));
    }

    #[DataProvider('positiveMatchData')]
    public function testPositiveMatch($value, $pattern): void
    {
        $this->assertTrue($this->matcher->match($value, $pattern));
    }

    #[DataProvider('negativeMatchData')]
    public function testNegativeMatch($value, $pattern): void
    {
        $this->assertFalse($this->matcher->match($value, $pattern));
    }

    #[DataProvider('negativeMatchDescription')]
    public function testNegativeMatchDescription($value, $pattern, $error): void
    {
        $this->matcher->match($value, $pattern);
        $this->assertEquals($error, $this->matcher->getError());
    }
}
