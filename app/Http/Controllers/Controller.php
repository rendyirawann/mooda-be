<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

/**
 * Spesifikasi OpenAPI global Mooda BE.
 *
 * Tag dipisah per-vertical/modul agar Swagger rapi walau API banyak
 * (bukan cuma POS F&B — ada Laundry, Event, dst).
 */
#[OA\Info(
    version: '1.0.0',
    title: 'Mooda API',
    description: 'REST API Mooda untuk aplikasi mobile (Flutter). Semua endpoint terproteksi memakai Bearer token (Sanctum).',
    contact: new OA\Contact(name: 'Mooda Teknologi Indonesia', email: 'dev@mooda.id')
)]
#[OA\Server(url: 'https://api.mooda.id/api/v1', description: 'Produksi')]
#[OA\Server(url: 'http://127.0.0.1:8080/api/v1', description: 'Lokal (dev)')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum'
)]
// ---- Umum / Auth ----
#[OA\Tag(name: 'Auth', description: 'Autentikasi & sesi (login/logout/me)')]
#[OA\Tag(name: 'Umum', description: 'Health check & konfigurasi aplikasi')]
#[OA\Tag(name: 'Akun', description: 'Profil, tenant, langganan/plan')]
// ---- F&B (Dine) ----
#[OA\Tag(name: 'F&B - Kasir', description: 'Pesanan, pembayaran, struk')]
#[OA\Tag(name: 'F&B - Dapur', description: 'Kitchen Display System (antrean & bump)')]
#[OA\Tag(name: 'F&B - Menu', description: 'Menu, kategori, add-on')]
#[OA\Tag(name: 'F&B - Meja', description: 'Meja & sesi meja')]
#[OA\Tag(name: 'F&B - Inventory', description: 'Bahan, batch, stok masuk/keluar, opname')]
#[OA\Tag(name: 'F&B - Resep & HPP', description: 'Resep menu & harga pokok penjualan')]
#[OA\Tag(name: 'F&B - Laporan', description: 'Laporan penjualan, HPP, shift')]
#[OA\Tag(name: 'Shift', description: 'Buka/tutup shift & kas')]
// ---- Laundry ----
#[OA\Tag(name: 'Laundry - Kasir', description: 'Nota laundry & pembayaran')]
#[OA\Tag(name: 'Laundry - Produksi', description: 'Pipeline status (Diterima..Diambil)')]
#[OA\Tag(name: 'Laundry - Layanan', description: 'Layanan & pelanggan laundry')]
// ---- Event (vertical mendatang) ----
#[OA\Tag(name: 'Event', description: 'Manajemen event & tiket (roadmap)')]
abstract class Controller
{
    //
}
