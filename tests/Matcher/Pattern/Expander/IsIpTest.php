<?php

declare(strict_types=1);

namespace Appverk\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Appverk\PHPMatcher\Backtrace;
use Appverk\PHPMatcher\Matcher\Pattern\Expander\IsIp;
use PHPUnit\Framework\TestCase;

class IsIpTest extends TestCase
{
    public static function examplesIpProvider()
    {
        return [
            ['127.0.0.1', true],
            ['255.255.255.255', true],
            ['2001:0db8:0000:42a1:0000:0000:ab1c:0001', true],
            ['999.999.999.999', false],
            ['127.127', false],
            ['foo:bar:42:42', false],
        ];
    }

    /**
     * @dataProvider examplesIpProvider
     */
    public function test_ip($ip, $expected) : void
    {
        $expander = new IsIp();
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expected, $expander->match($ip));
    }
}
