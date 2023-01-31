<?php

namespace TiagoF2\Enums;

trait HasEnum
{
    public static function enums()
    {
        $instance = new static();
        $enumClassName =
            property_exists(static, 'enumClass') &&
            $instance->enumClass &&
            \is_string($instance->enumClass)
            ? $instance->enumClass
            : \null;
    }
}
