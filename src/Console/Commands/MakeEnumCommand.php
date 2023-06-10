<?php

namespace TiagoF2\Enums\Console\Commands;

use Illuminate\Console\Command;
use TiagoF2\Enums\Support\Stub;
use TiagoF2\Enums\Support\Package;
use TiagoF2\Enums\Support\ConsoleText;
use Symfony\Component\Console\Input\InputOption;

class MakeEnumCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:enum {name : The enum name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new enum class.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = str($this->argument('name'))->studly()->toString();

        $translations = \array_map(
            fn ($langCode) => [
                'description' => "Translation file for {$langCode}",
                'stubPath' => 'translation.stub',
                'destination' => lang_path(
                    \sprintf(
                        "{$langCode}/enums/%s.php",
                        str($name)->snake()->append('_enum')->toString()
                    )
                ),
                'strListToReplace' => [
                    '{{name}}' => $name,
                    '{{langCode}}' => $langCode,
                    '{{UPPER_SNAKE_NAME}}' => str($name)->snake()->upper()->toString(),
                ],
                'replaceFile' => $this->option('force'),
            ],
            \array_filter(
                \array_unique(
                    \array_values([
                        config('app.locale'),
                        config('app.fallback_locale'),
                    ])
                ),
                'trim'
            )
        );

        $sources = [
            [
                'description' => 'Enum class',
                'stubPath' => 'EnumClass.stub',
                'destination' => \app_path("Enums/{$name}Enum.php"),
                'strListToReplace' => [
                    '{{name}}' => $name,
                    '{{UPPER_SNAKE_NAME}}' => str($name)->snake()->upper()->toString(),
                ],
                'replaceFile' => $this->option('force'),
            ],
            ...$translations
        ];

        foreach ($sources as $source) {
            if (\file_exists(($source['destination'])) && !($source['replaceFile'] ?? \null)) {
                $this->error("File '{$source['destination']}' exists.");

                continue;
            }

            $result = Stub::generateFile(
                $source['stubPath'],
                $source['destination'],
                $source['strListToReplace'],
                $source['replaceFile'],
            );

            echo \PHP_EOL;

            if ($result === false) {
                ConsoleText::line('Fail on creating file ' . $source['destination']);
                continue;
            }

            $destination = trim(
                \str_replace(\base_path(), '', $source['destination']),
                '/'
            );

            $description = trim((string) ($source['description'] ?? \null));

            if ($description) {
                $this->info($description);
            }

            $this->info("File: '{$destination}' created successfully.");
        }
    }

    protected function configure()
    {
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Replace file id exists.');
    }
}
