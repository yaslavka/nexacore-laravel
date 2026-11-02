# NexaCore — Laravel 12 + Filament

Полный перенос проекта **nexaCore** на Laravel 12 (PHP 8.2): публичный сайт 1:1 (дизайн, страницы, стили, мультиязычность en/ru/ky) + админ-панель Filament с инструментами управления.

## Стек

- **Laravel 12** (PHP 8.2+), SQLite (Eloquent)
- **Filament 3** — админ-панель `/admin`
- Публичная часть — Blade-оболочка + оригинальные ассеты (styles.css, motion.css, app.js, i18n.js и т.д.), перенесённые 1:1
- JSON API с тем же контрактом, что и в оригинальном Node-сервере

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

- **Dashboard** — статистика заявок/платежей
- **Enquiries** — заявки: просмотр, статусы (new/in_progress/completed/cancelled), Export CSV, Backup JSON
- **Payments** — платёжные ссылки (сумма согласуется владельцем), чекаут-ссылка
- **Directions / Services** — редактирование каталога (EN-канонический + ru/ky оверлеи)
- **Settings** — данные компании, юридические тексты
- **Audit log** — журнал действий

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
php artisan serve   # или php -S 127.0.0.1:8090 -t public router.php
```

## Валюты

Цены показываются в USD и KGS при `1 USD = 87.5 KGS` (как в оригинале). Цены предварительные, без налогов и комиссий.

## Что дальше

- Подключение PSP (Payler Gate): sandbox/live режимы, вебхук `/api/payler/callback`, сверка платежей
- Email-уведомления по заявкам (нужен SMTP-аккаунт)
