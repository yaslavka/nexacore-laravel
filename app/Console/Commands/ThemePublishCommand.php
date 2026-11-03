<?php

namespace App\Console\Commands;

use App\Support\Themes\ThemeManager;
use Illuminate\Console\Command;

class ThemePublishCommand extends Command
{
    protected $signature = 'themes:publish {--force : Recreate existing copies}';

    protected $description = 'Expose every theme\'s public assets at /themes/{slug}';

    public function handle(ThemeManager $manager): int
    {
        $published = $manager->publishAssets((bool) $this->option('force'));

        if ($published === []) {
            $this->info('All theme assets are already published.');

            return self::SUCCESS;
        }

        $this->info('Published: '.implode(', ', $published));

        return self::SUCCESS;
    }
}
