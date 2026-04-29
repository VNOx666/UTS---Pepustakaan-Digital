# Panduan Menjalankan Project Perpustakaan Microservices

## 1. Setup Environment
Pastikan sudah menginstall PHP, Composer, dan MySQL Server.

## 2. Struktur Folder
Struktur yang telah dibuat:
- `perpustakaan-microservices/`
  - `member-service/` (Port 8001)
  - `buku-service/` (Port 8002)
  - `pinjam-service/` (Port 8003)
  - `denda-service/` (Port 8004)

## 3. Database
4 database MySQL terpisah telah dibuat:
- `db_member_service`
- `db_buku_service`
- `db_pinjam_service`
- `db_denda_service`

Migrasi juga telah dijalankan untuk setiap service.

## 4. Cara Menjalankan Masing-masing Service
Buka 4 terminal atau tab terminal yang berbeda, masuk ke folder masing-masing, dan jalankan perintah berikut:

**Terminal 1 (Member Service)**
```bash
cd c:\xampp\htdocs\perpustakaan-microservices\member-service
php artisan serve --port=8001
```

**Terminal 2 (Buku Service)**
```bash
cd c:\xampp\htdocs\perpustakaan-microservices\buku-service
php artisan serve --port=8002
```

**Terminal 3 (Pinjam Service)**
```bash
cd c:\xampp\htdocs\perpustakaan-microservices\pinjam-service
php artisan serve --port=8003
```

**Terminal 4 (Denda Service)**
```bash
cd c:\xampp\htdocs\perpustakaan-microservices\denda-service
php artisan serve --port=8004
```

## 5. Pengujian dengan Postman
Import file `Perpustakaan_Microservices.postman_collection.json` ke dalam Postman Anda.

Ikuti alur pengujian berikut:
1. **Tambah Member**: Di MemberService, hit `POST /api/members`
2. **Tambah Buku**: Di BukuService, hit `POST /api/bukus`
3. **Peminjaman**: Di PinjamService, hit `POST /api/pinjams`
   - Pastikan ID Member dan Buku sesuai dengan yang baru saja dibuat.
   - Perhatikan bahwa respons berhasil berarti PinjamService telah memvalidasi ke MemberService dan BukuService, dan stok buku di BukuService akan berkurang.
4. **Hitung Denda**: Di DendaService, hit `POST /api/dendas/hitung`
   - Masukkan ID Pinjam dari langkah sebelumnya.
   - Karena mungkin belum terlambat, denda kemungkinan Rp0.
5. **Cek Profil Member**: Di MemberService, hit `GET /api/members/{id}/profile`
   - Anda akan melihat data diri member beserta riwayat peminjaman dan tanggungan denda yang ditarik dari service lain.
6. **Cek Histori Peminjam Buku**: Di BukuService, hit `GET /api/bukus/{id}/histori-peminjam`
   - Anda akan melihat daftar siapa saja yang meminjam buku tersebut.

**Catatan Tambahan**:
- Anda dapat menguji denda dengan mengedit manual tanggal peminjaman di database `db_pinjam_service` tabel `pinjams` menjadi misal bulan lalu, lalu jalankan hitung denda lagi untuk id pinjam tersebut.

Selamat mencoba!
