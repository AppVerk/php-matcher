<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\Pattern\Expander;

use AppVerk\PHPMatcher\Backtrace;

trait BacktraceBehavior
{
    protected Backtrace $backtrace;

    public function setBacktrace(Backtrace $backtrace) : void
    {
        $this->backtrace = $backtrace;
    }
}
