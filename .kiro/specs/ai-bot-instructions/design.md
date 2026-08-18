# AI Bot Instructions URL

## Goal
Allow an external AI agent to fetch a bot's identity and behavior rules as plain text through a token-protected URL.

## Data
Add a unique `identity_token` to `ai_bots`. It is generated as `identity_<random>` when a bot is created and can be rotated from the admin edit page. It is distinct from `api_key`.

## Endpoint
`GET /ai-bots/{aiBot}/instructions.txt?token={identity_token}` returns `text/plain; charset=UTF-8` only when the bot exists, is active, and the token matches. All other cases return HTTP 404.

## Text format
The response contains the bot name, identity, allowed actions, restricted actions, and forbidden actions. Multiline rules are rendered as bullet lists; missing fields show `（未設定）`.

## Admin UI
The edit page displays the token-protected instructions URL, offers copy-to-clipboard, and allows token rotation. Rotating a token invalidates the old URL immediately after the bot is saved.
