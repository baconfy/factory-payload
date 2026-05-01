<?php

declare(strict_types=1);

namespace Baconfy\FactoryPayload;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class FactoryPayloadServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (Factory::hasMacro('payload')) {
            return;
        }

        Factory::macro('payload', function (array|string $argument = [], ?Model $parent = null): array {
            if (is_string($argument)) {
                return PayloadBuilder::build($this, DtoAttributesResolver::for($argument), [], $parent);
            }

            return PayloadBuilder::build($this, PayloadAttributesResolver::for($this), $argument, $parent);
        });
    }
}
