<?php

declare(strict_types=1);

namespace Baconfy\FactoryPayload;

use Illuminate\Database\Eloquent\Model;

trait HasPayloadAttributes
{
    /**
     * Get an HTTP payload array for the model.
     *
     * @param  array<string, mixed>  $overrides
     * @param  Model|null  $parent
     * @return array<string, mixed>
     */
    public function payload(array $overrides = [], ?Model $parent = null): array
    {
        $attributes = $this->payloadAttributes ?? [];
        if (empty($attributes)) {
            return $overrides;
        }

        $filtered = array_intersect_key(
            $this->count(null)->raw([], $parent),
            array_flip($attributes)
        );

        return [...$filtered, ...$overrides];
    }
}