<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private const LOCALE_KEYS = ['ru' => 'catalog.source-ru.json', 'ky' => 'catalog.ky.json'];

    public function run(): void
    {
        $source = $this->readCatalog('catalog.json');
        $overlays = [];
        foreach (self::LOCALE_KEYS as $locale => $file) {
            $overlays[$locale] = $this->readCatalog($file);
        }

        foreach ($source['categories'] as $index => $categoryData) {
            $category = Category::query()->updateOrCreate(
                ['id' => $categoryData['id']],
                [
                    'number' => $categoryData['number'] ?? $index + 1,
                    'title' => $categoryData['title'],
                    'short_title' => $categoryData['shortTitle'] ?? null,
                    'description' => $categoryData['description'] ?? '',
                    'icon' => $categoryData['icon'] ?? null,
                ]
            );

            foreach (array_keys(self::LOCALE_KEYS) as $locale) {
                $overlayCategory = $this->findOverlay($overlays[$locale], $categoryData['id']);
                if ($overlayCategory) {
                    $category->{$locale} = [
                        'title' => $overlayCategory['title'] ?? '',
                        'shortTitle' => $overlayCategory['shortTitle'] ?? '',
                        'description' => $overlayCategory['description'] ?? '',
                    ];
                    $category->save();
                }
            }

            foreach ($categoryData['services'] as $serviceIndex => $serviceData) {
                $service = Service::query()->updateOrCreate(
                    ['id' => $serviceData['id']],
                    [
                        'category_id' => $categoryData['id'],
                        'sort_order' => $serviceIndex,
                        'title' => $serviceData['title'],
                        'description' => $serviceData['description'] ?? '',
                        'duration' => $serviceData['duration'] ?? null,
                        'period' => $serviceData['period'] ?? '',
                        'price' => $serviceData['price'] ?? null,
                        'price_label' => $serviceData['priceLabel'] ?? null,
                        'packages' => $serviceData['packages'] ?? [],
                        'deliverables' => $serviceData['deliverables'] ?? [],
                    ]
                );

                foreach (array_keys(self::LOCALE_KEYS) as $locale) {
                    $overlayService = $this->findOverlay($overlays[$locale], $serviceData['id']);
                    if ($overlayService) {
                        $service->{$locale} = [
                            'title' => $overlayService['title'] ?? '',
                            'description' => $overlayService['description'] ?? '',
                            'duration' => $overlayService['duration'] ?? '',
                            'deliverables' => $overlayService['deliverables'] ?? [],
                            'packages' => collect($overlayService['packages'] ?? [])
                                ->map(fn ($item) => ['description' => $item['description'] ?? ''])->all(),
                        ];
                        $service->save();
                    }
                }
            }
        }

        Setting::firstOrCreate(['id' => 1], Setting::defaults());

        User::query()->firstOrCreate(
            ['email' => 'owner@nexacore.kg'],
            [
                'name' => 'NexaCore Owner',
                'password' => bcrypt('NexaCore2026!'),
                'role' => 'owner',
            ]
        );
        User::query()->firstOrCreate(
            ['email' => 'editor@nexacore.kg'],
            [
                'name' => 'NexaCore Editor',
                'password' => bcrypt('NexaCoreEdit!'),
                'role' => 'editor',
            ]
        );
    }

    private function readCatalog(string $file): array
    {
        $path = database_path("seeders/data/{$file}");
        if (!file_exists($path)) {
            return ['categories' => []];
        }

        return json_decode((string) file_get_contents($path), true) ?: ['categories' => []];
    }

    private function findOverlay(array $catalog, string $id): ?array
    {
        foreach ($catalog['categories'] as $category) {
            if ($category['id'] === $id) {
                return $category;
            }
            foreach ($category['services'] ?? [] as $service) {
                if ($service['id'] === $id) {
                    return $service;
                }
            }
        }

        return null;
    }
}
