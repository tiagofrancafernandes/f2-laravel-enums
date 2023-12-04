<?php

namespace TiagoF2\Enums\Support;

use TiagoF2\Enums\Support\Package;
use TiagoF2\Enums\Support\ConsoleText;
use Illuminate\Support\Facades\Log;

class Stub
{
    /**
     * function getStubsPathOnApp
     *
     * @return string
     */
    public static function getStubsPathOnApp(): string
    {
        return \Illuminate\Support\Facades\App::basePath('stubs/f2-enums');
    }

    /**
     * function getStubsPathOnPackagePath
     *
     * @return string
     */
    public static function getStubsPathOnPackagePath(): string
    {
        return Package::packageBasePath('stubs');
    }

    /**
     * function getStubPath
     *
     * @param string $stub
     *
     * @return string
     */
    public static function getStubPath(string $stub): string
    {
        if (\file_exists(static::getStubsPathOnApp() . "/{$stub}")) {
            return static::getStubsPathOnApp() . "/{$stub}";
        }

        return static::getStubsPathOnPackagePath() . "/{$stub}";
    }

    /**
     * function getStubContents
     *
     * @param string $stub
     *
     * @return string
     */
    public static function getStubContents(string $stub): string
    {
        return file_get_contents(static::getStubPath($stub));
    }

    /**
     * function getFinalContent
     *
     * @param string $stubPath
     * @param array $strListToReplace
     *
     * @return string
     */
    public static function getFinalContent(string $stubPath, array $strListToReplace): string
    {
        $stub = static::getStubContents($stubPath);

        return str_replace(
            \array_keys($strListToReplace),
            \array_values($strListToReplace),
            $stub
        );
    }

    /**
     * generateFile function
     *
     * @param string $stubPath
     * @param string $destination
     * @param array $strListToReplace
     * @param boolean $replaceFile
     * @return void
     */
    public static function generateFile(
        string $stubPath,
        string $destination,
        array $strListToReplace,
        bool $replaceFile = false
    ): ?bool {
        try {
            if (\file_exists($destination) && !$replaceFile) {
                ConsoleText::line('', "File '{$destination}' exists," . '');

                return false;
            }

            $dirname = pathinfo($destination, PATHINFO_DIRNAME);

            if (!\is_dir($dirname)) {
                \mkdir($dirname, 0755, true);
            }

            return (bool) file_put_contents(
                $destination,
                static::getFinalContent($stubPath, $strListToReplace)
            );
        } catch (\Throwable $th) {
            Log::error($th);
            if (!\Illuminate\Support\Facades\App::isProduction()) {
                throw $th;
            }

            return false;
        }
    }
}
