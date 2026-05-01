<?php

declare(strict_types=1);

namespace Baconfy\FactoryPayload;

use Baconfy\FactoryPayload\Attributes\PayloadAttributes;
use Illuminate\Database\Eloquent\Factories\Factory;
use ReflectionClass;

class PayloadAttributesResolver
{
    /** @var array<class-string, array<int, string>> */
    protected static array $cache = [];

    /** @return array<int, string> */
    public static function for(Factory $factory): array
    {
        $class = $factory::class;

        if (array_key_exists($class, static::$cache)) {
            return static::$cache[$class];
        }

        $reflection = new ReflectionClass($class);

        do {
            $attributes = $reflection->getAttributes(PayloadAttributes::class);

            if ($attributes !== []) {
                return static::$cache[$class] = $attributes[0]->newInstance()->attributes;
            }
        } while ($reflection = $reflection->getParentClass());

        return static::$cache[$class] = [];
    }
}
