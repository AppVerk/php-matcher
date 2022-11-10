<?php

declare(strict_types=1);

namespace Appverk\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Appverk\PHPMatcher\Backtrace;
use Appverk\PHPMatcher\Matcher\Pattern\Expander\IsEmail;
use PHPUnit\Framework\TestCase;

class IsEmailTest extends TestCase
{
    public static function examplesEmailsProvider()
    {
        return [
            ['valid@email.com', true],
            ['valid+12345@email.com', true],
            ['...@domain.com', false],
            ['2222----###@domain.co', true],
        ];
    }

    /**
     * @dataProvider examplesEmailsProvider
     */
    public function test_emails($email, $expectedResult) : void
    {
        $expander = new IsEmail();
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($email));
    }
}
