<?php

declare(strict_types=1);

namespace Baconfy\FactoryPayload;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;

class DtoAttributesResolver
{
    /** @var array<class-string, array<int, string>> */
    protected static array $cache = [];

    /** @return array<int, string> */
    public static function for(string $class): array
    {
        if (! class_exists($class)) {
            throw new InvalidArgumentException("DTO class [{$class}] does not exist.");
        }

        if (array_key_exists($class, static::$cache)) {
            return static::$cache[$class];
        }

        if (method_exists($class, 'keys')) {
            return static::$cache[$class] = array_values($class::keys());
        }

        $properties = (new ReflectionClass($class))->getProperties(ReflectionProperty::IS_PUBLIC);

        return static::$cache[$class] = array_map(
            static fn (ReflectionProperty $property): string => $property->getName(),
            $properties
        );
    }
}
