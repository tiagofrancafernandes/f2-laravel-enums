<?php

namespace TiagoF2\Enums\Core;

use BackedEnum;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Contracts\Database\Eloquent\Castable;

class CastBackedEnum implements Castable
{
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @template TEnum of \UnitEnum|\BackedEnum
     *
     * @param  array{class-string<TEnum>}  $arguments
     * @return \Illuminate\Contracts\Database\Eloquent\CastsAttributes<\Illuminate\Support\Collection<array-key, TEnum>, iterable<TEnum>>
     */
    public static function castUsing(array $arguments)
    {
        $arguments = $arguments ?: [static::class];
        return new class($arguments) implements CastsAttributes
        {
            protected $arguments;

            public function __construct(array $arguments)
            {
                $this->arguments = $arguments;
            }

            public function get($model, $key, $value, $attributes)
            {
                if (!isset($attributes[$key]) || is_null($attributes[$key])) {
                    return;
                }

                $enumClass = $this->arguments[0] ?? null;

                if (!$enumClass) {
                    return null;
                }

                return is_subclass_of($enumClass, BackedEnum::class)
                    ? $enumClass::from($value)
                    : constant($enumClass . '::' . $value);
            }

            public function set($model, $key, $value, $attributes)
            {
                $value = $value !== null
                    ? $this->getStorableEnumValue($value)
                    : null;

                return [$key => $value];
            }

            public function serialize($model, string $key, $value, array $attributes)
            {
                return $this->getStorableEnumValue($value);
            }

            protected function getStorableEnumValue($enum)
            {
                if (is_string($enum) || is_int($enum)) {
                    return $enum;
                }

                return $enum instanceof BackedEnum ? $enum->value : $enum->name;
            }
        };
    }
}
