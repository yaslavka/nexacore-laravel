<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
use Illuminate\View\View;

class PageController extends Controller
{
    private const DEFAULT_TITLE = 'NexaCore — technology that moves business forward';
    private const DEFAULT_DESCRIPTION = 'NexaCore LLC provides IT services and business consulting remotely. Explore 12 areas of expertise with indicative pricing in USD and KGS.';

    public function home(string $locale): View
    {
        return $this->render($locale);
    }

    public function catalog(string $locale, ?string $categoryId): View
    {
        if ($categoryId === null) {
            return $this->render($locale);
        }

        $category = Category::query()->find($categoryId);
        if (! $category) {
            return $this->render($locale, 'Page not found — NexaCore', 'The requested page could not be found.');
        }

        $overlay = $category->translation($locale);

        return $this->render(
            $locale,
            (data_get($overlay, 'title') ?: $category->title).' — NexaCore',
            (string) (data_get($overlay, 'description') ?: $category->description)
        );
    }

    public function service(string $locale, string $serviceId): View
    {
        $service = Service::query()->find($serviceId);
        if (! $service) {
            return $this->render($locale, 'Page not found — NexaCore', 'The requested page could not be found.');
        }

        $overlay = $service->translation($locale);

        return $this->render(
            $locale,
            (data_get($overlay, 'title') ?: $service->title).' — NexaCore',
            (string) (data_get($overlay, 'description') ?: $service->description)
        );
    }

    public function page(string $locale, string $slug): View
    {
        return $this->render($locale);
    }

    public function checkout(string $locale, string $token): View
    {
        return $this->render($locale, 'NexaCore — secure payment', 'Secure payment for your agreed NexaCore services.');
    }

    private function render(string $locale, ?string $title = null, ?string $description = null): View
    {
        $locales = site_locales();

        return theme_view('layout', [
            'lang' => in_array($locale, $locales, true) ? $locale : default_site_locale(),
            'title' => $title ?? self::DEFAULT_TITLE,
            'description' => $description ?? self::DEFAULT_DESCRIPTION,
            'theme' => theme(),
        ]);
    }
}
