<?php

namespace TiagoF2\Enums\Support;

class ConsoleText
{
    /**
     * function line
     *
     * @param ...string $content
     * @return void
     */
    public static function line(string ...$content): void
    {
        if (!$content) {
            return;
        }

        foreach ($content as $line) {
            \Symfony\Component\VarDumper\VarDumper::dump(
                new \Symfony\Component\VarDumper\Caster\ScalarStub($line)
            );
        }
    }

    /**
     * function dump
     *
     * @param ...string $content
     * @return void
     */
    public static function dump(string ...$content): void
    {
        if (!$content) {
            return;
        }

        foreach ($content as $line) {
            \Symfony\Component\VarDumper\VarDumper::dump(
                $line
            );
        }
    }
}
