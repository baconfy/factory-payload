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
        $attributes = $this->payloadAttributes ?? PayloadAttributesResolver::for($this);

        return PayloadBuilder::build($this, $attributes, $overrides, $parent);
    }
}