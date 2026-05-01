<?php

declare(strict_types=1);

namespace Baconfy\FactoryPayload;

use Illuminate\Database\Eloquent\Model;

trait HasPayloadAttributes
{
    /**
     * Get an HTTP payload array for the model.
     *
     * @param  array<string, mixed>|class-string  $argument
     * @param  Model|null  $parent
     * @return array<string, mixed>
     */
    public function payload(array|string $argument = [], ?Model $parent = null): array
    {
        if (is_string($argument)) {
            return PayloadBuilder::build($this, DtoAttributesResolver::for($argument), [], $parent);
        }

        $attributes = $this->payloadAttributes ?? PayloadAttributesResolver::for($this);

        return PayloadBuilder::build($this, $attributes, $argument, $parent);
    }
}