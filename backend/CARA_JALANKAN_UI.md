# 🚀 Cara Jalankan UI Laravel Blade

## ✅ Yang Sudah Dibuat

### 📄 Halaman
- ✅ **Login** (`/`) - Halaman login dengan demo accounts
- ✅ **Register** (`/register`) - Halaman registrasi user baru
- ✅ **Dashboard** (`/dashboard`) - Dashboard dengan stats & recent activities
- ✅ **Data Alat** (`/alat`) - CRUD Alat lengkap dengan modal
- ✅ **Kategori** (`/kategori`) - Placeholder (tinggal copy pattern dari Alat)
- ✅ **Peminjaman** (`/peminjaman`) - Placeholder
- ✅ **Pengembalian** (`/pengembalian`) - Placeholder
- ✅ **Users** (`/users`) - Placeholder (Admin only)
- ✅ **Laporan** (`/laporan`) - Placeholder (Admin/Petugas)

### 🎨 Design Features
- ✅ Modern gradient UI dengan glassmorphism
- ✅ Responsive layout (sidebar collapsible)
- ✅ Role-based menu hiding
- ✅ Font Awesome icons
- ✅ Animated cards & buttons
- ✅ Modal untuk form CRUD
- ✅ Loading states
- ✅ Alert messages
- ✅ Demo account buttons (quick login)

---

## 📝 Langkah-Langkah

### 1. Pastikan Backend Sudah Running

```bash
cd /home/lenovo/API-UJIKOM/backend

# Fix permissions (kalau belum)
chmod -R 775 storage bootstrap/cache

# Run migration & seeder (kalau belum)
php artisan migrate:fresh --seed

# Start Laravel server
php artisan serve
```

Server akan jalan di: **http://localhost:8000**

---

### 2. Buka Browser

Langsung akses: **http://localhost:8000**

Anda akan melihat halaman **Login** yang modern! 🎉

---

### 3. Login dengan Akun Demo

Klik salah satu tombol demo account:

- **Admin**: admin@example.com / password
- **Petugas**: petugas@example.com / password  
- **Peminjam**: peminjam@example.com / password

Atau ketik manual, lalu klik **Login**.

---

### 4. Jelajahi Dashboard

Setelah login, Anda akan masuk ke **Dashboard** dengan:
- 4 kartu statistik (Total Alat, Peminjaman, dll)
- Tabel peminjaman terbaru
- Sidebar menu (berbeda tergantung role)

---

### 5. Test CRUD Alat

Klik menu **Data Alat** di sidebar:

1. **Lihat Data**: Semua alat akan muncul di tabel
2. **Tambah Alat**: Klik tombol "Tambah Alat" (Admin/Petugas)
3. **Edit Alat**: Klik icon pensil di kolom Aksi
4. **Hapus Alat**: Klik icon sampah di kolom Aksi

**Note**: Peminjam tidak bisa tambah/edit/hapus, hanya bisa lihat!

---

## 🎯 Cara Menambah Halaman Lainnya

Halaman **Kategori, Peminjaman, Pengembalian, Users, Laporan** masih placeholder.

### Contoh: Lengkapi Halaman Kategori

1. **Buka file**: `backend/resources/views/kategori/index.blade.php`
2. **Copy-paste** dari `backend/resources/views/alat/index.blade.php`
3. **Ganti**:
   - Semua kata "alat" → "kategori-alat"
   - Semua kata "Alat" → "Kategori"
   - Field form sesuai model Kategori (nama_kategori, deskripsi)
   - Hapus field yang tidak diperlukan (merk, jumlah, kondisi)

**Endpoint API yang dipakai**:
- GET `/api/kategori-alat` - List
- POST `/api/kategori-alat` - Create
- GET `/api/kategori-alat/{id}` - Detail
- PUT `/api/kategori-alat/{id}` - Update
- DELETE `/api/kategori-alat/{id}` - Delete

Semua endpoint sudah jalan di backend!

---

## 🔒 Role-Based Access Control

### Peminjam
- **Bisa akses**: Dashboard, Data Alat (view only), Peminjaman
- **Tidak bisa**: Kategori, Pengembalian, Users, Laporan

### Petugas
- **Bisa akses**: Dashboard, Kategori, Alat (CRUD), Peminjaman, Pengembalian, Laporan
- **Tidak bisa**: Users

### Admin
- **Bisa akses**: Semua menu & CRUD

Menu otomatis **hidden** di sidebar berdasarkan role!

---

## 🎨 Cara Custom Warna/Style

Semua CSS ada di `backend/resources/views/layouts/app.blade.php` di bagian `<style>`.

**Variabel warna** (line 21-29):
```css
:root {
    --primary: #6366f1;        /* Warna utama */
    --secondary: #8b5cf6;      /* Warna sekunder */
    --success: #10b981;        /* Hijau */
    --danger: #ef4444;         /* Merah */
    --warning: #f59e0b;        /* Kuning */
    --info: #3b82f6;           /* Biru */
}
```

Tinggal ganti hex code sesuai selera!

---

## 📱 Responsive Design

UI sudah responsive! Test di:
- Desktop (optimal)
- Tablet (sidebar collapsible)
- Mobile (sidebar jadi drawer)

Klik icon hamburger untuk toggle sidebar di mobile.

---

## 🐛 Troubleshooting

### Error: "Failed to fetch"
**Penyebab**: Laravel server belum jalan  
**Solusi**: Jalankan `php artisan serve`

### Error: "Unauthenticated"
**Penyebab**: Token expired atau tidak valid  
**Solusi**: Logout → Login ulang

### Halaman blank setelah login
**Penyebab**: JavaScript error (cek console)  
**Solusi**: 
1. Buka DevTools (F12)
2. Lihat tab Console
3. Pastikan `API_URL` benar (http://localhost:8000/api)

### Menu tidak muncul sesuai role
**Penyebab**: Data user di localStorage salah  
**Solusi**: 
1. Buka DevTools → Application → Local Storage
2. Hapus semua data
3. Login ulang

---

## ✨ Next Steps

1. ✅ Lengkapi halaman Kategori (copy pattern dari Alat)
2. ✅ Lengkapi halaman Peminjaman (form multiple detail)
3. ✅ Lengkapi halaman Pengembalian
4. ✅ Lengkapi halaman Users (CRUD user)
5. ✅ Lengkapi halaman Laporan (filter & charts)

**Pattern sudah ada di halaman Alat, tinggal customize!**

---

## 🚀 Deploy Production

Kalau mau deploy:

1. **Update API_URL** di `layouts/app.blade.php`:
```javascript
const API_URL = 'https://yourdomain.com/api';
```

2. **Build assets** (kalau pakai Vite):
```bash
npm run build
```

3. **Optimize Laravel**:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

**Happy Coding! 🎉**

Kalau ada yang error atau bingung, tinggal tanya!
