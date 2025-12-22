<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher;

use AppVerk\PHPMatcher\Backtrace;
use PHPUnit\Framework\Attributes\DataProvider;
use AppVerk\PHPMatcher\Matcher\TaxNumberMatcher;
use PHPUnit\Framework\TestCase;
use stdClass;

class TaxNumberMatcherTest extends TestCase
{
    private ?TaxNumberMatcher $matcher = null;

    public static function positiveCanMatchData()
    {
        return [
            ['@tax_number@'],
        ];
    }

    public static function positiveMatchData()
    {
        return [
            ['1548703403', '@tax_number@'],
            ['154 870 34 03', '@tax_number@'],
            ['154-870-34-03', '@tax_number@'],
        ];
    }

    public static function negativeCanMatchData()
    {
        return [
            ['@tax_number'],
            ['tax_number'],
            [1],
        ];
    }

    public static function negativeMatchData()
    {
        return [
            [8185623252, '@tax_number@'],
            [5236854215, '@tax_number@'],
        ];
    }

    public static function negativeMatchDescription()
    {
        return [
            [new stdClass(),  '@tax_number@', 'object "\\stdClass" is not a valid tax number.'],
            [1.1, '@integer@', 'double "1.1" is not a valid tax number.'],
            [false, '@double@', 'boolean "false" is not a valid tax number.'],
            [1, '@array@', 'integer "1" is not a valid tax number.'],
        ];
    }

    protected function setUp(): void
    {
        $this->matcher = new TaxNumberMatcher(new Backtrace\InMemoryBacktrace());
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
