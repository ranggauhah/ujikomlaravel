# 🚀 Cara Menjalankan Project dengan Docker

## Prerequisites
- Docker sudah terinstall di sistem
- Docker Compose sudah terinstall

## Step-by-Step

### 1. Clean Docker Cache (Optional tapi recommended)
```bash
# Masuk ke WSL
wsl -d Ubuntu

# Clean Docker
cd /home/lenovo/API-UJIKOM
docker-compose down -v
docker system prune -af --volumes
```

### 2. Start Semua Services dengan Docker Compose
```bash
cd /home/lenovo/API-UJIKOM
docker-compose up -d --build
```

Perintah ini akan:
- Build image untuk Node.js API
- Install dependencies (`npm install`)
- Generate Prisma Client (`npx prisma generate`)
- Run migrations (`npx prisma migrate deploy`)
- Seed database dengan master user (`npm run seed`)
- Start API server di port 3000
- Start Frontend dengan Nginx di port 8080

### 3. Check Status Container
```bash
docker-compose ps
```

Expected output:
```
NAME                 STATUS              PORTS
nodejs-ujikom-api    Up                  0.0.0.0:3000->3000/tcp
ujikom-frontend      Up                  0.0.0.0:8080->80/tcp
```

### 4. Check Logs (jika ada error)
```bash
# Log API
docker-compose logs -f api-nodejs

# Log Frontend
docker-compose logs -f frontend
```

### 5. Akses Aplikasi

**Frontend:**
```
http://localhost:8080
```

**API:**
```
http://localhost:3000
```

**API Documentation:**
```
http://localhost:3000/
```

## Testing

### Test API
```bash
# Test root endpoint
curl http://localhost:3000

# Test login
curl -X POST http://localhost:3000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin"}'
```

### Test Frontend
1. Buka browser: `http://localhost:8080`
2. Klik "Masuk ke Sistem"
3. Login dengan `admin`/`admin`
4. Seharusnya berhasil masuk ke dashboard

## Stop Services

```bash
# Stop containers
docker-compose stop

# Stop dan hapus containers
docker-compose down

# Stop, hapus containers + volumes
docker-compose down -v
```

## Rebuild (jika ada perubahan code)

```bash
# Rebuild dan restart
docker-compose up -d --build --force-recreate
```

## Troubleshooting

### Port sudah digunakan
Jika port 3000 atau 8080 sudah digunakan, edit `docker-compose.yml`:
```yaml
ports:
  - "3001:3000"  # Ubah 3000 ke 3001 untuk API
  - "8081:80"    # Ubah 8080 ke 8081 untuk Frontend
```

### Database error
```bash
# Reset database
docker-compose exec api-nodejs npx prisma migrate reset
docker-compose exec api-nodejs npm run seed
```

### Container tidak start
```bash
# Check logs
docker-compose logs api-nodejs

# Restart container
docker-compose restart api-nodejs
```

### CORS error di frontend
Pastikan akses frontend melalui `http://localhost:8080` bukan `file://`

---

## Quick Commands Cheat Sheet

```bash
# Start all
docker-compose up -d

# Stop all
docker-compose down

# Restart API only
docker-compose restart api-nodejs

# View logs
docker-compose logs -f

# Clean everything
docker-compose down -v && docker system prune -af --volumes

# Rebuild from scratch
docker-compose down -v && docker-compose up -d --build
```

---

**SELAMAT! Project UJIKOM Anda siap digunakan! 🎉**
