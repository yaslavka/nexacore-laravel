<?php

namespace App\Console\Commands;

use App\Support\Themes\ThemeManager;
use Illuminate\Console\Command;

class ThemeListCommand extends Command
{
    protected $signature = 'themes:list';

    protected $description = 'List every discovered theme in the themes/ directory';

    public function handle(ThemeManager $manager): int
    {
        $themes = $manager->all();

        if ($themes === []) {
            $this->warn('No themes discovered in ['.$manager->basePath().'].');

            return self::SUCCESS;
        }

        $active = $manager->activeSlug();

        $this->table(
            ['Slug', 'Name', 'Version', 'Locales', 'Status', 'Description'],
            collect($themes)->map(fn ($theme, string $slug) => [
                $slug,
                $theme->name(),
                $theme->version(),
                implode('/', $theme->locales()),
                $slug === $active ? '<fg=green;options=bold>active</>' : '',
                $theme->description(),
            ])->all()
        );

        return self::SUCCESS;
    }
}
