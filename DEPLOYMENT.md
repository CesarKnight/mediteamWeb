# Deploying to tecnoweb.org.bo/inf513/grupo11sc/proyecto2/

Runbook for deploying this app to the university VPS at
`https://www.tecnoweb.org.bo/inf513/grupo11sc/proyecto2/`. Access is via a Cockpit web panel
(includes a real terminal, i.e. shell access as the group's own Linux user) and FTP.

## Constraint that shapes everything below

**The group's home directory itself is the Apache document root** for
`https://www.tecnoweb.org.bo/inf513/grupo11sc/` (confirmed: `cd ..` from home shows sibling
group folders like `grupo10sc`, `grupo12sc`). There's no `public_html` wrapper, and no way to
place files "above" the web root. Any file under the home directory is potentially fetchable by
direct URL unless something actively blocks it — including `.env` (DB password), `vendor/`, and
app source. The layout below keeps the real Laravel app in a directory covered by a deny-all
`.htaccess`, and exposes only Laravel's `public/` folder (via a symlink) at the target URL.

## 1. Pre-deploy code fixes (already applied in this repo)

- `vite.config.ts` — `base: '/inf513/grupo11sc/proyecto2/build/'` added to `defineConfig`, so
  built asset URLs carry the correct subdirectory prefix.
- `resources/views/app.blade.php` — favicon/apple-touch-icon links use `asset()` instead of
  hardcoded root-relative paths (`/favicon.ico` would resolve against the domain root, not the
  subdirectory, and 404).

If either of these ever gets reverted, redo them before deploying.

## 2. Production `.env` values (create directly on the VPS — never commit this file)

```env
APP_NAME="Mediteam"                                                        # avoids session-cookie name collisions with sibling groups running the same starter kit on the same domain
APP_ENV=production
APP_DEBUG=false                                                            # CRITICAL — leaks stack traces/secrets on error pages if left true
APP_URL=https://www.tecnoweb.org.bo/inf513/grupo11sc/proyecto2             # must include "www." — must match the browsed URL exactly
ASSET_URL=https://www.tecnoweb.org.bo/inf513/grupo11sc/proyecto2

LOG_LEVEL=warning

SESSION_PATH=/inf513/grupo11sc/proyecto2                                  # scopes the session cookie to this app instead of the whole tecnoweb.org.bo domain
```
Keep the rest (the `pgsql` DB connection, `APP_KEY`, `SESSION_DRIVER=database`, `PAGOFACIL_*`
vars) as already configured. Don't run `key:generate` if `APP_KEY` is already set — regenerating
it invalidates already-encrypted data/sessions.

> **Why `APP_URL` must include `www.`**: `config/filesystems.php`'s `public` disk builds its URL
> as `rtrim(env('APP_URL'),'/').'/storage'` — a literal env read, not derived from the live
> request — so `Storage::url()` and any mail link rendered outside an HTTP request (queued jobs,
> artisan commands) will use exactly what's in `APP_URL`.
>
> **Why `APP_URL`'s path must be correct *before* building assets**: Wayfinder's Vite plugin runs
> `php artisan wayfinder:generate` as its first build step, reading `config('app.url')` to compute
> the path prefix baked into every generated JS route helper. If `.env` isn't correct yet when
> `npm run build` runs, rerun the build after fixing it — there's no other way to regenerate those
> files.

## 3. VPS folder layout

```
~/apps/proyecto2/          <- full Laravel app (git clone target)
~/apps/.htaccess            <- "Require all denied" — blocks direct HTTP access to app source/.env/vendor
~/proyecto2                 <- symlink -> ~/apps/proyecto2/public  (this is what Apache serves at .../grupo11sc/proyecto2/)
```
Standard "symlink the public folder out" pattern for Laravel on shared hosting where the docroot
can't be moved. `public/index.php` uses only relative `__DIR__` requires, so it resolves correctly
through the symlink. Preferred over copying `public/*` up and patching `index.php`'s paths, since
that copy step would need to be redone by hand on every future deploy.

**Verify before relying on this** (via the Cockpit terminal):
```bash
php -i | grep open_basedir     # should be empty, or scoped to your whole home dir — not just the docroot
```

## 4. First-time deploy (run via Cockpit terminal)

### 4.0 Check what's available
```bash
which git composer node npm php
php -v                       # need 8.3+ (VPS reports 8.4.22 — OK)
php -m | grep -i pgsql       # confirm pdo_pgsql is loaded
node -v; npm -v; composer -V
```
If `git`/`composer`/`node`/`npm` are missing with no way to install them, build locally instead
(with the production `.env` in place locally, since Wayfinder bakes `APP_URL`'s path into the JS
bundle at build time) and upload `vendor/` + `public/build/` via FTP.

### 4.1 Clone the repo
```bash
mkdir -p ~/apps
git clone https://github.com/CesarKnight/mediteamWeb.git ~/apps/proyecto2
cd ~/apps/proyecto2
```

### 4.2 Block direct access to the app folder
Create `~/apps/.htaccess`:
```apache
Require all denied
```

### 4.3 PHP dependencies
```bash
composer install --no-dev --optimize-autoloader
```

### 4.4 Place the production `.env`
Create `~/apps/proyecto2/.env` with the values from section 2, then:
```bash
chmod 600 .env
```

### 4.5 Run database migrations
```bash
php artisan migrate --force
```
Also creates the `sessions`/`cache`/`jobs` tables (all set to the `database` driver).

### 4.6 Storage symlink
```bash
php artisan storage:link
```

### 4.7 Frontend build — must happen after `.env` is in place
```bash
npm ci
npm run build
```

### 4.8 Create the public-facing symlink
```bash
ln -s ~/apps/proyecto2/public ~/proyecto2
```
If `~/proyecto2` already exists as a plain directory from earlier experiments, inspect it first
(`ls -la ~/proyecto2`) before removing anything.

### 4.9 Production caches
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 4.10 Permissions
```bash
chmod -R 775 storage bootstrap/cache
```
If this is a per-user PHP-FPM/suPHP setup, default permissions from `git clone` are probably
already sufficient — treat this as a safety net. If you see 500s with permission errors in
`storage/logs/laravel.log`, revisit this step.

## 5. Redeploying after future `git push`

```bash
cd ~/apps/proyecto2
git pull origin main

composer install --no-dev --optimize-autoloader
php artisan migrate --force

npm ci
npm run build

php artisan optimize:clear
php artisan optimize
```
`.env` is not tracked in git and persists across pulls — no changes needed there unless you're
introducing new config values.

## 6. Verification checklist

1. Visit `https://www.tecnoweb.org.bo/inf513/grupo11sc/proyecto2/` — a 403/500 instead of the app
   means the symlink isn't being followed (Apache `FollowSymLinks` or `open_basedir` issue) — fall
   back to copying `public/*` up with patched `index.php` paths instead.
2. Try `https://www.tecnoweb.org.bo/inf513/grupo11sc/apps/proyecto2/.env` directly in a browser —
   must return 403. Also try `.../apps/proyecto2/composer.json` and `.../apps/proyecto2/vendor/autoload.php`.
3. DevTools Network tab on page load: `favicon.ico`, `favicon.svg`, `apple-touch-icon.png`, and the
   Vite `build/assets/*.js`/`*.css` files should all return 200 with the `.../proyecto2/...` prefix.
4. Trigger a Wayfinder-driven action (e.g. a form submit) and confirm the outgoing request URL has
   the full `/inf513/grupo11sc/proyecto2/...` prefix — if missing, `.env` wasn't correct when
   `npm run build` ran; fix and rebuild.
5. `php artisan migrate:status` — all migrations should show `Ran`.
6. Trigger a 500 deliberately and confirm you see Laravel's generic production error page, not a
   stack trace — otherwise `APP_DEBUG` is still `true`.
7. DevTools → Application → Cookies: cookie name should reflect `APP_NAME` (not `laravel_session`),
   with `Path` = `/inf513/grupo11sc/proyecto2`, not `/`.
8. `tail -f storage/logs/laravel.log` while clicking around to catch silent errors.
