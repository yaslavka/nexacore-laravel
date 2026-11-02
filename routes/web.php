<?php

use App\Http\Controllers\PageController;
use App\Services\CatalogService;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

/**
 * Public page routes are registered three times: for English (no prefix),
 * Russian (/ru/…) and Kyrgyz (/ky/…), mirroring the original routing.
 */
foreach (['' => 'en', 'ru' => 'ru', 'ky' => 'ky'] as $prefix => $locale) {
    Route::prefix($prefix)->group(function () use ($locale) {
        Route::get('/', fn (PageController $controller) => $controller->home($locale));
        Route::get('/services', fn (PageController $controller) => $controller->catalog($locale, null));
        Route::get('/services/{categoryId}', fn (PageController $controller, string $categoryId) => $controller->catalog($locale, $categoryId));
        Route::get('/service/{serviceId}', fn (PageController $controller, string $serviceId) => $controller->service($locale, $serviceId));
        Route::get('/contact', fn (PageController $controller) => $controller->page($locale, 'contact'));
        Route::get('/privacy', fn (PageController $controller) => $controller->page($locale, 'privacy'));
        Route::get('/terms', fn (PageController $controller) => $controller->page($locale, 'terms'));
        Route::get('/offer', fn (PageController $controller) => $controller->page($locale, 'offer'));
        Route::get('/payment', fn (PageController $controller) => $controller->page($locale, 'payment'));
        Route::get('/orders', fn (PageController $controller) => $controller->page($locale, 'orders'));
        Route::get('/refunds', fn (PageController $controller) => $controller->page($locale, 'refunds'));
        Route::get('/remote', fn (PageController $controller) => $controller->page($locale, 'remote'));
        Route::get('/company', fn (PageController $controller) => $controller->page($locale, 'company'));
        Route::get('/checkout/{token}', fn (PageController $controller, string $token) => $controller->checkout($locale, $token))->where('token', '[a-f0-9]{64}');
    });
}

Route::get('/robots.txt', fn () => response(
    "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /api/\nSitemap: ".rtrim(config('app.url'), '/')."/sitemap.xml\n",
    200,
    ['Content-Type' => 'text/plain; charset=utf-8']
));

Route::get('/sitemap.xml', function (CatalogService $catalog) {
    $base = rtrim(config('app.url'), '/');
    $categories = $catalog->get('en')['categories'];
    $paths = [];
    $bases = ['/', '/services', '/contact', '/privacy', '/offer', '/payment', '/orders', '/refunds', '/remote', '/company'];
    foreach ($categories as $category) {
        $bases[] = '/services/'.$category['id'];
        foreach ($category['services'] as $service) {
            $bases[] = '/service/'.$service['id'];
        }
    }
    foreach (['en', 'ru', 'ky'] as $locale) {
        foreach ($bases as $path) {
            $paths[] = $locale === 'en' ? $path : '/'.$locale.($path === '/' ? '' : $path);
        }
    }
    $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
        .collect($paths)->map(fn ($path) => '<url><loc>'.e($base.$path).'</loc></url>')->implode('')
        .'</urlset>';

    return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
});
