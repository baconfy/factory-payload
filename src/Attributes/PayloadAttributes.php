<?php

declare(strict_types=1);

namespace Baconfy\FactoryPayload\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class PayloadAttributes
{
    /** @var array<int, string> */
    public array $attributes;

    /** @param array<int, string>|string ...$attributes */
    public function __construct(array|string ...$attributes)
    {
        if (is_array($attributes[0] ?? null)) {
            $this->attributes = array_values($attributes[0]);

            return;
        }

        $this->attributes = $attributes;
    }
}
