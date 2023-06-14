<?php

namespace TiagoF2\Enums\Core;

interface EnumInterface
{
    public static function getValue(int|null $enum, bool $tranlate = true, ?string $locale = null): string|null;

    public static function getEnum(string|null $value): int|null;

    public static function enumList(bool $only_ids = false): array;

    public static function enumExists(int $enum): bool;
}
