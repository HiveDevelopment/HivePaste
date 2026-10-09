# HivePanel public sharing integration

This update adds `POST /api/v1/integrations/hivepanel/pastes`, an **unauthenticated public upload endpoint**. It does not modify the existing bearer-token API.

## Deployment

1. Deploy updated HivePaste files, keeping your production `.env` unchanged.
2. Configure a persistent cache shared across all HivePaste instances (`CACHE_STORE=database` with a working cache table, or Redis). **Do not use `array`** for production rate limits.
3. Set `HIVEPASTE_HIVEPANEL_PUBLIC_ENABLED=true` in the HivePaste `.env` after reviewing your anti-abuse controls.
4. Run `php artisan optimize:clear` and check `php artisan route:list --path=api`.
5. Test using `curl -i -X POST https://paste.hivepanel.dev/api/v1/integrations/hivepanel/pastes -H 'Accept: application/json' -H 'Content-Type: application/json' -d '{"title":"test.log","content":"Server started","language":"log"}'`. Expect 201 with `url`, `id`, and `expires_at`.

## Limits and security

- Defaults: 512 KiB max, 5 uploads/minute/IP, 50/day/IP, 300/hour globally, 2000/day globally. Tune for your hosting usage.
- Expiration is forced to 7 days, visibility to unlisted, no public delete/edit credential is issued.
- Existing secret detection and input validation still apply.
- These are basic safeguards, **not sufficient alone for an internet-facing anonymous upload service**. Add edge bot mitigation, bandwidth/storage monitoring, and an emergency disable procedure before wide release. IP limits may affect multiple users behind one hosting IP.
- Keep `HIVEPASTE_HIVEPANEL_PUBLIC_ENABLED=false` until ready.
- HivePanel should call this endpoint server-side and must ask for confirmation before uploading files/logs.
