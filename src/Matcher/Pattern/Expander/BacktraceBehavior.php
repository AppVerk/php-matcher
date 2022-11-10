<?php

declare(strict_types=1);

namespace Appverk\PHPMatcher\Matcher\Pattern\Expander;

use Appverk\PHPMatcher\Backtrace;

trait BacktraceBehavior
{
    protected Backtrace $backtrace;

    public function setBacktrace(Backtrace $backtrace) : void
    {
        $this->backtrace = $backtrace;
    }
}
