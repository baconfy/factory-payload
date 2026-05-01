<?php

declare(strict_types=1);

namespace Baconfy\FactoryPayload;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class PayloadBuilder
{
    /**
     * @param  array<int, string>  $attributes
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public static function build(Factory $factory, array $attributes, array $overrides = [], ?Model $parent = null): array
    {
        if (empty($attributes)) {
            return $overrides;
        }

        $filtered = array_intersect_key(
            $factory->count(null)->raw([], $parent),
            array_flip($attributes)
        );

        return [...$filtered, ...$overrides];
    }
}
