# API Documentation - UJIKOM
## Hak Akses dan CRUD User & Kategori

### Base URL
```
http://localhost:3000/api
```

---

## Authentication

### Login
**Endpoint:** `POST /auth/login`

**Request Body:**
```json
{
  "username": "admin",
  "password": "admin"
}
```

**Response Success:**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "id_user": 1,
    "nama_user": "admin",
    "username": "admin",
    "level": 1,
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  }
}
```

**Response Error:**
```json
{
  "success": false,
  "message": "Username atau password salah"
}
```

---

## CRUD User (Admin Only - Level 1)

### 1. Create User
**Endpoint:** `POST /user`

**Headers:**
```
Authorization: Bearer <token>
```

**Request Body:**
```json
{
  "nama_user": "Operator Baru",
  "username": "operator3",
  "password": "operator3",
  "level": 2
}
```

**Response Success:**
```json
{
  "success": true,
  "message": "User berhasil ditambahkan",
  "data": {
    "id_user": 4,
    "nama_user": "Operator Baru",
    "username": "operator3",
    "level": 2
  }
}
```

### 2. Read All Users
**Endpoint:** `GET /user`

**Headers:**
```
Authorization: Bearer <token>
```

**Response Success:**
```json
{
  "success": true,
  "message": "Data user berhasil diambil",
  "data": [
    {
      "id_user": 1,
      "nama_user": "admin",
      "username": "admin",
      "level": 1
    },
    {
      "id_user": 2,
      "nama_user": "operator1",
      "username": "operator1",
      "level": 2
    }
  ]
}
```

### 3. Read User by ID
**Endpoint:** `GET /user/:id`

**Headers:**
```
Authorization: Bearer <token>
```

**Response Success:**
```json
{
  "success": true,
  "message": "Data user berhasil diambil",
  "data": {
    "id_user": 1,
    "nama_user": "admin",
    "username": "admin",
    "level": 1
  }
}
```

### 4. Update User
**Endpoint:** `PUT /user/:id`

**Headers:**
```
Authorization: Bearer <token>
```

**Request Body:**
```json
{
  "nama_user": "Admin Updated",
  "username": "admin",
  "password": "newpassword",
  "level": 1
}
```

**Response Success:**
```json
{
  "success": true,
  "message": "User berhasil diubah",
  "data": {
    "id_user": 1,
    "nama_user": "Admin Updated",
    "username": "admin",
    "level": 1
  }
}
```

### 5. Delete User
**Endpoint:** `DELETE /user/:id`

**Headers:**
```
Authorization: Bearer <token>
```

**Response Success:**
```json
{
  "success": true,
  "message": "User berhasil dihapus"
}
```

---

## CRUD Kategori (Admin & Operator - Level 1 & 2)

### 1. Create Kategori
**Endpoint:** `POST /kategori`

**Headers:**
```
Authorization: Bearer <token>
```

**Request Body:**
```json
{
  "nama_kategori": "Elektronik"
}
```

**Response Success:**
```json
{
  "success": true,
  "message": "Kategori berhasil ditambahkan",
  "data": {
    "id_kategori": 1,
    "nama_kategori": "Elektronik"
  }
}
```

### 2. Read All Kategori
**Endpoint:** `GET /kategori`

**Headers:**
```
Authorization: Bearer <token>
```

**Response Success:**
```json
{
  "success": true,
  "message": "Data kategori berhasil diambil",
  "data": [
    {
      "id_kategori": 1,
      "nama_kategori": "Elektronik"
    },
    {
      "id_kategori": 2,
      "nama_kategori": "Furniture"
    }
  ]
}
```

### 3. Read Kategori by ID
**Endpoint:** `GET /kategori/:id`

**Headers:**
```
Authorization: Bearer <token>
```

**Response Success:**
```json
{
  "success": true,
  "message": "Data kategori berhasil diambil",
  "data": {
    "id_kategori": 1,
    "nama_kategori": "Elektronik"
  }
}
```

### 4. Update Kategori
**Endpoint:** `PUT /kategori/:id`

**Headers:**
```
Authorization: Bearer <token>
```

**Request Body:**
```json
{
  "nama_kategori": "Elektronik Updated"
}
```

**Response Success:**
```json
{
  "success": true,
  "message": "Kategori berhasil diubah",
  "data": {
    "id_kategori": 1,
    "nama_kategori": "Elektronik Updated"
  }
}
```

### 5. Delete Kategori
**Endpoint:** `DELETE /kategori/:id`

**Headers:**
```
Authorization: Bearer <token>
```

**Response Success:**
```json
{
  "success": true,
  "message": "Kategori berhasil dihapus"
}
```

---

## Hak Akses

### Admin (Level 1)
- ✅ Tambah User
- ✅ Edit User
- ✅ Hapus User
- ✅ Lihat User
- ✅ Tambah Kategori
- ✅ Edit Kategori
- ✅ Hapus Kategori
- ✅ Lihat Kategori

### Operator (Level 2)
- ❌ Tidak bisa akses User
- ✅ Tambah Kategori
- ✅ Edit Kategori
- ✅ Hapus Kategori
- ✅ Lihat Kategori

---

## Master User Default

| id_user | nama_user  | username   | password   | level |
|---------|------------|------------|------------|-------|
| 1       | admin      | admin      | admin      | 1     |
| 2       | operator1  | operator1  | operator1  | 2     |
| 3       | operator2  | operator2  | operator2  | 2     |

---

## Error Responses

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Token tidak valid"
}
```

### 403 Forbidden
```json
{
  "success": false,
  "message": "Akses ditolak. Hanya admin yang dapat mengakses"
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Data tidak ditemukan"
}
```

### 500 Internal Server Error
```json
{
  "success": false,
  "message": "Database error",
  "error": "Error message"
}
```
