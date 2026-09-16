# ✅ Testing Checklist - Sistem Peminjaman Alat

## 🔧 Pre-Testing Setup

### 1. Pastikan API Running
```bash
cd api
npm start
# API should run on http://localhost:8000
```

### 2. Pastikan Laravel Running
```bash
cd backend
php artisan serve
# Frontend should run on http://localhost:8000 (or different port)
```

### 3. Clear Cache & Browser Data
- Clear browser cache (Ctrl+Shift+Del)
- Clear localStorage
- Open DevTools Console (F12)

---

## 🧪 Test Cases

### A. Authentication Tests

#### ✅ Test 1: Login
- [ ] Buka halaman `/login`
- [ ] Coba login dengan akun demo: `admin@example.com` / `password`
- [ ] Periksa redirect ke `/dashboard`
- [ ] Periksa localStorage (`token` dan `user` ada)
- [ ] Periksa nama user muncul di header

**Expected Result:**
- ✅ Login berhasil
- ✅ Redirect ke dashboard
- ✅ Token tersimpan
- ✅ Nama user ditampilkan

#### ✅ Test 2: Register
- [ ] Buka halaman `/register`
- [ ] Isi form registrasi dengan data baru
- [ ] Submit form
- [ ] Periksa auto-login setelah register

**Expected Result:**
- ✅ Registrasi berhasil
- ✅ Auto-login
- ✅ Redirect ke dashboard

#### ✅ Test 3: Logout
- [ ] Klik tombol "Logout" di sidebar
- [ ] Konfirmasi logout
- [ ] Periksa redirect ke halaman login
- [ ] Periksa localStorage (kosong)

**Expected Result:**
- ✅ Logout berhasil
- ✅ Token dihapus
- ✅ Redirect ke login

---

### B. Dashboard Tests

#### ✅ Test 4: Dashboard Loading
- [ ] Login sebagai admin
- [ ] Periksa stats cards muncul dengan angka
- [ ] Periksa tabel "Peminjaman Terbaru" tampil
- [ ] Periksa tidak ada error di console

**Expected Result:**
- ✅ Stats cards terisi
- ✅ Tabel tampil dengan data
- ✅ No console errors

---

### C. CRUD Tests

#### ✅ Test 5: CRUD Kategori (Admin/Petugas)
**Create:**
- [ ] Buka halaman `/kategori`
- [ ] Klik "Tambah Kategori"
- [ ] Isi nama: "Test Kategori"
- [ ] Isi deskripsi: "Test Description"
- [ ] Submit
- [ ] Periksa kategori baru muncul di list

**Read:**
- [ ] Periksa kategori ditampilkan dalam card grid
- [ ] Periksa search berfungsi

**Update:**
- [ ] Klik tombol edit pada kategori
- [ ] Ubah nama menjadi "Test Kategori Updated"
- [ ] Submit
- [ ] Periksa perubahan tersimpan

**Delete:**
- [ ] Klik tombol delete
- [ ] Konfirmasi delete
- [ ] Periksa kategori terhapus dari list

**Expected Result:**
- ✅ Semua operasi CRUD berhasil
- ✅ UI update real-time

#### ✅ Test 6: CRUD Alat
**Create:**
- [ ] Buka halaman `/alat`
- [ ] Klik "Tambah Alat"
- [ ] Pilih kategori
- [ ] Isi nama alat: "Laptop Test"
- [ ] Isi stok: 5
- [ ] Submit

**Filter & Search:**
- [ ] Test search box
- [ ] Test filter kategori
- [ ] Test filter status

**Update & Delete:**
- [ ] Edit alat yang dibuat
- [ ] Delete alat test

**Expected Result:**
- ✅ CRUD berhasil
- ✅ Filter & search berfungsi
- ✅ Stats cards update otomatis

#### ✅ Test 7: Peminjaman
**Create Peminjaman:**
- [ ] Buka halaman `/peminjaman`
- [ ] Klik "Pinjam Alat"
- [ ] Pilih alat yang tersedia
- [ ] Isi tanggal pinjam (hari ini)
- [ ] Isi tanggal kembali (minggu depan)
- [ ] Isi keperluan
- [ ] Submit

**View Detail:**
- [ ] Klik icon "eye" untuk view detail
- [ ] Periksa semua info tampil lengkap

**Expected Result:**
- ✅ Peminjaman berhasil dibuat
- ✅ Stats update
- ✅ Detail modal berfungsi

#### ✅ Test 8: Pengembalian (Admin/Petugas)
**Process Return:**
- [ ] Buka halaman `/pengembalian`
- [ ] Periksa list alat yang dipinjam
- [ ] Klik "Kembalikan" pada salah satu item
- [ ] Pilih kondisi alat: "Baik"
- [ ] Isi catatan (opsional)
- [ ] Submit

**Expected Result:**
- ✅ Pengembalian berhasil
- ✅ Status berubah jadi "dikembalikan"
- ✅ List update

#### ✅ Test 9: Manajemen User (Admin Only)
**Create User:**
- [ ] Login sebagai admin
- [ ] Buka halaman `/users`
- [ ] Klik "Tambah User"
- [ ] Isi data user baru
- [ ] Pilih role
- [ ] Submit

**Update & Delete:**
- [ ] Edit user yang dibuat
- [ ] Coba delete user lain (bukan diri sendiri)

**Expected Result:**
- ✅ CRUD user berhasil
- ✅ Tidak bisa delete user sendiri
- ✅ Stats update

#### ✅ Test 10: Laporan (Admin/Petugas)
**Filter Laporan:**
- [ ] Buka halaman `/laporan`
- [ ] Set tanggal awal dan akhir
- [ ] Klik "Tampilkan"
- [ ] Periksa stats update
- [ ] Periksa chart "Alat Terpopuler" tampil
- [ ] Periksa "Peminjam Teraktif" tampil
- [ ] Periksa tabel detail transaksi

**Export:**
- [ ] Klik "Export Excel"
- [ ] Periksa alert konfirmasi

**Expected Result:**
- ✅ Filter berfungsi
- ✅ Charts tampil
- ✅ Data akurat

---

### D. Role-Based Access Tests

#### ✅ Test 11: Admin Access
- [ ] Login sebagai admin
- [ ] Periksa semua menu visible
- [ ] Akses semua halaman berhasil

**Expected Result:**
- ✅ Full access ke semua fitur

#### ✅ Test 12: Petugas Access
- [ ] Login sebagai petugas
- [ ] Periksa menu "Manajemen User" TIDAK tampil
- [ ] Coba akses `/users` manual (URL)
- [ ] Periksa redirect atau error

**Expected Result:**
- ✅ Menu user tidak tampil
- ✅ Tidak bisa akses user management

#### ✅ Test 13: Peminjam Access
- [ ] Login sebagai peminjam
- [ ] Periksa menu terbatas (Dashboard, Alat, Peminjaman)
- [ ] Menu Kategori, Pengembalian, Users, Laporan TIDAK tampil
- [ ] Coba akses halaman restricted manual

**Expected Result:**
- ✅ Menu terbatas
- ✅ Tidak bisa akses fitur admin/petugas

---

### E. UI/UX Tests

#### ✅ Test 14: Responsive Design
**Desktop (>1024px):**
- [ ] Sidebar fixed & visible
- [ ] Layout 2-kolom proper
- [ ] All elements properly spaced

**Tablet (768-1024px):**
- [ ] Sidebar collapsible
- [ ] Stats grid responsive
- [ ] Table scrollable

**Mobile (<768px):**
- [ ] Sidebar hidden by default
- [ ] Menu toggle button muncul
- [ ] Stats cards stacked (1 column)
- [ ] Table scrollable horizontal
- [ ] User details hidden di header

**Expected Result:**
- ✅ Responsive di semua device

#### ✅ Test 15: Animations & Interactions
- [ ] Hover effects pada cards
- [ ] Button hover animations
- [ ] Modal open/close smooth
- [ ] Page transition smooth
- [ ] Loading states tampil

**Expected Result:**
- ✅ Semua animasi smooth
- ✅ No janky transitions

#### ✅ Test 16: Toast Notifications
- [ ] Test success message (create data)
- [ ] Test error message (validasi gagal)
- [ ] Test info message
- [ ] Periksa toast auto-close setelah 3 detik

**Expected Result:**
- ✅ Toast muncul dengan styling proper
- ✅ Auto-dismiss

---

### F. Error Handling Tests

#### ✅ Test 17: Network Error
- [ ] Stop API server
- [ ] Coba load dashboard
- [ ] Periksa error message user-friendly
- [ ] Periksa console error logged

**Expected Result:**
- ✅ User-friendly error message
- ✅ No app crash

#### ✅ Test 18: Invalid Data
- [ ] Coba submit form kosong
- [ ] Coba submit dengan email invalid
- [ ] Coba submit dengan password < 8 karakter
- [ ] Periksa validasi message

**Expected Result:**
- ✅ Form validation berfungsi
- ✅ Error message jelas

#### ✅ Test 19: Session Expired
- [ ] Login normal
- [ ] Hapus token dari localStorage manual
- [ ] Refresh atau navigate
- [ ] Periksa redirect ke login

**Expected Result:**
- ✅ Auto-redirect ke login
- ✅ Token cleared

---

### G. Performance Tests

#### ✅ Test 20: Page Load Speed
- [ ] Measure initial load time (<3s)
- [ ] Check bundle size (Network tab)
- [ ] Check no memory leaks (Memory profiler)

**Expected Result:**
- ✅ Load time acceptable
- ✅ No memory leaks

#### ✅ Test 21: Search Performance
- [ ] Test search dengan 100+ items
- [ ] Periksa debounce berfungsi
- [ ] Periksa no lag saat typing

**Expected Result:**
- ✅ Search responsive
- ✅ Debounce prevents excessive API calls

---

## 🐛 Known Issues & Fixes

### Issue 1: Token Expired tanpa Warning
**Fix:** Implemented auto-redirect with toast notification

### Issue 2: Sidebar tidak close saat click outside (mobile)
**Fix:** Added click-outside handler

### Issue 3: Stats tidak update setelah CRUD
**Fix:** Added reload functions after success

---

## 📊 Testing Summary

```
Total Tests: 21
Passing: [ ]
Failing: [ ]
Success Rate: [ ]%
```

---

## 🚀 Ready for Production?

Before deploying:
- [ ] All tests passing
- [ ] No console errors
- [ ] Performance acceptable
- [ ] Security checks passed
- [ ] API endpoints secured
- [ ] CORS configured
- [ ] Environment variables set
- [ ] Error logging implemented
- [ ] Analytics ready (optional)

---

**Last Updated:** [Add date when testing completed]
**Tested By:** [Tester name]
**Environment:** Development
