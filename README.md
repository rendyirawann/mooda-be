# Mooda BE — API (Laravel + Octane + Redis)

Backend **API-only** untuk aplikasi mobile Mooda (`mooda-fe`). Menyediakan REST API
ber-**Swagger** yang dikonsumsi Flutter. Repo **terpisah** dari `stakko-pos`; di server
diarahkan ke subdomain **`api.mooda.id`**.

- **Auth**: Sanctum (Bearer token) — cocok untuk mobile.
- **Dokumentasi**: Swagger UI di **`/api/documentation`** (OpenAPI di `storage/api-docs/api-docs.json`).
- **Prefix**: semua endpoint di bawah **`/api/v1`**.
- **Tag** dipisah per modul/vertical (F&B, Laundry, Event, …) supaya rapi walau API banyak.

## Arsitektur data
**Berbagi database dengan `stakko-pos`** (satu PostgreSQL `stakko_pos` dipakai web + API).
Dev lokal `.env` menunjuk `127.0.0.1:5433/stakko_pos`; produksi ke DB server yang sama.

- **PK `users` = UUID**, `tenant_id`/`menus.id`/`tenants.id` = bigint.
- Auth mobile pakai Sanctum → tabel `personal_access_tokens` dibuat dengan `tokenable_id`
  bertipe **uuid** (menyesuaikan `users.id`).
- **Tenant-scoping WAJIB**: setiap query difilter `->where('tenant_id', $user->tenant_id)`
  secara eksplisit (mooda-be tidak memakai global-scope tenancy stakko).

### Status wiring
- ✅ **Nyata (baca DB bersama)**: `Auth` (login/me/logout), `Akun` (tenant/plan), `F&B - Menu`.
- 🚧 **Masih stub** (`meta.stub=true`, ada `// TODO`): Kasir/Order, Dapur, Meja, Inventory,
  Resep&HPP, Laporan, Shift, Laundry, Event — tinggal ikuti pola MenuController
  (filter `tenant_id` + map model ke tabel `stakko_pos`).

## Setup
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed      # dev (sqlite): buat tabel + akun demo
php artisan l5-swagger:generate # build dokumentasi OpenAPI
php artisan serve --host=127.0.0.1 --port=8080
```
Buka: http://127.0.0.1:8080/api/documentation

## Produksi (ringkas)
1. `.env` → PostgreSQL `stakko_pos` yang sama + Redis (session/cache/queue).
2. **Jangan** jalankan migrate bawaan (tabel sudah ada di DB bersama); cukup tambahkan
   tabel `personal_access_tokens` (Sanctum) bila belum ada.
3. Octane RoadRunner:
   ```bash
   php artisan octane:install --server=roadrunner
   php artisan octane:start --host=127.0.0.1 --port=8080
   ```
   **WAJIB** `php artisan octane:reload` setiap deploy.
4. `L5_SWAGGER_GENERATE_ALWAYS=false` + `php artisan l5-swagger:generate` saat deploy.
5. Nginx: `api.mooda.id` → Octane.

## Menambah endpoint
1. Buat controller di `app/Http/Controllers/Api/<Modul>/`.
2. Beri atribut `#[OA\Get/Post(...)]` dengan `tags: ['<Tag>']` (tambah `#[OA\Tag]`
   baru di `Controller.php` bila modul baru).
3. Daftarkan route di `routes/api.php` (grup `auth:sanctum`).
4. `php artisan l5-swagger:generate`.

## Peta endpoint (v1)
| Tag | Contoh |
|-----|--------|
| Auth | `POST /auth/login`, `GET /auth/me`, `POST /auth/logout` |
| Umum | `GET /health`, `GET /config` |
| Akun | `GET /account/tenant`, `GET /account/plan` |
| F&B - Kasir | `GET/POST /fnb/orders`, `POST /fnb/orders/{id}/pay` |
| F&B - Dapur | `GET /fnb/kitchen/orders`, `POST /fnb/kitchen/items/{id}/bump` |
| F&B - Menu | `GET /fnb/menus`, `GET /fnb/menus/{id}` |
| F&B - Meja | `GET /fnb/tables` |
| F&B - Inventory | `GET /fnb/inventory/ingredients`, `POST /fnb/inventory/movements`, `POST /fnb/inventory/opname` |
| F&B - Resep & HPP | `GET /fnb/recipes/{menuId}` |
| F&B - Laporan | `GET /fnb/reports/sales`, `GET /fnb/reports/hpp` |
| Shift | `GET /shifts/current`, `POST /shifts/open`, `POST /shifts/close` |
| Laundry | `GET/POST /laundry/orders`, `POST /laundry/orders/{id}/advance`, `GET /laundry/services` |
| Event | `GET /event/events` (roadmap) |
