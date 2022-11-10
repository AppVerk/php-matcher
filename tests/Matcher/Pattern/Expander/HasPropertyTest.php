<?php

declare(strict_types=1);

namespace Appverk\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Appverk\PHPMatcher\Backtrace;
use Appverk\PHPMatcher\Matcher\Pattern\Expander\HasProperty;
use PHPUnit\Framework\TestCase;

class HasPropertyTest extends TestCase
{
    public static function examplesProvider()
    {
        return [
            ['property', '{"property":1}', true],
            ['property', '{"property_02":1}', false],
            ['property', ['property' => 1], true],
            ['property', ['property_02' => 1], false],
            ['property', '{"object_nested":{"property": 1}}', false],
        ];
    }

    /**
     * @dataProvider examplesProvider
     */
    public function test_examples($propertyName, $value, $expectedResult) : void
    {
        $expander = new HasProperty($propertyName);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($value));
    }
}
