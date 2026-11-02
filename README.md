# NexaCore — Laravel 12 + Filament

Полный перенос проекта **nexaCore** на Laravel 12 (PHP 8.2): публичный сайт 1:1 (дизайн, страницы, стили, мультиязычность en/ru/ky) + админ-панель Filament с инструментами управления.

**Дизайн вынесен в отдельную тему** (`themes/nexacore`). Система тем позволяет добавлять любое количество тем с различными вариантами вёрстки и переключать активную тему из админ-панели (`/admin` → System → Themes).

## Стек

- **Laravel 12** (PHP 8.2+), SQLite (Eloquent)
- **Filament 3** — админ-панель `/admin`
- **Система тем** — каждая тема в отдельной папке со своими Blade-шаблонами и ассетами
- Публичная часть — Blade-оболочка + оригинальные ассеты (styles.css, motion.css, app.js, i18n.js и т.д.), перенесённые 1:1
- JSON API с тем же контрактом, что и в оригинальном Node-сервере

## Система тем

Структура каждой темы:

```
themes/
├── nexacore/               # активная тема (1:1 дизайн оригинала)
│   ├── theme.json          # манифест темы
│   ├── views/              # Blade-шаблоны (layout.blade.php)
│   └── public/             # ассеты → /themes/nexacore/…
│       ├── css/            # styles.css, motion.css, admin.css
│       ├── js/             # app.js, i18n.js, checkout.js, …
│       └── assets/         # шрифты, favicon, платёжные иконки
└── starter/                # шаблон для новых тем (статичная вёрстка)
    ├── theme.json
    ├── views/layout.blade.php
    └── public/css/theme.css
```

Манифест `theme.json`:

```json
{
    "slug": "nexacore",
    "name": "NexaCore Classic",
    "version": "1.0.0",
    "description": "…",
    "author": "…",
    "locales": ["en", "ru", "ky"]
}
```

**Как добавить новую тему:** скопируйте `themes/starter`, задайте уникальный `slug` в `theme.json`, измените `views/layout.blade.php` и ассеты. Тема появится в `/admin` → System → Themes и в `php artisan themes:list` — ядро менять не нужно.

**Переключение темы:** админ-панель (`System → Themes`) или `settings.active_theme` в БД. Фолбэк: env `THEME_ACTIVE` → первый найденный манифест.

**Хелперы (доступны в шаблонах темы):**

- `theme()` — объект активной темы (`->name()`, `->version()`, `->slug()`)
- `theme_view('layout', [...])` — рендер шаблона активной темы (namespace `{slug}::*`)
- `theme_asset('css/styles.css')` — URL ассета активной темы (`/themes/{slug}/css/styles.css`)
- `theme_asset('assets/favicon.svg', 'nexacore')` — ассет конкретной темы

**Artisan-команды:**

- `php artisan themes:list` — список найденных тем
- `php artisan themes:publish [--force]` — публикация ассетов (`public/themes/{slug}` — симлинки; при недоступности symlink — копирование)

## Страницы (всё как в оригинале)

- `/` — главная (hero, 12 направлений, о компании, процесс, FAQ, CTA)
- `/services`, `/services/{category}`, `/service/{id}` — каталог 92 услуг, поиск
- `/contact` — форма заявки (диалог, 3 шага)
- `/privacy`, `/terms`, `/offer`, `/payment`, `/orders`, `/refunds`, `/remote`, `/company` — юридические страницы
- `/checkout/{token}` — страница оплаты (PSP Payler подключается отдельно)
- `/admin` — Filament-панель (только английский, как в оригинале)
- Локали: `/` (en), `/ru/…`, `/ky/…`; канонический английский, ru/ky — оверлеи
- `/sitemap.xml`, `/robots.txt`

## API (контракт 1:1 с оригиналом)

- `GET /api/health`, `GET /api/settings`, `GET /api/catalog?lang=en|ru|ky`
- `POST /api/orders` — заявка (rate limit 20/час)
- `GET /api/payments/{token}`, `POST /api/payments/{token}/start|status`
- `POST /api/payler/callback` — вебхук Payler (следующий этап)

## Админ-панель (`/admin`)

- **Мультиязычность панели** — интерфейс админки переводится на все языки сайта (en / ru / ky). Переключение: раздел **«Язык панели»** (System → Admin language) или локаль пользователя в БД (`users.locale`). Филаментовские строки для кыргызского — собственные переводы в `lang/vendor/filament-*/ky/…`
- **Управление языками** — раздел **Languages**: добавление, редактирование, включение/отключение языков сайта, выбор языка по умолчанию (защищён от удаления/деактивации). Активные языки автоматически используются публичным сайтом (маршруты, sitemap, API) и переключателем панели
- **Карточки вместо таблиц** — все списки (Enquiries, Payments, Directions, Services, Audit log, Languages) отображаются карточками с пагинацией (10/25/50 на страницу) — адаптируются под мобильные (1 колонка) и десктоп (2–3 колонки)
- **Dashboard** — статистика заявок/платежей
- **Enquiries** — заявки: просмотр, статусы (new/in_progress/completed/cancelled), Export CSV, Backup JSON
- **Payments** — платёжные ссылки (сумма согласуется владельцем), чекаут-ссылка
- **Directions / Services** — редактирование каталога (EN-канонический + ru/ky оверлеи)
- **Themes** — переключение активной темы сайта
- **Settings** — данные компании, юридические тексты
- **Audit log** — журнал действий (в т.ч. смена темы, языка панели, языков сайта)

Роли: `owner` (всё), `editor` (каталог и заявки), `viewer` (только просмотр).

## Учётные записи (созданы сеялкой)

| Роль   | Email                | Пароль        |
|--------|----------------------|---------------|
| owner  | owner@nexacore.kg    | NexaCore2026! |
| editor | editor@nexacore.kg   | NexaCoreEdit! |

⚠️ Смените пароли в продакшене.

## Запуск

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan themes:publish
php artisan serve   # или php -S 127.0.0.1:8090 -t public router.php
```

## Валюты

Цены показываются в USD и KGS при `1 USD = 87.5 KGS` (как в оригинале). Цены предварительные, без налогов и комиссий.

## Что дальше

- Подключение PSP (Payler Gate): sandbox/live режимы, вебхук `/api/payler/callback`, сверка платежей
- Email-уведомления по заявкам (нужен SMTP-аккаунт)
