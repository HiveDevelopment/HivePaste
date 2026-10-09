# HivePaste API v1

Base URL: `https://YOUR_DOMAIN/api/v1`

## Authentication

Create an API token on the server using `php artisan hivepaste:token:create` (check the command help for arguments). Pass the token using `Authorization: Bearer YOUR_TOKEN`. Token required for creation and deletion. Do not embed an API token in public browser code.

## Endpoints

- `POST /pastes` - create paste (authenticated); returns HTTP 201 with `id`, `url`, `raw_url`, `title`, `language`, `visibility`, `expires_at`.
- `GET /pastes/{uuid}` - fetch a non-expired paste, including content (no token required).
- `DELETE /pastes/{uuid}` - delete a paste created by the authenticated API token.

### Create example

```bash
curl -X POST https://YOUR_DOMAIN/api/v1/pastes \
  -H 'Authorization: Bearer YOUR_TOKEN' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"title":"Example log","content":"Server started","language":"log","expires_in":"7d"}'
```

Supported expiration: `1h`, `1d`, `7d`, `30d`, and `never` if enabled. All new pastes are unlisted, identified by a UUID. Anonymous web paste creation is separate from API token authentication. API rate limits are configured through `HIVEPASTE_API_RATE_LIMIT`. Public GET responses should be treated as sensitive data if you share secret content.

## Integration readiness

The endpoints exist, but a production integration should additionally validate token rotation, request limits, CORS requirements, stable error responses, versioning guarantees, and automated contract tests. API tokens are server-side credentials.

## OpenAPI contract

`openapi.json` describes request and response shapes and authentication for integrations. Import it into Swagger UI, Postman, or an SDK generator after replacing the example server URL. The API is versioned under `/api/v1`; do not expose bearer tokens in client-side JavaScript.

## Token rotation

Create a replacement with `php artisan hivepaste:token:create --name=integration-next`, deploy the replacement to the integration, verify it works, then revoke the old token using `php artisan hivepaste:token:revoke`. Run `php artisan hivepaste:token:list` to audit active tokens.
