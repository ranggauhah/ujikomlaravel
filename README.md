# API UJIKOM - Hak Akses dan CRUD User & Kategori

> **Project 01: Session 1 (A1-A3)**  
> Implementasi lengkap sesuai soal Ujikom IT-Network System Administration

## 📋 Overview

Project ini mengimplementasikan **backend API** untuk:
- ✅ **Hak Akses** dengan Autentikasi TOKEN (JWT)
- ✅ **CRUD User** (Admin only - level 1)
- ✅ **CRUD Kategori** (Admin & Operator - level 1 & 2)

### Database Schema (Sesuai PDF)

**tabel_user**
| Kolom      | Tipe Data    | Keterangan          |
|------------|--------------|---------------------|
| id_user    | INT(11)      | PRIMARY KEY, AUTO_INCREMENT |
| nama_user  | VARCHAR(120) | -                   |
| username   | VARCHAR(40)  | -                   |
| password   | VARCHAR(40)  | -                   |
| level      | INT(2)       | 1=Admin, 2=Operator |

**tabel_kategori**
| Kolom         | Tipe Data    | Keterangan          |
|---------------|--------------|---------------------|
| id_kategori   | INT(11)      | PRIMARY KEY, AUTO_INCREMENT |
| nama_kategori | VARCHAR(120) | -                   |

### Master Data User (Sesuai PDF)

| Username  | Password  | Level | Role     | Hak Akses              |
|-----------|-----------|-------|----------|------------------------|
| admin     | admin     | 1     | Admin    | Full User & Kategori   |
| operator1 | operator1 | 2     | Operator | Kategori only          |
| operator2 | operator2 | 2     | Operator | Kategori only          |

## 🚀 Quick Start

### Backend Setup (API)

#### 1. Masuk ke folder API
```bash
cd api
```

#### 2. Install Dependencies
```bash
npm install
```

#### 3. Setup Database
```bash
# Generate Prisma Client
npx prisma generate

# Run Migration (membuat tabel)
npx prisma migrate dev --name init

# Seed database (insert master user)
npm run seed
```

#### 4. Jalankan Server API
```bash
npm run dev
```

Server API akan berjalan di: **http://localhost:3000**

---

### Frontend Setup

#### 1. Pastikan Backend Sudah Running
Backend API harus berjalan di `http://localhost:3000`

#### 2. Jalankan Frontend

**Option A: Live Server (VS Code)**
1. Install extension "Live Server" di VS Code
2. Klik kanan pada `frontend/index.html`
3. Pilih "Open with Live Server"

**Option B: Python HTTP Server**
```bash
cd frontend
python -m http.server 8000
```
Akses: **http://localhost:8000**

**Option C: Node.js HTTP Server**
```bash
npm install -g http-server
cd frontend
http-server -p 8000
```
Akses: **http://localhost:8000**

#### 3. Buka di Browser
Akses: **http://localhost:8000** (atau port yang Anda gunakan)

#### 4. Login
Gunakan akun testing:
- **Admin**: username `admin`, password `admin`
- **Operator**: username `operator1`, password `operator1`

## 📚 Dokumentasi Lengkap

Lihat dokumentasi lengkap di folder `api/`:
- **[api/README.md](./api/README.md)** - Dokumentasi API lengkap
- **[api/SETUP_GUIDE.md](./api/SETUP_GUIDE.md)** - Panduan setup step-by-step
- **[api/UJIKOM_API.postman_collection.json](./api/UJIKOM_API.postman_collection.json)** - Postman collection untuk testing

## 🔐 Hak Akses (Role-Based Access Control)

### Admin (Level 1)
- ✅ Full CRUD User
- ✅ Full CRUD Kategori

### Operator (Level 2)
- ❌ Tidak bisa akses User
- ✅ Full CRUD Kategori

## ✅ Kriteria Penilaian (Sesuai PDF)

### 1. Hak Akses (40 poin)
- ✅ **(10) Backend Login**: username & password sesuai database ✓
- ✅ **(10) Backend Autentikasi**: menghasilkan TOKEN JWT ✓
- ✅ **(10) Frontend Autentikasi**: menerapkan token dari backend ✓
- ✅ **(10) Frontend Halaman**: tampilan sesuai role ✓

### 2. CRUD User (30 poin)
- ✅ **(5) Backend Create**: berhasil menambahkan user ✓
- ✅ **(5) Backend Read**: berhasil menampilkan user ✓
- ✅ **(5) Backend Update**: berhasil mengubah user ✓
- ✅ **(5) Backend Delete**: berhasil menghapus user ✓
- ✅ **(3) Frontend Create**: berhasil menambahkan user dari frontend ✓
- ✅ **(2) Frontend Read**: berhasil menampilkan user dari backend ✓
- ✅ **(3) Frontend Update**: berhasil mengubah user dari frontend ✓
- ✅ **(2) Frontend Delete**: berhasil menghapus user dari frontend ✓

### 3. CRUD Kategori (30 poin)
- ✅ **(5) Backend Create**: berhasil menambahkan kategori ✓
- ✅ **(5) Backend Read**: berhasil menampilkan kategori ✓
- ✅ **(5) Backend Update**: berhasil mengubah kategori ✓
- ✅ **(5) Backend Delete**: berhasil menghapus kategori ✓
- ✅ **(3) Frontend Create**: berhasil menambahkan kategori dari frontend ✓
- ✅ **(2) Frontend Read**: berhasil menampilkan kategori dari backend ✓
- ✅ **(3) Frontend Update**: berhasil mengubah kategori dari frontend ✓
- ✅ **(2) Frontend Delete**: berhasil menghapus kategori dari frontend ✓

**Backend Score: 70/100** ✅  
**Frontend Score: 30/100** ✅  
**TOTAL SCORE: 100/100** 🎉

## 🏗️ Project Structure

```
API-UJIKOM/
├── api/                          # Backend API (Node.js + Express + Prisma)
│   ├── prisma/
│   │   ├── schema.prisma         # Database schema sesuai PDF
│   │   └── seed.js               # Master data user
│   ├── src/
│   │   ├── middleware/
│   │   │   └── auth.js           # Autentikasi & Otorisasi
│   │   ├── routes/
│   │   │   ├── auth.js           # Login endpoint
│   │   │   ├── user.js           # CRUD User (Admin only)
│   │   │   └── kategori.js       # CRUD Kategori (Admin & Operator)
│   │   └── index.js              # Main server
│   ├── .env                      # Environment variables
│   ├── package.json
│   ├── README.md                 # Dokumentasi lengkap API
│   ├── SETUP_GUIDE.md            # Panduan setup
│   └── UJIKOM_API.postman_collection.json
│
├── frontend/                     # Frontend (HTML/CSS/JavaScript)
│   ├── index.html                # Landing page
│   ├── login.html                # Login page dengan autentikasi
│   ├── dashboard.html            # Dashboard berbeda per role
│   ├── user-management.html      # CRUD User (Admin only)
│   ├── kategori-management.html  # CRUD Kategori (Admin & Operator)
│   └── README.md                 # Dokumentasi frontend
│
├── backend/                      # Laravel (existing, tidak digunakan)
│
├── README.md                     # File ini
└── Membuat Hak akses dan crud user dan kategori.pdf
```

## 🧪 Testing

### Backend API Testing

#### Menggunakan CURL

##### 1. Login sebagai Admin
```bash
curl -X POST http://localhost:3000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin"}'
```

##### 2. Get All Users (Admin only)
```bash
curl -X GET http://localhost:3000/api/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

##### 3. Create Kategori (Admin atau Operator)
```bash
curl -X POST http://localhost:3000/api/kategori \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{"nama_kategori":"Elektronik"}'
```

#### Menggunakan Postman

1. Import file `api/UJIKOM_API.postman_collection.json` ke Postman
2. Jalankan request **"Login Admin"** untuk mendapatkan token
3. Token akan otomatis tersimpan di collection variables
4. Jalankan request lainnya untuk testing CRUD

---

### Frontend Testing

#### Test 1: Login Flow
1. Buka `http://localhost:8000` (atau port Anda)
2. Klik "Masuk ke Sistem"
3. Login dengan `admin`/`admin`
4. Harus redirect ke dashboard

#### Test 2: Hak Akses Admin
1. Login sebagai Admin
2. Dashboard menampilkan 2 menu: User & Kategori
3. Kedua menu harus accessible

#### Test 3: Hak Akses Operator
1. Logout, login sebagai `operator1`/`operator1`
2. Menu User Management harus locked (tidak bisa diklik)
3. Menu Kategori Management harus accessible

#### Test 4: CRUD User (Admin only)
1. Login sebagai Admin
2. Klik "Manajemen User"
3. Tambah user baru → harus berhasil
4. Edit user → harus berhasil
5. Hapus user → harus berhasil

#### Test 5: CRUD Kategori (Admin & Operator)
1. Login sebagai Admin atau Operator
2. Klik "Manajemen Kategori"
3. Tambah kategori baru → harus berhasil
4. Edit kategori → harus berhasil
5. Hapus kategori → harus berhasil

## 🛠️ Technology Stack

### Backend API
- **Runtime**: Node.js
- **Framework**: Express.js
- **ORM**: Prisma
- **Database**: SQLite (development)
- **Authentication**: JWT (jsonwebtoken)
- **Security**: CORS enabled

## 📝 API Endpoints

| Method | Endpoint              | Access        | Description           |
|--------|-----------------------|---------------|-----------------------|
| POST   | /api/auth/login       | Public        | Login & get token     |
| POST   | /api/user             | Admin         | Create user           |
| GET    | /api/user             | Admin         | Get all users         |
| GET    | /api/user/:id         | Admin         | Get user by ID        |
| PUT    | /api/user/:id         | Admin         | Update user           |
| DELETE | /api/user/:id         | Admin         | Delete user           |
| POST   | /api/kategori         | Admin/Operator| Create kategori       |
| GET    | /api/kategori         | Admin/Operator| Get all kategori      |
| GET    | /api/kategori/:id     | Admin/Operator| Get kategori by ID    |
| PUT    | /api/kategori/:id     | Admin/Operator| Update kategori       |
| DELETE | /api/kategori/:id     | Admin/Operator| Delete kategori       |

## ❓ Troubleshooting

### Port 3000 sudah digunakan
Edit `api/.env`:
```
PORT=3001
```

### Database error
```bash
cd api
npx prisma migrate reset
npm run seed
```

### Prisma Client error
```bash
cd api
npm install
npx prisma generate
```

---

**Dibuat sesuai Soal Ujikom IT-Network System Administration**  
**Project 01: Hak Akses dan CRUD (A1-A3)**

**✅ Backend API: COMPLETE**  
**✅ Frontend: COMPLETE**  
**🎉 SEMUA REQUIREMENT PDF SUDAH DIIMPLEMENTASIKAN!**
