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

        Factory::macro('payload', function (array $overrides = [], ?Model $parent = null): array {
            return PayloadBuilder::build($this, PayloadAttributesResolver::for($this), $overrides, $parent);
        });
    }
}
