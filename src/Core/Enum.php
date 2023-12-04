<?php

namespace TiagoF2\Enums\Core;

use TiagoF2\Helpers\StringHelpers;
use TiagoF2\Helpers\CollectionSearch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use TiagoF2\Helpers\LaravelHelpers;
use Illuminate\Support\Facades\App;

abstract class Enum implements EnumInterface
{
    protected static bool $clearCache = false;

    // const ABERTO = 1;

    protected static array $enums = [
        /**
         * Note:
         * need be 'self', not 'static'
         */

        // self::ABERTO => 'Aberto',
    ];

    public static function get(
        int|null $enum,
        bool $tranlate = true,
        ?string $locale = null
    ): string|null {
        return static::getValue($enum, $tranlate, $locale);
    }

    public static function getValue(
        int|null $enum,
        bool $tranlate = true,
        ?string $locale = null
    ): string|null {
        $value = static::$enums[$enum] ?? null;

        if (!$value || !$tranlate) {
            return $value;
        }

        return static::trans($value, $locale);
    }

    public static function getEnum(string|null $value): int|null
    {
        if (!$value) {
            return null;
        }

        $value = trim($value);

        return array_flip(static::$enums)[$value] ?? null;
    }

    public static function enums(
        bool|null $onlyIds = false,
        bool|null $tranlate = null,
        string|null $locale = null,
        bool $updateCache = false
    ): array {
        return static::enumList(
            $onlyIds,
            $tranlate,
            $locale,
            $updateCache
        );
    }

    /**
     * enumList function
     *
     * @param boolean $onlyIds
     * @param boolean|null|null $tranlate
     * @param string|null|null $locale
     * @param boolean $updateCache
     * @return array
     */
    public static function enumList(
        bool|null $onlyIds = false,
        bool|null $tranlate = null,
        string|null $locale = null,
        bool $updateCache = false
    ): array {
        if ($onlyIds) {
            return array_keys(static::$enums);
        }

        if (!$tranlate) {
            return static::$enums ?? [];
        }

        $locale ??= App::getLocale();

        $cacheKey = \Str::slug(static::class . "_enum_list_locale-{$locale}");

        if ($updateCache) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, 300 /*secs*/, function () use ($locale) {
            return  collect(
                static::$enums ?? []
            )->map(
                fn ($transKey) => static::trans($transKey, $locale)
            )->all();
        });
    }

    public static function enumExists(int $enum): bool
    {
        return in_array($enum, array_keys(static::$enums));
    }

    public static function cached()
    {
        $className = StringHelpers::classNameSlug(static::class);

        $cacheKey = "{$className}_enum_list";

        if (static::$clearCache) {
            Cache::forget($cacheKey);
        }

        $data = Cache::remember($cacheKey, 3600 /*secs*/, fn () => static::enumList(null, true));

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
        ?string $locale = null
    ) {
        //App\Enums\PlanEnum, App\Enums\PlanEnum::class, App\Enums\PlanEnum or plan_enum
        $className = StringHelpers::classNameSlug(static::class);

        $snakeCaseOfClass = Str::snake($className); // OperationEnum -> operation_enum

        $locale ??= App::getLocale();

        // The translation file must be here:
        // resources/lang/[LANG]/enums/operation_enum.php

        return \str_replace(
            [
                "{$snakeCaseOfClass}."
            ],
            '',
            LaravelHelpers::trans("{$snakeCaseOfClass}.{$key}", [], "{$locale}/enums")
        );
    }

    public static function transByEnum(int $enum, ?string $locale = null)
    {
        return static::trans(static::getValue($enum), $locale);
    }

    public static function clearCache(bool $clearCache = false)
    {
        static::$clearCache = $clearCache;
    }
}
