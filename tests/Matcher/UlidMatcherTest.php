<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Lexer;
use AppVerk\PHPMatcher\Matcher\UlidMatcher;
use AppVerk\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;
use stdClass;

class UlidMatcherTest extends TestCase
{
    private ?UlidMatcher $matcher = null;

    public static function positiveCanMatchData()
    {
        return [
            ['@ulid@'],
        ];
    }

    public static function positiveMatchData()
    {
        return [
            ['01BX5ZZKBKACTAV9WEVGEMMVRY', '@ulid@'],
            ['01BX5ZZKBKACTAV9WEVGEMMVS0', '@ulid@'],
            ['01BX5ZZKBKACTAV9WEVGEMMVS1', '@ulid@'],
            ['01BX5ZZKBKACTAV9WEVGEMMVRZ', '@ulid@'],
            ['7ZZZZZZZZZZZZZZZZZZZZZZZZZ', '@ulid@'],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ['@ulid'],
            ['ulid'],
            [1],
        ];
    }

    public static function negativeMatchData()
    {
        return [
            [1, '@ulid@'],
            [0, '@ulid@'],
            ['7b44804e-37d5-4df4-9bdd-b738d4a45bb4', '@ulid@'],
            ['01BX5ZZKBKACTAV9WEVGEMMVR=', '@ulid@'],
            ['01BX5ZZKBKACTAV9WEVGEMMVS', '@ulid@'],
            ['01BX5ZZKBKACTAV9WEVGEMMVS00', '@ulid@'],
            ['8ZZZZZZZZZZZZZZZZZZZZZZZZZ', '@ulid@'],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [new stdClass(),  '@ulid@', 'object "\\stdClass" is not a valid ULID: not a string.'],
            [1.1, '@ulid@', 'double "1.1" is not a valid ULID: not a string.'],
            [false, '@ulid@', 'boolean "false" is not a valid ULID: not a string.'],
            [1, '@ulid@', 'integer "1" is not a valid ULID: not a string.'],
            ['lorem ipsum', '@ulid@', 'string "lorem ipsum" is not a valid ULID: invalid characters.'],
            ['7b44804e-37d5-4df4-9bdd-b738d4a45bb4', '@ulid@', 'string "7b44804e-37d5-4df4-9bdd-b738d4a45bb4" is not a valid ULID: invalid characters.'],
            ['01BX5ZZKBKACTAV9WEVGEMMVR=', '@ulid@', 'string "01BX5ZZKBKACTAV9WEVGEMMVR=" is not a valid ULID: invalid characters.'],
            ['01BX5ZZKBKACTAV9WEVGEMMVS', '@ulid@', 'string "01BX5ZZKBKACTAV9WEVGEMMVS" is not a valid ULID: too short.'],
            ['01BX5ZZKBKACTAV9WEVGEMMVS00', '@ulid@', 'string "01BX5ZZKBKACTAV9WEVGEMMVS00" is not a valid ULID: too long.'],
            ['8ZZZZZZZZZZZZZZZZZZZZZZZZZ', '@ulid@', 'string "8ZZZZZZZZZZZZZZZZZZZZZZZZZ" is not a valid ULID: overflow.'],
        ];
    }

    protected function setUp(): void
    {
        $this->matcher = new UlidMatcher(
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
