# Montreal UBF

A bilingual (English / French) Laravel web app for the Montreal University Bible Fellowship community — home page, about/events/giving pages, a Bible study library with downloadable resources, and an authenticated dashboard for members to manage their profile and avatar.

See [project-spec.md](project-spec.md) for the original product spec (the 2026 Francophone Bible Conference site) that this project grew out of.

## Tech Stack

- **Backend:** Laravel 13 (PHP 8.3), SQLite by default
- **Frontend:** Blade views, Alpine.js, Tailwind CSS v4, Vite
- **Testing:** PHPUnit

## Requirements

- PHP 8.3+
- Composer
- Node.js + npm
- [Laravel Herd](https://herd.laravel.com/) (recommended for local dev on this project) or any PHP dev server

## Getting Started

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate
```

### Run the app

If you're using Herd, the site is served automatically at its configured `.test` domain. Otherwise, run everything (server, queue listener, Vite) together:

```bash
composer dev
```

Or individually:

```bash
php artisan serve
php artisan queue:listen
npm run dev
```

### Build frontend assets for production

```bash
npm run build
```

## Testing

```bash
composer test
```

## Localization

Routes and content are duplicated for English (`en_CA`) and French (`fr_CA`) — e.g. `/events` and `/evenements`. Translation strings live in [lang/en_CA](lang/en_CA) and [lang/fr_CA](lang/fr_CA). Language switching is handled by `SwitchLanguageController` via `/language/{locale}`.

## Key Features

- **Public pages** — Home, About, Events, Giving, Bible Books, Study Series, and Bible Studies, each with EN/FR routes.
- **Bible study library** — `BibleStudyController` and related models (`BibleBook`, `StudySeries`, `BibleStudy`, `StudyAttachment`) power a browsable library of studies with downloadable attachments.
- **Questionnaire/PDF viewer** — `QuestionnaireController` serves PDF resources via `/view-pdf/{dir}/{filename}` (and the French equivalent).
- **Authentication** — Email/password login (`LoginController`) protecting the member dashboard.
- **Member dashboard** — Authenticated users can update their profile and upload/stream an avatar image (`ProfileController`, `DashboardController`).

## Project Structure Notes

- `app/Http/Controllers` — one controller per feature area (see routes above).
- `app/Models` — Eloquent models for Bible content (`BibleBook`, `BibleStudy`, `StudySeries`, `StudyAttachment`) and `User`.
- `resources/views/pages` and `resources/views/resources` — Blade views grouped by feature.
- `database/migrations` — includes the base Laravel tables plus Bible content tables and a migration adding roles/avatar support to users.

### Maintenance routes (dev only)

`routes/web.php` contains a commented-out block of helper routes for local/dev use — `/reset-migrations`, `/fresh-migrations`, `/run-migrations`, `/run-seeders`. They wrap `artisan migrate`/`db:seed` over HTTP and are disabled by default; uncomment them only for local/dev use, and never leave them enabled in a public environment since they're unauthenticated.
