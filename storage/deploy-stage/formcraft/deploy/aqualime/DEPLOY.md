# FormCraft — aqualime.uz/forms

Asosiy sayt `https://aqualime.uz/login/` da ishlaydi. FormCraft alohida papkada joylashadi.

## URL manzillar

| Sahifa | Manzil |
|--------|--------|
| Admin kirish | https://aqualime.uz/forms/login |
| Dashboard | https://aqualime.uz/forms/dashboard |
| Formalar ro'yxati | https://aqualime.uz/forms/manage |
| Ochiq forma | https://aqualime.uz/forms/f/{slug} |

## Server strukturasi (Ahost / cPanel)

```
public_html/
  login/              ← mavjud Aqualime Admin (o'zgartirmang)
  ...                 ← asosiy sayt fayllari
  formcraft/          ← Laravel loyiha (app, bootstrap, vendor, ...)
    .htaccess         ← Deny from all (ixtiyoriy, xavfsizlik uchun)
  forms/              ← faqat public/ papka tarkibi
    index.php         ← deploy/aqualime/index.php (yo'llarni tekshiring)
    .htaccess         ← deploy/aqualime/.htaccess
    build/            ← npm run build natijasi
    storage -> ../formcraft/storage/app/public (symlink)
```

Agar `formcraft` papkasi `public_html` dan tashqarida bo'lsa, `deploy/aqualime/index.php` ichidagi `$laravelRoot` yo'lini moslang.

## O'rnatish qadamlari

1. Loyihani serverga yuklang (`formcraft/` papkasiga).
2. `public/` ichidagi barcha fayllarni `public_html/forms/` ga nusxalang.
3. `deploy/aqualime/.htaccess` va `deploy/aqualime/index.php` fayllarini `public_html/forms/` ga qo'ying (`index.php` ustiga yoziladi).
4. `.env` faylini yarating:

```env
APP_NAME=FormCraft
APP_ENV=production
APP_KEY=base64:...   # php artisan key:generate
APP_DEBUG=false
APP_URL=https://aqualime.uz/forms

APP_LOCALE=uz

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

SESSION_DRIVER=database
SESSION_PATH=/forms
SESSION_DOMAIN=aqualime.uz

CACHE_STORE=database
QUEUE_CONNECTION=database
```

5. Terminal (SSH yoki cPanel Terminal):

```bash
cd ~/public_html/formcraft
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

6. `forms/storage` uchun symlink: `public_html/forms/storage` → `formcraft/storage/app/public`

7. Papka huquqlari: `storage/` va `bootstrap/cache/` — yozish uchun ochiq (775).

## Build (CSS/JS)

Lokal kompyuterdan:

```bash
npm ci
npm run build
```

`public/build/` papkasini `public_html/forms/build/` ga yuklang.

## Tekshirish

- https://aqualime.uz/forms/login — FormCraft kirish sahifasi
- https://aqualime.uz/login/ — mavjud Aqualime Admin (o'zgarishsiz)
