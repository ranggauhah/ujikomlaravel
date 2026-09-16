# 🎨 Frontend Guide - Sistem Peminjaman Alat

## 📋 Daftar Halaman

### Halaman Publik
- **Welcome Page** (`/`) - Landing page dengan informasi sistem
- **Login** (`/login`) - Halaman login dengan akun demo
- **Register** (`/register`) - Halaman registrasi user baru

### Halaman Dashboard (Authenticated)
- **Dashboard** (`/dashboard`) - Overview sistem dengan statistik
- **Kategori Alat** (`/kategori`) - CRUD kategori alat (Admin/Petugas)
- **Data Alat** (`/alat`) - CRUD data alat dengan filter
- **Peminjaman** (`/peminjaman`) - Manajemen peminjaman alat
- **Pengembalian** (`/pengembalian`) - Proses pengembalian alat (Admin/Petugas)
- **Manajemen User** (`/users`) - CRUD user (Admin only)
- **Laporan** (`/laporan`) - Laporan dan statistik (Admin/Petugas)

## 🎨 Desain & Styling

### Tema Warna
```css
Primary: #6366f1 (Indigo)
Secondary: #8b5cf6 (Purple)
Success: #10b981 (Green)
Warning: #f59e0b (Amber)
Danger: #ef4444 (Red)
```

### Font
- **Font Family**: Inter (Google Fonts)
- **Fallback**: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto

### Komponen UI Modern
1. **Glassmorphism** - Background blur dengan transparency
2. **Gradient Buttons** - Tombol dengan gradient effect
3. **Smooth Animations** - Transisi halus pada semua interaksi
4. **Card Hover Effects** - Efek hover dengan shadow dan transform
5. **Responsive Design** - Mobile-friendly layout

## 🚀 Fitur Frontend

### Authentication
- ✅ Login dengan validasi
- ✅ Register user baru
- ✅ Auto-redirect jika sudah login
- ✅ Logout dengan konfirmasi
- ✅ Token management (localStorage)
- ✅ User session persistence

### Dashboard
- ✅ Stats cards dengan gradient
- ✅ Recent activities table
- ✅ User profile display
- ✅ Role-based menu visibility

### CRUD Operations
- ✅ Create dengan modal form
- ✅ Read dengan table & cards
- ✅ Update inline editing
- ✅ Delete dengan konfirmasi
- ✅ Search & filter functionality

### Data Visualization
- ✅ Stats cards dengan animasi
- ✅ Progress indicators
- ✅ Status badges
- ✅ Ranking lists (Top 5)
- ✅ Date range filters

## 📱 Responsive Breakpoints

```css
Desktop: >= 1024px (Full sidebar)
Tablet: 768px - 1023px (Collapsible sidebar)
Mobile: < 768px (Hidden sidebar with toggle)
```

## 🔐 Role-Based Access

### Admin
- ✅ Full access ke semua halaman
- ✅ CRUD User
- ✅ Lihat laporan lengkap

### Petugas
- ✅ CRUD Kategori & Alat
- ✅ Proses peminjaman & pengembalian
- ✅ Lihat laporan
- ❌ Tidak bisa CRUD User

### Peminjam
- ✅ Lihat dashboard
- ✅ Lihat data alat
- ✅ Ajukan peminjaman
- ❌ Tidak bisa CRUD data
- ❌ Tidak bisa proses pengembalian

## 🎯 Komponen Reusable

### 1. Modal
```javascript
// Show modal
document.getElementById('modalId').classList.add('active');

// Hide modal
document.getElementById('modalId').classList.remove('active');
```

### 2. Alert
```javascript
showAlert('Message here', 'success'); // success, danger, warning, info
```

### 3. Loading State
```html
<div class="loading">
    <i class="fas fa-spinner"></i> Loading...
</div>
```

### 4. Empty State
```html
<div class="empty-state">
    <i class="fas fa-inbox"></i>
    <h3>Title</h3>
    <p>Description</p>
</div>
```

### 5. Stat Card
```html
<div class="stat-card gradient-primary">
    <div class="stat-icon">
        <i class="fas fa-icon"></i>
    </div>
    <div class="stat-content">
        <div class="stat-value">123</div>
        <div class="stat-label">Label</div>
    </div>
</div>
```

## 🔧 JavaScript Helpers

### API Configuration
```javascript
const API_URL = 'http://localhost:8000/api';
```

### Token Management
```javascript
getToken()              // Get JWT token
setToken(token)         // Save token
removeToken()           // Remove token
```

### User Management
```javascript
getUser()               // Get user data
setUser(user)           // Save user data
```

### Common Functions
```javascript
logout()                // Logout user
showAlert(msg, type)    // Show alert message
toggleSidebar()         // Toggle sidebar (mobile)
```

## 📊 Data Fetching Pattern

```javascript
async function loadData() {
    try {
        const response = await fetch(`${API_URL}/endpoint`, {
            headers: { 
                'Authorization': `Bearer ${getToken()}`,
                'Content-Type': 'application/json'
            }
        });
        const data = await response.json();
        
        if (data.success) {
            // Handle success
            renderData(data.data);
        }
    } catch (error) {
        console.error('Error:', error);
        // Handle error
    }
}
```

## 🎨 Custom Styling Tips

### Gradient Backgrounds
```css
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### Box Shadow
```css
box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
```

### Border Radius
```css
border-radius: 16px; /* Cards */
border-radius: 12px; /* Buttons & Inputs */
border-radius: 8px;  /* Small elements */
```

### Transitions
```css
transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
```

## 🐛 Debugging

### Check Authentication
```javascript
console.log('Token:', getToken());
console.log('User:', getUser());
```

### Check API Response
```javascript
fetch(`${API_URL}/endpoint`, {
    headers: { 'Authorization': `Bearer ${getToken()}` }
})
.then(res => res.json())
.then(data => console.log('Response:', data));
```

## 📝 To-Do untuk Development

- [ ] Implementasi real-time notifications
- [ ] Upload foto alat
- [ ] Export laporan ke PDF/Excel
- [ ] Dark mode toggle
- [ ] Multi-language support
- [ ] PWA (Progressive Web App)
- [ ] WebSocket for live updates

## 🎓 Best Practices

1. **Always check authentication** sebelum load data
2. **Validate user input** sebelum submit
3. **Show loading states** saat fetch data
4. **Handle errors gracefully** dengan user-friendly message
5. **Use semantic HTML** untuk better accessibility
6. **Optimize images** untuk faster loading
7. **Test responsive** di berbagai device size
8. **Clean up event listeners** untuk prevent memory leaks

## 📞 Contact

Jika ada pertanyaan atau bug, silakan buat issue di repository.

---

**Made with ❤️ using Modern Web Technologies**
