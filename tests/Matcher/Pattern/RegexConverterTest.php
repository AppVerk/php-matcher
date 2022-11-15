<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher\Pattern;

use AppVerk\PHPMatcher\Matcher\Pattern\RegexConverter;
use AppVerk\PHPMatcher\Matcher\Pattern\TypePattern;
use PHPUnit\Framework\TestCase;

class RegexConverterTest extends TestCase
{
    private ?RegexConverter $converter = null;

    public function setUp() : void
    {
        $this->converter = new RegexConverter();
    }

    public function test_convert_unknown_type() : void
    {
        $this->expectException(\AppVerk\PHPMatcher\Exception\UnknownTypeException::class);

        $this->converter->toRegex(new TypePattern('not_a_type'));
    }
}
