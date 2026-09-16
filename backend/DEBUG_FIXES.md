# 🐛 Debug Fixes & Common Issues

## ⚠️ Common Problems & Solutions

### 1. **API Connection Error**

**Symptom:**
```
Failed to fetch
Cannot connect to http://localhost:8000
```

**Solutions:**
1. Pastikan API berjalan:
```bash
cd api
npm start
# Should show: Server running on port 8000
```

2. Check API URL di browser DevTools Console:
```javascript
console.log(CONFIG.API_URL)
// Should be: http://localhost:8000/api
```

3. Test API langsung:
```bash
# Di browser, buka:
http://localhost:8000/api/health
# atau
http://localhost:8000/api/kategori-alat
```

---

### 2. **Token/Session Issues**

**Symptom:**
```
Unauthorized (401)
Session expired
```

**Solutions:**

**Clear localStorage:**
```javascript
// Open browser console (F12)
localStorage.clear()
// Then refresh and login again
```

**Check token:**
```javascript
console.log('Token:', getToken())
console.log('User:', getUser())
```

**Manual fix:**
```javascript
// If token exists but invalid
removeToken()
window.location.href = '/'
```

---

### 3. **Sidebar Not Showing (Mobile)**

**Symptom:**
- Menu toggle button tidak muncul
- Sidebar tidak bisa dibuka di mobile

**Solutions:**

1. **Check viewport:**
```javascript
console.log('Width:', window.innerWidth)
// If < 768px, toggle should appear
```

2. **Force show toggle:**
```javascript
document.getElementById('menuToggle').style.display = 'flex'
```

3. **Check CSS:**
```css
/* Ensure this exists in layout */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }
}
```

---

### 4. **Modal Not Closing**

**Symptom:**
- Modal tetap terbuka setelah submit
- Background tidak clickable

**Solutions:**

1. **Manual close:**
```javascript
closeModal()
// or
document.getElementById('modalId').classList.remove('active')
```

2. **Check z-index:**
```css
.modal {
    z-index: 2000;
}
```

3. **Add ESC key handler:**
```javascript
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeModal()
    }
})
```

---

### 5. **Data Not Loading**

**Symptom:**
- Table shows "Loading..." forever
- Empty state tidak muncul

**Solutions:**

1. **Check API response:**
```javascript
// Open console, check for errors
fetch('http://localhost:8000/api/alat', {
    headers: {
        'Authorization': `Bearer ${getToken()}`
    }
})
.then(res => res.json())
.then(data => console.log(data))
```

2. **Check data structure:**
```javascript
// Response should be:
{
    success: true,
    data: [...]
}
```

3. **Fix empty state:**
```javascript
if (!data.data || data.data.length === 0) {
    showEmptyState()
}
```

---

### 6. **Search/Filter Not Working**

**Symptom:**
- Typing tidak filter data
- Filter dropdown tidak berefek

**Solutions:**

1. **Check event listeners:**
```javascript
// Ensure these exist:
document.getElementById('searchInput').addEventListener('input', filterData)
document.getElementById('filterStatus').addEventListener('change', filterData)
```

2. **Check filter logic:**
```javascript
function filterData() {
    console.log('Filter triggered')
    const search = document.getElementById('searchInput').value
    console.log('Search:', search)
    // Debug the filter logic
}
```

3. **Add debounce untuk search:**
```javascript
const debouncedFilter = debounce(filterData, 300)
searchInput.addEventListener('input', debouncedFilter)
```

---

### 7. **Stats Not Updating**

**Symptom:**
- Stats cards menunjukkan angka lama
- Tidak update setelah CRUD

**Solutions:**

1. **Reload data setelah CRUD:**
```javascript
if (data.success) {
    closeModal()
    loadAlat()  // ← Add this
    loadCategories()  // ← And this
}
```

2. **Update stats manually:**
```javascript
function updateStats() {
    document.getElementById('totalAlat').textContent = allAlat.length
    // etc...
}
```

---

### 8. **CSS Not Loading**

**Symptom:**
- Halaman terlihat broken
- No styling applied

**Solutions:**

1. **Check file paths:**
```html
<!-- Ensure these exist in <head> -->
<link rel="stylesheet" href="{{ asset('css/animations.css') }}">
<script src="{{ asset('js/app.js') }}"></script>
```

2. **Clear Laravel cache:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

3. **Check public directory:**
```bash
ls -la public/css/
ls -la public/js/
```

---

### 9. **Form Validation Not Working**

**Symptom:**
- Empty form dapat di-submit
- No error message

**Solutions:**

1. **Add HTML5 validation:**
```html
<input type="email" required>
<input type="password" minlength="8" required>
```

2. **Add JS validation:**
```javascript
if (!validateRequired('formId')) {
    showToast('Harap isi semua field!', 'warning')
    return false
}
```

3. **Check backend validation:**
```javascript
if (data.errors) {
    const errors = Object.values(data.errors).flat().join(', ')
    showToast(errors, 'danger')
}
```

---

### 10. **Role-Based Menu Not Hiding**

**Symptom:**
- Peminjam bisa lihat menu admin
- Menu tidak hilang berdasarkan role

**Solutions:**

1. **Check user role:**
```javascript
const user = getUser()
console.log('User role:', user.role)
```

2. **Force remove menu:**
```javascript
if (user.role === 'peminjam') {
    document.getElementById('menuKategori')?.remove()
    document.getElementById('menuUsers')?.remove()
    // etc...
}
```

3. **Add page-level protection:**
```javascript
// At top of each protected page
if (user.role !== 'admin') {
    window.location.href = '/dashboard'
}
```

---

## 🔧 Quick Debug Commands

### Check Everything:
```javascript
// Paste in console
console.log('=== DEBUG INFO ===')
console.log('API URL:', CONFIG.API_URL)
console.log('Token:', getToken())
console.log('User:', getUser())
console.log('Window Size:', window.innerWidth + 'x' + window.innerHeight)
console.log('User Agent:', navigator.userAgent)
```

### Test API Connection:
```javascript
fetch(CONFIG.API_URL + '/kategori-alat', {
    headers: { 'Authorization': `Bearer ${getToken()}` }
})
.then(res => res.json())
.then(data => console.log('API Response:', data))
.catch(err => console.error('API Error:', err))
```

### Force Logout & Reset:
```javascript
localStorage.clear()
sessionStorage.clear()
window.location.href = '/'
```

---

## 📝 Checklist Before Reporting Bug

- [ ] Clear browser cache (Ctrl+Shift+Del)
- [ ] Check console for errors (F12)
- [ ] Verify API is running (http://localhost:8000)
- [ ] Check localStorage has token
- [ ] Test in incognito/private mode
- [ ] Try different browser
- [ ] Check internet connection
- [ ] Restart browser
- [ ] Restart API server

---

## 🚑 Emergency Fixes

### Nuclear Option (Reset Everything):
```bash
# Stop all servers
# Clear Laravel cache
cd backend
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Restart API
cd api
# Kill process if running
npm start

# Restart Laravel
cd backend
php artisan serve
```

### Browser Reset:
1. Open DevTools (F12)
2. Right-click refresh button
3. Select "Empty Cache and Hard Reload"
4. Close all tabs
5. Reopen application

---

## 📞 Still Having Issues?

1. **Check logs:**
   - Browser Console (F12)
   - Network tab untuk failed requests
   - API terminal output

2. **Test step-by-step:**
   - Start with login only
   - Then test one feature at a time
   - Isolate the problematic feature

3. **Compare with working setup:**
   - Test on different machine
   - Use same browser/version
   - Check Node/PHP versions

---

## ✅ All Fixed! Now What?

Run the full testing checklist:
- See `TESTING_CHECKLIST.md`
- Complete all 21 test cases
- Document any remaining issues

**Happy Debugging! 🐛➡️✨**
