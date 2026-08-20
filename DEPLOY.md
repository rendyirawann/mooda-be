# Deploy mooda-be → `api.mooda.id`

Pola sama seperti stakko-pos: **nginx → Octane (RoadRunner)**, Redis, PostgreSQL
`stakko_pos` yang **dibagi** dengan web. mooda-be = aplikasi terpisah, DB sama.

Asumsi: Ubuntu + nginx + PHP 8.3 + Composer + Redis + PostgreSQL sudah ada (dipakai stakko-pos).

---

## 1. DNS
Arahkan **`api.mooda.id`** (A record) ke IP server yang sama dengan `mooda.id`.

## 2. Clone & dependency
```bash
cd /var/www
git clone https://github.com/rendyirawann/mooda-be.git
cd mooda-be
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate         # APP_KEY sendiri; token Sanctum di-hash SHA-256, aman beda key
```

## 3. `.env` produksi (pakai DB & Redis bersama)
```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.mooda.id

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=stakko_pos          # SAMA dengan stakko-pos
DB_USERNAME=stakko
DB_PASSWORD=****

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
# Beri prefix beda agar cache tak bentrok dgn stakko-pos:
REDIS_PREFIX=moodabe_

OCTANE_SERVER=roadrunner
L5_SWAGGER_GENERATE_ALWAYS=false
CORS_ALLOWED_ORIGINS=            # kosong = izinkan semua (mobile pakai Bearer, bukan cookie)
```

## 4. Tabel Sanctum di DB bersama (PENTING)
Jangan `migrate` penuh — tabel `users/cache/jobs/sessions` sudah ada di `stakko_pos`.
Hanya butuh `personal_access_tokens`. **Cek dulu**:
```bash
php artisan tinker --execute="echo Schema::hasTable('personal_access_tokens') ? 'ADA' : 'BELUM';"
```
- Kalau **BELUM**, jalankan hanya migrasi itu:
  ```bash
  php artisan migrate --path=database/migrations/*_create_personal_access_tokens_table.php --force
  ```
- Hapus/ jangan pakai migrasi bawaan lain milik mooda-be (users, cache, jobs, sessions)
  agar tak bentrok. (Aman ditinggal selama kamu tak menjalankan `migrate` tanpa `--path`.)

## 5. Cache & Swagger
```bash
php artisan config:cache
php artisan route:cache
php artisan l5-swagger:generate
```

## 6. Octane RoadRunner
```bash
php artisan octane:install --server=roadrunner   # unduh binari rr (butuh internet)
```
Jalankan sebagai service (supervisor) — **pilih port bebas**, mis. 8001
(port stakko-pos jangan dipakai ulang):

`/etc/supervisor/conf.d/mooda-be.conf`
```ini
[program:mooda-be-octane]
command=php /var/www/mooda-be/artisan octane:start --server=roadrunner --host=127.0.0.1 --port=8001
directory=/var/www/mooda-be
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/mooda-be/storage/logs/octane.log
stopwaitsecs=15
```
```bash
sudo supervisorctl reread && sudo supervisorctl update && sudo supervisorctl start mooda-be-octane
```

## 7. nginx `api.mooda.id`
`/etc/nginx/sites-available/api.mooda.id`
```nginx
server {
    listen 80;
    server_name api.mooda.id;

    client_max_body_size 20M;

    location / {
        proxy_pass http://127.0.0.1:8001;   # port Octane mooda-be
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```
```bash
sudo ln -s /etc/nginx/sites-available/api.mooda.id /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
sudo certbot --nginx -d api.mooda.id           # SSL
```
> mooda-be sudah `trustProxies`-friendly? Laravel 13 default: tambah `->trustProxies(at:'*')`
> di `bootstrap/app.php` bila skema HTTPS tak terdeteksi di belakang nginx (sama seperti stakko-pos).

## 8. Verifikasi
```bash
curl https://api.mooda.id/api/v1/health
# buka: https://api.mooda.id/api/documentation
```

---

## Deploy ulang (tiap update)
```bash
cd /var/www/mooda-be
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan config:cache && php artisan route:cache
php artisan l5-swagger:generate
php artisan octane:reload        # WAJIB — RoadRunner tidak baca kode baru tanpa ini
```
