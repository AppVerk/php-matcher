<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher;

interface Factory
{
    public function createMatcher(Backtrace $backtrace) : Matcher;
}
