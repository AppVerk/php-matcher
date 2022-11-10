<?php

declare(strict_types=1);

namespace Appverk\PHPMatcher;

interface Factory
{
    public function createMatcher(Backtrace $backtrace) : Matcher;
}
