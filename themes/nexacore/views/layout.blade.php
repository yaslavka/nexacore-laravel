<!doctype html>
{{-- NexaCore Classic theme — SPA shell. --}}
<html lang="{{ $lang }}" translate="yes">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#123A6E">
  <meta name="description" content="{{ $description }}">
  <title>{{ $title }}</title>
  <link rel="icon" type="image/svg+xml" href="{{ theme_asset('assets/favicon.svg') }}">
  <link rel="preload" href="{{ theme_asset('assets/inter-variable.ttf') }}" as="font" type="font/ttf" crossorigin>
  <link rel="stylesheet" href="{{ theme_asset('css/styles.css') }}?v=20261101-payler">
  <link rel="stylesheet" href="{{ theme_asset('css/motion.css') }}">
  <link rel="stylesheet" href="{{ theme_asset('css/admin.css') }}?v=20261101-payler">
  <script type="module" src="{{ theme_asset('js/app.js') }}?v=20261101-payler"></script>
</head>
<body data-theme-base="{{ theme_asset('') }}">
  <div class="scroll-progress" aria-hidden="true"></div>
  <a class="skip-link" href="#main">Skip to content</a>
  <header class="site-header" id="header"></header>
  <main id="main" tabindex="-1"><div class="loading-state">Loading NexaCore…</div></main>
  <footer id="footer"></footer>
  <dialog id="order-dialog" aria-labelledby="order-title"></dialog>
  <div id="toast" class="toast" role="status" aria-live="polite"></div>
</body>
</html>
