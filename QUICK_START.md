# 🚀 Quick Start - Sistem Peminjaman Alat

## 📦 Struktur Project

```
API-UJIKOM/
├── backend/           # Laravel Frontend + Views
│   ├── resources/views/   # Blade templates (UI)
│   ├── routes/api.php     # Laravel API routes
│   └── public/            # JS, CSS assets
├── api/               # Node.js API (TIDAK DIPAKAI)
└── docker-compose.yml # Docker setup
```

**PENTING:** Project ini menggunakan **Laravel untuk Frontend DAN API**, bukan Node.js API!

---

## 🐳 Start dengan Docker

### 1. Start Docker Containers
```bash
cd API-UJIKOM
docker-compose up -d
```

### 2. Check Status
```bash
docker ps
```

Harus ada:
- ✅ **laravel-api** → Port 8000 (API + Frontend)
- ✅ **MySQL-API** → Port 3306 (Database)
- ✅ **PhpMyAdmin-API** → Port 8081 (Optional)

### 3. Akses Aplikasi
- **Frontend:** http://localhost:8000
- **API:** http://localhost:8000/api
- **PhpMyAdmin:** http://localhost:8081

---

## 🔐 Login Demo

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Petugas | petugas@example.com | password |
| Peminjam | peminjam@example.com | password |

---

## 🎯 Fitur Utama

### Halaman Available:
- ✅ `/` - Landing page
- ✅ `/login` - Login form
- ✅ `/register` - Register form  
- ✅ `/dashboard` - Dashboard overview
- ✅ `/kategori` - CRUD Kategori (Admin/Petugas)
- ✅ `/alat` - CRUD Data Alat
- ✅ `/peminjaman` - Manajemen Peminjaman
- ✅ `/pengembalian` - Proses Pengembalian (Admin/Petugas)
- ✅ `/users` - Manajemen User (Admin only)
- ✅ `/laporan` - Laporan & Statistik (Admin/Petugas)

### Role-Based Access:
- **Admin:** Full access
- **Petugas:** Tidak bisa manage users
- **Peminjam:** Hanya lihat & pinjam alat

---

## 🐛 Troubleshooting

### Problem: Loading terus / Data tidak muncul

**Solusi:**
1. **Pastikan Docker running:**
   ```bash
   docker ps | grep laravel-api
   ```

2. **Test API di browser:**
   ```
   http://localhost:8000/api/kategori-alat
   ```
   Harus return JSON (atau 401 Unauthorized - itu normal)

3. **Clear browser cache:**
   - Ctrl+Shift+Del > Clear cache
   - Atau hard refresh: Ctrl+F5

4. **Check console (F12):**
   ```javascript
   console.log('API URL:', CONFIG.API_URL)
   // Harus: http://localhost:8000/api
   ```

### Problem: CORS Error

**Fix CORS di Laravel:**
File: `backend/config/cors.php`

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_origins' => ['*'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
'exposed_headers' => [],
'max_age' => 0,
'supports_credentials' => true,
```

---

## 📝 Development

### Laravel Commands (dalam Docker):
```bash
# Masuk ke container
docker exec -it laravel-api bash

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Docker Commands:
```bash
# Stop all
docker-compose down

# Start all
docker-compose up -d

# Restart Laravel only
docker-compose restart laravel-api

# View logs
docker logs laravel-api -f
```

---

## 🎨 Design Features

### UI Components:
- ✨ Modern gradient colors
- 💎 Glassmorphism effects
- 🎯 Smooth animations
- 📱 Fully responsive
- 🌙 Clean & futuristic design

### Tech Stack:
- **Frontend:** Laravel Blade + Vanilla JS
- **Styling:** Custom CSS (no framework)
- **Icons:** Font Awesome 6
- **Fonts:** Inter (Google Fonts)
- **API:** Laravel Sanctum Auth

---

## 📚 Documentation Files

- `FRONTEND_GUIDE.md` - Complete frontend docs
- `TESTING_CHECKLIST.md` - Testing guide (21 test cases)
- `DEBUG_FIXES.md` - Common issues & solutions
- `CHANGELOG.md` - All changes & bug fixes

---

## 🚀 Production Deployment

1. Update `.env` dengan production values
2. Set `APP_ENV=production`
3. Set `APP_DEBUG=false`
4. Generate new `APP_KEY`
5. Configure proper CORS
6. Setup SSL certificate
7. Optimize:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

## ✅ Ready to Use!

1. Start Docker: `docker-compose up -d`
2. Open: http://localhost:8000
3. Login: admin@example.com / password
4. Enjoy! 🎉

---

**Made with ❤️ using Laravel + Modern UI**
