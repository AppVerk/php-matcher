<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\PHPMatcher;
use PHPUnit\Framework\TestCase;

final class EmptyPatternsTest extends TestCase
{
    protected ?PHPMatcher $matcher = null;

    public static function emptyPatternString()
    {
        return [
            [
                '', '', true,
                '123', '', false,
                ' ', '', false,
                null, '', false,
                1, '', false,
                0, '', false,
                '{"name": "123"}', '{"name": ""}', false,
                '{"name": ""}', '{"name": ""}', true,
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->matcher = new PHPMatcher();
    }

    #[DataProvider('emptyPatternString')]
    public function testEmptyPatternInTheJson($value, $pattern, $expectedResult): void
    {
        $match = $this->matcher->match($value, $pattern);
        $this->assertSame($expectedResult, $match);
    }
}
