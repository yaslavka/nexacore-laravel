<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Service;

class CatalogService
{
    /**
     * Build the catalog exactly like the original server: EN canonical,
     * ru/ky overlay titles, descriptions, durations and deliverables while
     * keeping ids, prices and English package names.
     */
    public function get(?string $lang = null): array
    {
        $locale = in_array($lang, ['ru', 'ky'], true) ? $lang : 'en';

        $categories = Category::query()
            ->orderBy('number')
            ->with('services')
            ->get();

        $result = [
            'categories' => $categories->map(function (Category $category) use ($locale) {
                $overlay = $category->translation($locale);

                return [
                    'id' => $category->id,
                    'number' => $category->number,
                    'title' => data_get($overlay, 'title') ?: $category->title,
                    'shortTitle' => data_get($overlay, 'shortTitle') ?: $category->short_title,
                    'description' => data_get($overlay, 'description') ?: $category->description,
                    'icon' => $category->icon,
                    'services' => $category->services->map(fn (Service $service) => $this->serviceArray($service, $locale))->values()->all(),
                ];
            })->all(),
        ];

        return $result;
    }

    public function serviceArray(Service $service, ?string $locale = 'en'): array
    {
        $overlay = $service->translation($locale);

        $packages = collect($service->packages ?? [])->map(function (array $item, int $index) use ($overlay) {
            $translated = data_get($overlay, "packages.{$index}.description");

            return [
                'name' => $item['name'] ?? '',
                'description' => $translated ?: ($item['description'] ?? ''),
                'price' => $item['price'] ?? null,
            ];
        })->values()->all();

        $deliverables = collect($service->deliverables ?? [])->values()->all();
        if ($locale !== 'en' && is_array(data_get($overlay, 'deliverables')) && count(data_get($overlay, 'deliverables'))) {
            $deliverables = data_get($overlay, 'deliverables');
        }

        return [
            'id' => $service->id,
            'categoryId' => $service->category_id,
            'title' => data_get($overlay, 'title') ?: $service->title,
            'description' => data_get($overlay, 'description') ?: $service->description,
            'duration' => data_get($overlay, 'duration') ?: $service->duration,
            'period' => $service->period ?? '',
            'price' => $service->price,
            'priceLabel' => $service->price_label,
            'deliverables' => $deliverables,
            'packages' => $packages,
        ];
    }

    public function findService(string $id): ?Service
    {
        return Service::query()->find($id);
    }

    public function findCategory(string $id): ?Category
    {
        return Category::query()->find($id);
    }
}
