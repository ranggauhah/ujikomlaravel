# 🚀 PANDUAN TESTING API - Sistem Peminjaman Alat

## 📌 Langkah Setup

### 1. Fix Permissions (Jalankan di WSL/Ubuntu Terminal)
```bash
cd /home/lenovo/API-UJIKOM/backend
chmod -R 775 storage bootstrap/cache
```

### 2. Jalankan Migration & Seeder
```bash
php artisan migrate:fresh --seed
```

Output yang diharapkan:
- Tabel: users, kategori_alat, alat, peminjaman, detail_pinjam, pengembalian, log_aktivitas
- Data awal: 3 users (admin, petugas, peminjam), 4 kategori, 4 alat

### 3. Start Laravel Server
```bash
php artisan serve
```

Server akan jalan di: `http://localhost:8000`

---

## 🔐 Testing Authentication

### 1. Register User Baru
**POST** `http://localhost:8000/api/auth/register`

**Body (JSON):**
```json
{
  "name": "Test Peminjam",
  "email": "test@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "peminjam"
}
```

**Response:**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": { ... },
    "token": "1|xxxxxxxxxxxx"
  }
}
```

**❗ SIMPAN TOKEN INI untuk request selanjutnya!**

---

### 2. Login
**POST** `http://localhost:8000/api/auth/login`

**Body (JSON):**
```json
{
  "email": "admin@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Administrator",
      "email": "admin@example.com",
      "role": "admin"
    },
    "token": "2|xxxxxxxxxxxx"
  }
}
```

**Kredensial Default Seeder:**
- **Admin:** admin@example.com / password
- **Petugas:** petugas@example.com / password
- **Peminjam:** peminjam@example.com / password

---

### 3. Get Current User
**GET** `http://localhost:8000/api/auth/me`

**Headers:**
```
Authorization: Bearer {your_token}
```

---

### 4. Logout
**POST** `http://localhost:8000/api/auth/logout`

**Headers:**
```
Authorization: Bearer {your_token}
```

---

## 📦 Testing CRUD Kategori Alat (Admin/Petugas Only)

### 1. Get All Kategori
**GET** `http://localhost:8000/api/kategori-alat`

**Headers:**
```
Authorization: Bearer {admin_or_petugas_token}
```

---

### 2. Create Kategori
**POST** `http://localhost:8000/api/kategori-alat`

**Headers:**
```
Authorization: Bearer {admin_or_petugas_token}
Content-Type: application/json
```

**Body:**
```json
{
  "nama_kategori": "Multimedia",
  "deskripsi": "Peralatan multimedia seperti kamera, speaker, dll"
}
```

---

### 3. Get Single Kategori
**GET** `http://localhost:8000/api/kategori-alat/{id}`

---

### 4. Update Kategori
**PUT** `http://localhost:8000/api/kategori-alat/{id}`

**Body:**
```json
{
  "nama_kategori": "Multimedia Updated",
  "deskripsi": "Deskripsi baru"
}
```

---

### 5. Delete Kategori
**DELETE** `http://localhost:8000/api/kategori-alat/{id}`

---

## 🔧 Testing CRUD Alat

### 1. Get All Alat (Semua Role)
**GET** `http://localhost:8000/api/alat`

**Headers:**
```
Authorization: Bearer {any_token}
```

---

### 2. Create Alat (Admin/Petugas Only)
**POST** `http://localhost:8000/api/alat`

**Headers:**
```
Authorization: Bearer {admin_or_petugas_token}
Content-Type: application/json
```

**Body:**
```json
{
  "kategori_id": 1,
  "nama_alat": "Laptop HP",
  "merk": "HP",
  "jumlah": 15,
  "deskripsi": "Laptop untuk coding",
  "kondisi": "baik"
}
```

---

### 3. Update Alat
**PUT** `http://localhost:8000/api/alat/{id}`

**Body:**
```json
{
  "jumlah": 20,
  "kondisi": "baik"
}
```

---

### 4. Delete Alat
**DELETE** `http://localhost:8000/api/alat/{id}`

---

## 📋 Testing Peminjaman

### 1. Get All Peminjaman
**GET** `http://localhost:8000/api/peminjaman`

**Headers:**
```
Authorization: Bearer {token}
```

**Catatan:**
- Peminjam: hanya melihat peminjamannya sendiri
- Admin/Petugas: melihat semua peminjaman

---

### 2. Create Peminjaman (Semua Role)
**POST** `http://localhost:8000/api/peminjaman`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
  "tanggal_pinjam": "2026-07-29",
  "keperluan": "Untuk praktikum programming",
  "detail": [
    {
      "alat_id": 1,
      "jumlah_pinjam": 2
    },
    {
      "alat_id": 2,
      "jumlah_pinjam": 1
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Peminjaman berhasil dibuat",
  "data": {
    "id": 1,
    "user_id": 3,
    "tanggal_pinjam": "2026-07-29",
    "status": "dipinjam",
    "detail_pinjam": [...]
  }
}
```

**⚠️ Fitur Auto:**
- Stok alat otomatis berkurang
- Status "dipinjam" otomatis
- Log aktivitas tercatat

---

### 3. Get Single Peminjaman
**GET** `http://localhost:8000/api/peminjaman/{id}`

---

### 4. Update Status Peminjaman
**PUT** `http://localhost:8000/api/peminjaman/{id}`

**Body:**
```json
{
  "status": "terlambat"
}
```

---

### 5. Delete Peminjaman
**DELETE** `http://localhost:8000/api/peminjaman/{id}`

**⚠️ Fitur Auto:**
- Stok alat otomatis dikembalikan

---

## ↩️ Testing Pengembalian

### 1. Get All Pengembalian
**GET** `http://localhost:8000/api/pengembalian`

---

### 2. Create Pengembalian (Admin/Petugas Only)
**POST** `http://localhost:8000/api/pengembalian`

**Headers:**
```
Authorization: Bearer {admin_or_petugas_token}
Content-Type: application/json
```

**Body:**
```json
{
  "peminjaman_id": 1,
  "tanggal_kembali": "2026-07-31",
  "kondisi_alat": "baik",
  "keterangan": "Semua alat dalam kondisi baik",
  "denda": 0
}
```

**⚠️ Fitur Auto:**
- Status peminjaman berubah jadi "dikembalikan"
- Stok alat otomatis bertambah
- Jika kondisi "rusak", kondisi alat diupdate

---

### 3. Update Pengembalian
**PUT** `http://localhost:8000/api/pengembalian/{id}`

**Body:**
```json
{
  "denda": 50000,
  "keterangan": "Terlambat 5 hari"
}
```

---

## 👥 Testing User Management (Admin Only)

### 1. Get All Users
**GET** `http://localhost:8000/api/users`

**Headers:**
```
Authorization: Bearer {admin_token}
```

---

### 2. Create User
**POST** `http://localhost:8000/api/users`

**Body:**
```json
{
  "name": "Petugas Baru",
  "email": "petugas2@example.com",
  "password": "password123",
  "role": "petugas"
}
```

---

### 3. Update User
**PUT** `http://localhost:8000/api/users/{id}`

**Body:**
```json
{
  "name": "Petugas Updated",
  "role": "admin"
}
```

---

### 4. Delete User
**DELETE** `http://localhost:8000/api/users/{id}`

---

## 📊 Testing Laporan (Admin/Petugas Only)

### 1. Laporan Peminjaman
**GET** `http://localhost:8000/api/laporan/peminjaman?tanggal_mulai=2026-07-01&tanggal_selesai=2026-07-31`

**Response:**
```json
{
  "success": true,
  "data": {
    "periode": { ... },
    "summary": {
      "total_peminjaman": 10,
      "status_dipinjam": 3,
      "status_dikembalikan": 5,
      "status_terlambat": 2
    },
    "peminjaman": [...]
  }
}
```

---

### 2. Laporan Pengembalian
**GET** `http://localhost:8000/api/laporan/pengembalian?tanggal_mulai=2026-07-01&tanggal_selesai=2026-07-31`

---

### 3. Laporan Stok Alat
**GET** `http://localhost:8000/api/laporan/stok-alat`

---

### 4. Laporan User
**GET** `http://localhost:8000/api/laporan/user`

---

### 5. Laporan Log Aktivitas
**GET** `http://localhost:8000/api/laporan/log-aktivitas?tanggal_mulai=2026-07-01&tanggal_selesai=2026-07-31&aksi=create`

**Query Parameters (Optional):**
- `tanggal_mulai`: Filter tanggal mulai
- `tanggal_selesai`: Filter tanggal selesai
- `user_id`: Filter berdasarkan user
- `aksi`: Filter berdasarkan aksi (create/update/delete)

---

## 🔒 Testing Role-Based Access

### Test 1: Peminjam Coba Akses Admin Route
**GET** `http://localhost:8000/api/users`

**Headers:**
```
Authorization: Bearer {peminjam_token}
```

**Expected Response:**
```json
{
  "success": false,
  "message": "Forbidden. Role tidak memiliki akses."
}
```

---

### Test 2: Peminjam Coba Create Kategori
**POST** `http://localhost:8000/api/kategori-alat`

**Expected:** 403 Forbidden

---

### Test 3: Peminjam Akses Peminjaman Sendiri
**GET** `http://localhost:8000/api/peminjaman`

**Expected:** Hanya melihat data peminjamannya sendiri

---

## 📝 Testing Observer (Log Aktivitas)

Setiap operasi CRUD akan **otomatis tercatat** di `log_aktivitas`.

**Contoh: Setelah Create Alat**

**GET** `http://localhost:8000/api/laporan/log-aktivitas`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "aksi": "create",
      "tabel": "alat",
      "data_lama": null,
      "data_baru": "{\"nama_alat\":\"Laptop HP\", ...}",
      "created_at": "2026-07-29 10:30:00"
    }
  ]
}
```

---

## ✅ Checklist Testing

- [ ] Register & Login berhasil
- [ ] Token authentication berfungsi
- [ ] CRUD Kategori Alat (Admin/Petugas)
- [ ] CRUD Alat dengan eager loading kategori
- [ ] Create Peminjaman dengan multiple detail
- [ ] Stok alat berkurang saat peminjaman
- [ ] Create Pengembalian
- [ ] Stok alat bertambah saat pengembalian
- [ ] Status peminjaman berubah saat pengembalian
- [ ] CRUD User (Admin only)
- [ ] Laporan Peminjaman dengan filter periode
- [ ] Laporan Pengembalian dengan summary denda
- [ ] Laporan Stok Alat
- [ ] Laporan User
- [ ] Laporan Log Aktivitas dengan filter
- [ ] Role middleware berfungsi (403 untuk unauthorized role)
- [ ] Observer mencatat semua aktivitas CRUD
- [ ] Peminjam hanya bisa lihat data sendiri
- [ ] Validation errors return 422 dengan message Indonesia

---

## 🐛 Common Errors & Solutions

### Error: "Unauthenticated"
**Solution:** Pastikan header `Authorization: Bearer {token}` sudah benar

### Error: "Forbidden. Role tidak memiliki akses"
**Solution:** Login dengan user role yang sesuai (cek akses di routes/api.php)

### Error: "Stok tidak mencukupi"
**Solution:** Pastikan jumlah_pinjam <= jumlah stok alat yang tersedia

### Error: "Peminjaman sudah dikembalikan"
**Solution:** Satu peminjaman hanya bisa dikembalikan sekali

### Error: "Validation errors"
**Solution:** Cek field yang required dan format data (lihat response errors)

---

## 📚 Struktur Database

```
users (id, name, email, password, role, timestamps)
kategori_alat (id, nama_kategori, deskripsi, timestamps)
alat (id, kategori_id, nama_alat, merk, jumlah, deskripsi, kondisi, timestamps)
peminjaman (id, user_id, tanggal_pinjam, tanggal_kembali, status, keperluan, timestamps)
detail_pinjam (id, peminjaman_id, alat_id, jumlah_pinjam, timestamps)
pengembalian (id, peminjaman_id, tanggal_kembali, kondisi_alat, keterangan, denda, timestamps)
log_aktivitas (id, user_id, aksi, tabel, data_lama, data_baru, timestamps)
```

---

## 🎯 Next Steps

1. Test semua endpoint dengan Postman
2. Buat collection Postman untuk dokumentasi
3. Test edge cases (stok habis, peminjaman terlambat, dll)
4. Deploy ke production server
5. Setup CORS jika diakses dari frontend

---

**Happy Testing! 🚀**
