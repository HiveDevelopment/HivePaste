# HivePaste deployment

## Production: MariaDB recommended

Copy `.env.example` to `.env`, set a secure `APP_KEY` and choose unique strong `DB_PASSWORD` and `DB_ROOT_PASSWORD` values. Use `DB_CONNECTION=mysql`.

```sh
docker compose -f compose.yaml -f compose.mariadb.yaml up -d --build
```

The default `compose.yaml` remains SQLite-compatible for existing lightweight installations. **Changing DB_CONNECTION does not migrate existing paste data.** Back up and migrate the SQLite database before switching an existing installation to MariaDB. Do not remove its SQLite volume until verified.

## Frontend

The site uses Tailwind CSS v4 compiled during the Docker build (Node build stage). For local styling changes, run `npm install && npm run build`; generated CSS is served at `/assets/hivepaste.css`. The editor and viewer have a 1680px maximum content width.

## Privacy

The editor's IP redaction checkbox replaces valid IPv4 and IPv6 addresses in paste content before storing it. This is best-effort and does not redact hostnames, tokens, or other identifying details. API clients are not changed; they should redact their own content. Existing pastes are unchanged until edited with redaction enabled.

## Privacy and syntax highlighting

The paste editor has a **Preview IP redaction** action that sends the draft to a rate-limited, CSRF-protected endpoint and returns a preview without storing it. Applying the preview edits the local draft; the **Redact IP addresses** checkbox also applies server-side redaction on save. IP filtering is best-effort; manually check for tokens, hostnames and secrets.

The read-only paste viewer uses lightweight, escaped syntax highlighting for selected languages. This is not a full language parser. For richer language support, integrate a maintained syntax highlighter with a pinned version and local assets rather than loading untrusted scripts from a CDN.

## Validation

Run `php artisan test` and `npm run build` before release. Check the rate limiter, reverse-proxy trust settings, cleanup schedule and database backups in the deployment environment.

## Production security and operations

- Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain`, and a stable `APP_KEY`. Force HTTPS and verify forwarded proxy headers when behind Caddy/Cloudflare.
- Use MariaDB with persistent storage and scheduled, tested off-host backups. Back up the database and environment secrets before updating.
- Ensure `php artisan schedule:run` runs every minute (or run `php artisan schedule:work` as a supervised process) so expired pastes are actually removed.
- Configure Turnstile for public anonymous creation and reporting, and tune rate limits to expected traffic. Rate limiting and secret scanning are best effort, not a substitute for abuse monitoring.
- Review reports using `php artisan hivepaste:reports:list` (check `--help`), and implement an operational response process.
- Protect access logs: management links include secrets, so avoid logging URL query strings. Set short retention and do not log request bodies.
- Verify `/`, `/api/v1/pastes`, raw/download responses, and public paste URLs over HTTPS. Configure uptime checks, log alerts and capacity monitoring.
- Rebuild assets with `npm install && npm run build` and run `php artisan test` in CI before deployments.
