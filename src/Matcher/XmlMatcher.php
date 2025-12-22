<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Matcher;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\Pattern\Assert\Xml;
use Coduo\ToString\StringConverter;
use LSS\XML2Array;

use function sprintf;

final class XmlMatcher extends Matcher
{
    public function __construct(private readonly ArrayMatcher $arrayMatcher, private readonly Backtrace $backtrace)
    {
    }

    #[\Override]
    public function match(mixed $value, mixed $pattern): bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        if (parent::match($value, $pattern)) {
            $this->backtrace->matcherSucceed(self::class, $value, $pattern);

            return true;
        }

        if (!Xml::isValid($value) || !Xml::isValid($pattern)) {
            $this->error = "Value or pattern are not valid XML's";
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        $arrayValue = XML2Array::createArray($value);
        $arrayPattern = XML2Array::createArray($pattern);

        $match = $this->arrayMatcher->match($arrayValue, $arrayPattern);

        if (!$match) {
            $this->error = sprintf(
                'Value %s does not match pattern %s',
                new StringConverter($value),
                new StringConverter($pattern),
            );

            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        $this->backtrace->matcherSucceed(self::class, $value, $pattern);

        return true;
    }

    #[\Override]
    public function canMatch(mixed $pattern): bool
    {
        $result = Xml::isValid($pattern);
        $this->backtrace->matcherCanMatch(self::class, $pattern, $result);

        return $result;
    }
}
