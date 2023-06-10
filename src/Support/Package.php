<?php

namespace TiagoF2\Enums\Support;

class Package
{
    /**
     * function packageBasePath
     *
     * @param string $path
     * @param bool $realpath
     *
     * @return string
     */
    public static function packageBasePath(string $path = '', bool $realpath = \true): string
    {
        if (!$realpath) {
            return __DIR__ . "/../../{$path}";
        }

        return \realpath(
            __DIR__ . "/../../{$path}"
        );
    }
}
