<?php

namespace TiagoF2\Enums\Core;

use TiagoF2\Enums\Core\EnumInterface;
use TiagoF2\Enums\Core\Enum;

trait HasEnum
{
    /**
     * enum function
     *
     * @param boolean $safeMode
     *
     * @return Enum|null
     */
    public static function enum(bool $safeMode = false)
    {
        $classToGetEnum = static::class;
        $instance = new static();

        $enumClass =
            property_exists($instance, 'enumClass') &&
            $instance->enumClass &&
            \is_string($instance->enumClass)
            ? $instance->enumClass
            : "{$classToGetEnum}Enum";

        if (!class_exists($enumClass)) {
            if ($safeMode) {
                return \null;
            }

            throw new \Exception(
                "{$enumClass} do not exists. "
                    . "Create {$enumClass} or add 'enumClass' property on {$classToGetEnum} class."
            );
        }

        $implementsInterface = in_array(EnumInterface::class, class_implements($enumClass), true);

        if (!$implementsInterface) {
            if ($safeMode) {
                return \null;
            }

            throw new \Exception("{$enumClass} do not implements " . EnumInterface::class);
        }

        return new $enumClass();
    }

    /**
     * enumList function
     *
     * @param bool $onlyIds
     * @param boolean $safeMode
     *
     * @return array|null
     */
    public static function enumList(bool $onlyIds = false, bool $safeMode = false): array
    {
        /**
         * @var Enum $enum
         */
        $enum = static::enum($safeMode);

        return (array) ($enum ? $enum->enumList($onlyIds) : []);
    }
}
