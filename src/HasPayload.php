<?php

declare(strict_types=1);

namespace Baconfy\FactoryPayload;

use Illuminate\Database\Eloquent\Model;

trait HasPayload
{
    /**
     * The model attributes that should be included in HTTP payloads.
     *
     * @var array<int, string>
     */
    protected array $payloadAttributes = [];

    /**
     * Get an HTTP payload array for the model.
     *
     * @param  array<string, mixed>  $overrides
     * @param  \Illuminate\Database\Eloquent\Model|null  $parent
     * @return array<string, mixed>
     */
    public function payload(array $overrides = [], ?Model $parent = null): array
    {
        if (empty($this->payloadAttributes)) {
            return $overrides;
        }

        $filtered = array_intersect_key($this->count(null)->raw([], $parent), array_flip($this->payloadAttributes));

        return array_merge($filtered, $overrides);
    }
}