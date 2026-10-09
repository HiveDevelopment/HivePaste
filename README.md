# HivePaste

HivePaste is an account-free Laravel 12 paste service. Visitors can create and share code or logs anonymously. API integrations use **host-issued** bearer tokens, not user accounts.

## Install

Requirements: PHP 8.2+, Composer 2, Laravel-compatible PHP extensions and SQLite or MySQL/MariaDB.

```bash
composer install
cp .env.example .env
php artisan key:generate
# For SQLite: touch database/database.sqlite
php artisan migrate
php artisan serve
```

Set `APP_URL` to the public HTTPS URL and `APP_DEBUG=false` in production. Configure your web server document root as `public/`. Run `php artisan schedule:run` every minute (cron) for expired-paste cleanup. The ZIP excludes Composer dependencies.

## Anonymous paste workflow

Anyone can create a paste via the web editor. Pastes default to unlisted: anyone who knows the public URL can read them. On creation, HivePaste displays a **one-time private management link**. Store it safely to edit/delete the paste later, including from a different browser or device. The management secret is randomly generated and only its SHA-256 hash is stored. Losing the link means losing edit/delete access. No user registration, login, dashboard or browser-session ownership is required.

Management URLs contain secrets: never share them, post them in logs, or forward them to third parties. The management pages use `Referrer-Policy: no-referrer`. Public paste URLs never contain management keys.

## Host API tokens

```bash
php artisan hivepaste:token:create --name=hivepanel
php artisan hivepaste:token:list
php artisan hivepaste:token:revoke 1
```

Tokens are displayed only at creation, stored hashed and can be revoked by numeric ID. Use a token on the **server side**, never embed it in public JavaScript.

```bash
curl -X POST https://paste.example.com/api/v1/pastes \
  -H 'Authorization: Bearer hp_YOUR_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"title":"Console log","content":"Server started","language":"log","expires_in":"7d"}'
```

`GET /api/v1/pastes/{slug}` returns a non-expired public/unlisted paste; `DELETE /api/v1/pastes/{slug}` requires the original API token.

## Host configuration

Configure in `.env`:

```dotenv
HIVEPASTE_ANONYMOUS_ENABLED=true
HIVEPASTE_API_ENABLED=true
HIVEPASTE_MAX_PASTE_BYTES=524288
HIVEPASTE_WEB_RATE_LIMIT=10
HIVEPASTE_API_RATE_LIMIT=30
HIVEPASTE_DEFAULT_EXPIRATION=7d
HIVEPASTE_ALLOW_NEVER_EXPIRE=false
```

Use `php artisan config:clear` after changing configuration if config is cached. Web uploads are rate-limited per IP; API uploads per token. Configure trusted proxies, web-server body size limits and PHP `post_max_size` accordingly.

## Security / launch checklist

- Never paste credentials or personal data. **Secret redaction is not yet implemented**; users must remove sensitive values themselves.
- Unlisted does not mean encrypted or private. There is no public discovery directory yet.
- For a public service, add CAPTCHA or abuse detection, abuse reporting and moderation, plus logging/retention policies before launch.
- Management links are bearer secrets; anyone holding one has edit/delete access. Avoid analytics and third-party scripts on management pages.
- Enforce HTTPS, sensible request limits, secure session cookies and proxy limits.

Run `php artisan test` after installing dependencies. The source archive does not include `vendor/`.

## Docker deployment (single container, SQLite)

The included Compose stack runs Nginx, PHP-FPM and Laravel's scheduler in one container. SQLite and runtime storage are persisted in named volumes. Put a TLS reverse proxy in front of the service; the default port binds to localhost only.

```bash
cp .env.example .env
# Set APP_ENV=production, APP_DEBUG=false, APP_URL=https://paste.example.com
# Generate APP_KEY with: php -r 'echo "base64:".base64_encode(random_bytes(32)).PHP_EOL;'
# Paste that value into APP_KEY in .env. Keep it safe and never rotate it casually.
docker compose up -d --build
docker compose logs -f hivepaste
```

Do not delete the `hivepaste_database` volume when upgrading. The scheduler deletes expired pastes hourly. Configure HTTPS and reverse proxy trust for correct public URLs; do not expose port 8080 publicly without a TLS proxy.

The paste viewer includes a Download action, served as `text/plain` with `nosniff` to avoid executing user-provided HTML.

## Anonymous upload abuse protection

Web uploads and reports remain anonymous. Optional Cloudflare Turnstile can be enabled by setting **both** `HIVEPASTE_TURNSTILE_SITE_KEY` and `HIVEPASTE_TURNSTILE_SECRET_KEY` in `.env`; only the public site key is rendered to visitors. Turnstile verification is server-side and fails closed when enabled. API uploads continue to use host-managed API tokens and do not use Turnstile.

`HIVEPASTE_SECRET_DETECTION=true` rejects content matching common credential patterns before storage (including private key headers and common access token formats). This is a heuristic, not a guarantee; users must still review their logs. Set it to `false` to disable. It applies to web and API uploads and edits.

Anyone viewing a paste can report it. Reports are rate limited with `HIVEPASTE_REPORT_RATE_LIMIT` (per IP, per hour), include a hidden bot trap, and store a keyed hash of the reporter IP rather than the address itself. Host operators can review reports via `php artisan hivepaste:reports`. Reports **do not automatically delete content**; hosts must review and act through their own moderation process.

Run `php artisan migrate --force` after upgrading. Do not regenerate `APP_KEY` on an existing installation. For a public service, configure HTTPS, an upstream proxy/body-size limit, reliable backups, a content moderation/contact policy, and a privacy notice.

## Release and integrations

See `API.md` and `openapi.json` for API integration, `DEPLOYMENT.md` for production requirements. CSS is generated with `npm ci && npm run build`.
