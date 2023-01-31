<?php

namespace TiagoF2\Enums;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use TiagoF2\Helpers\StringHelpers;
use TiagoF2\Helpers\CollectionSearch;

class Enum implements EnumInterface
{
    protected static bool $clearCache = false;

    //const ABERTO         = 1;

    protected static array $enums = [
        //static::ABERTO          => 'Aberto',
    ];

    public static function get(
        int|null $enum,
        bool $tranlate = true,
        string $locale = null
    ): string|null {
        return static::getValue($enum, $tranlate, $locale);
    }

    public static function getValue(
        int|null $enum,
        bool $tranlate = true,
        string $locale = null
    ): string|null {
        $value = static::$enums[$enum] ?? null;
        if (!$value || !$tranlate) {
            return $value;
        }

        return static::trans($value, $locale);
    }

    public static function getEnum(string|null $value): int|null
    {
        $value = trim($value);

        return array_flip(static::$enums)[$value] ?? null;
    }

    public static function enums(
        bool|null $only_ids = false,
        bool|null $tranlate = null,
        string|null $locale = null,
        bool $update_cache = false
    ): array {
        return static::enumList(
            $only_ids,
            $tranlate,
            $locale,
            $update_cache
        );
    }

    public static function enumList(
        bool|null $only_ids = false,
        bool|null $tranlate = null,
        string|null $locale = null,
        bool $update_cache = false
    ): array {
        if ($only_ids) {
            return array_keys(static::$enums);
        }

        if (!$tranlate) {
            return static::$enums ?? [];
        }

        $locale = $locale ?? app()->getLocale();

        $cache_key = Str::slug(static::class . "_enum_list_locale-{$locale}");

        if ($update_cache) {
            Cache::forget($cache_key);
        }

        return Cache::remember($cache_key, 300 /*secs*/, function () use ($locale) {
            $collection = collect(static::$enums ?? [])->map(function ($trans_key) use ($locale) {
                return static::trans($trans_key, $locale);
            });

            return $collection->all();
        });
    }

    public static function enumExists(int $enum): bool
    {
        return in_array($enum, array_keys(static::$enums));
    }

    public static function cached()
    {
        $class_name = StringHelpers::classNameSlug(static::class);

        $cache_key = "{$class_name}_enum_list";

        if (static::$clearCache) {
            $cache = Cache::forget($cache_key);
        }

        $data = Cache::remember($cache_key, 3600 /*secs*/, function () {
            return static::enumList(null, true);
        });

        return collect($data ?? []);
    }

    public static function search(string $search, bool $exact = false, bool $first = false)
    {
        $itemCollection = static::cached();

        $filtered = CollectionSearch::filterLike($itemCollection, null, $search, $exact);

        if ($first) {
            return $filtered->chunk(1)->first() ?? null;
        }

        return $filtered ?? null;
    }

    public static function trans(
        string $key,
        string $locale = null
    ) {
        //App\Enums\PlanEnum, App\Enums\PlanEnum::class, App\Enums\PlanEnum or plan_enum
        $class_name = StringHelpers::classNameSlug(static::class);

        $class_tranlation_key = Str::snake($class_name); // OperationEnum -> operation_enum

        return __("enum.{$class_tranlation_key}.{$key}", [], $locale);
    }

    public static function transByEnum(int $enum, string $locale = null)
    {
        return static::trans(static::getValue($enum), $locale);
    }

    public static function clearCache(bool $clearCache = false)
    {
        static::$clearCache = $clearCache;
    }
}
