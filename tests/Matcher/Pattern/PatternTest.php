<?php

declare(strict_types=1);

namespace Appverk\PHPMatcher\Tests\Matcher\Pattern;

use Appverk\PHPMatcher\Matcher\Pattern\Expander\IsEmail;
use Appverk\PHPMatcher\Matcher\Pattern\Expander\IsEmpty;
use Appverk\PHPMatcher\Matcher\Pattern\Expander\Optional;
use Appverk\PHPMatcher\Matcher\Pattern\TypePattern;
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

    public function setUp() : void
    {
        $this->pattern = new TypePattern('dummy');
        $this->pattern->addExpander(new isEmail());
        $this->pattern->addExpander(new isEmpty());
        $this->pattern->addExpander(new Optional());
    }

    /**
     * @dataProvider examplesProvider
     */
    public function test_has_expander($expander, $expectedResult) : void
    {
        $this->assertEquals($expectedResult, $this->pattern->hasExpander($expander));
    }
}
