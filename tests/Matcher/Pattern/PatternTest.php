<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher\Pattern;

use PHPUnit\Framework\Attributes\DataProvider;

use AppVerk\PHPMatcher\Matcher\Pattern\Expander\IsEmail;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\IsEmpty;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\Optional;
use AppVerk\PHPMatcher\Matcher\Pattern\TypePattern;
use PHPUnit\Framework\TestCase;

class PatternTest extends TestCase
{
    private ?TypePattern $pattern = null;

    public static function examplesProvider()
    {
        return [
            ['isEmail', true],
            ['isEmpty', true],
            ['optional', true],
            ['isUrl', false],
            ['non existing expander', false],
        ];
    }

    protected function setUp(): void
    {
        $this->pattern = new TypePattern('dummy');
        $this->pattern->addExpander(new IsEmail());
        $this->pattern->addExpander(new IsEmpty());
        $this->pattern->addExpander(new Optional());
    }

    #[DataProvider('examplesProvider')]
    public function testHasExpander($expander, $expectedResult): void
    {
        $this->assertEquals($expectedResult, $this->pattern->hasExpander($expander));
    }
}
