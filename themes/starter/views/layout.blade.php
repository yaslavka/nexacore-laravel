<!doctype html>
{{-- Starter theme — example of an alternative, statically rendered layout.
     New themes can copy this folder and change the markup freely. --}}
<html lang="{{ $lang ?? 'en' }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="{{ $description ?? '' }}">
  <title>{{ $title ?? 'NexaCore' }}</title>
  <link rel="icon" type="image/svg+xml" href="{{ theme_asset('assets/favicon.svg', 'nexacore') }}">
  <link rel="stylesheet" href="{{ theme_asset('css/theme.css') }}">
</head>
<body>
@php $base = (($lang ?? 'en') === 'en') ? '' : '/'.($lang ?? 'en'); @endphp
<header class="starter-header">
  <a class="starter-brand" href="{{ $base ?: '/' }}"><span aria-hidden="true">◆</span> NexaCore</a>
  <nav aria-label="Main">
    <a href="{{ $base }}/services">{{ __('Services') }}</a>
    <a href="{{ $base }}/contact">{{ __('Contact') }}</a>
    <a href="{{ $base }}/payment">{{ __('Payment') }}</a>
  </nav>
</header>

<main class="starter-shell">
  <section class="starter-hero">
    <p class="starter-badge">
      {{ __('Theme') }}: {{ $theme?->name() ?? 'starter' }} · v{{ $theme?->version() ?? '0.1.0' }}
    </p>
    <h1>{{ __('A different way to present the same business.') }}</h1>
    <p class="starter-lead">
      {{ __('This page is rendered by the active theme. Copy themes/starter, edit theme.json and views/layout.blade.php to ship your own layout variant — no core changes needed.') }}
    </p>
    <div class="starter-actions">
      <a class="starter-btn" href="{{ $base }}/services">{{ __('Browse services') }}</a>
      <a class="starter-btn starter-btn--ghost" href="{{ $base }}/company">{{ __('About the company') }}</a>
    </div>
  </section>
</main>

<footer class="starter-footer">
  <p>© {{ date('Y') }} NexaCore LLC · {{ __('Starter theme demo') }}</p>
</footer>
</body>
</html>
