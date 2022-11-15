<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher\Pattern;

use AppVerk\PHPMatcher\Backtrace;

interface PatternExpander
{
    public static function is(string $name) : bool;

    public function setBacktrace(Backtrace $backtrace) : void;

    public function match($value) : bool;

    public function getError() : ?string;
}
