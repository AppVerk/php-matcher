<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Value;

use AppVerk\PHPMatcher\Matcher\Pattern\Assert\Json;

final readonly class SingleLineString implements \Stringable
{
    public function __construct(private string $raw)
    {
    }

    #[\Override]
    public function __toString(): string
    {
        $normalized = $this->raw;

        if (Json::isValid($this->raw)) {
            $normalized = Json::reformat($this->raw);
        } elseif (Json::isValid(Json::transformPattern($this->raw))) {
            $normalized = Json::reformat(Json::transformPattern($this->raw));
        }

        return str_replace(["\r\n", "\r", "\n"], ' ', $normalized);
    }
}
