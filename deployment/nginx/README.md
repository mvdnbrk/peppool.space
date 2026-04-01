# Nginx Configuration

Custom nginx configuration snippets for the production server.

## Directory Structure

### `before-server/`

Included in the `http` context, before the `server` block. Used for directives like `map` that must be defined at the http level.

### Usage

The main `server` block and `location ~ \.php$` block must reference the `$frame_options` variable:

```nginx
# In the server block and/or php location block:
add_header X-Frame-Options $frame_options;
add_header X-XSS-Protection "1; mode=block";
add_header X-Content-Type-Options "nosniff";
```

## Notes

- `content-framing.conf` removes the `X-Frame-Options` header from `/content/*` responses. Inscription content is rendered inside sandboxed iframes (`sandbox="allow-scripts"`), which gives them a null origin — causing the browser to block them when `X-Frame-Options: SAMEORIGIN` is set.
- `$request_uri` is used instead of `$uri` because `try_files` rewrites `$uri` to `/index.php` before the PHP location block processes it.
