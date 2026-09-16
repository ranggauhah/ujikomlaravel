/**
 * Sistem Peminjaman Alat - Main JavaScript
 * Modern, Simple & Futuristic Design
 */

// Configuration
const CONFIG = {
    API_URL: window.location.hostname === 'localhost' 
        ? 'http://localhost:8000/api'  // ← Laravel API
        : 'http://localhost:8000/api',
    TOKEN_KEY: 'token',
    USER_KEY: 'user',
    TOAST_DURATION: 3000
};

// ============================================
// Authentication Functions
// ============================================

/**
 * Get JWT token from localStorage
 */
function getToken() {
    return localStorage.getItem(CONFIG.TOKEN_KEY);
}

/**
 * Save JWT token to localStorage
 */
function setToken(token) {
    localStorage.setItem(CONFIG.TOKEN_KEY, token);
}

/**
 * Remove token from localStorage
 */
function removeToken() {
    localStorage.removeItem(CONFIG.TOKEN_KEY);
    localStorage.removeItem(CONFIG.USER_KEY);
}

/**
 * Get user data from localStorage
 */
function getUser() {
    const user = localStorage.getItem(CONFIG.USER_KEY);
    return user ? JSON.parse(user) : null;
}

/**
 * Save user data to localStorage
 */
function setUser(user) {
    localStorage.setItem(CONFIG.USER_KEY, JSON.stringify(user));
}

/**
 * Check if user is authenticated
 */
function isAuthenticated() {
    return getToken() !== null && getUser() !== null;
}

/**
 * Logout user
 */
async function logout() {
    if (confirm('Yakin ingin logout?')) {
        try {
            await fetch(`${CONFIG.API_URL}/auth/logout`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${getToken()}`,
                    'Content-Type': 'application/json'
                }
            });
        } catch (error) {
            console.error('Logout error:', error);
        }
        
        removeToken();
        window.location.href = '/';
    }
}

// ============================================
// UI Helper Functions
// ============================================

/**
 * Show alert message
 */
function showAlert(message, type = 'success') {
    const alertBox = document.getElementById('alertBox');
    if (alertBox) {
        alertBox.className = `alert alert-${type}`;
        alertBox.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 
                              type === 'danger' ? 'exclamation-circle' : 
                              type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
            <span>${message}</span>
        `;
        alertBox.style.display = 'flex';
        
        setTimeout(() => {
            alertBox.style.display = 'none';
        }, CONFIG.TOAST_DURATION);
    } else {
        // Fallback to native alert
        alert(message);
    }
}

/**
 * Show toast notification
 */
function showToast(message, type = 'success') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.style.cssText = `
        position: fixed;
        top: 24px;
        right: 24px;
        background: ${type === 'success' ? '#10b981' : 
                     type === 'danger' ? '#ef4444' : 
                     type === 'warning' ? '#f59e0b' : '#3b82f6'};
        color: white;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        z-index: 9999;
        animation: slideInRight 0.3s ease;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 600;
        max-width: 400px;
    `;
    
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 
                          type === 'danger' ? 'times-circle' : 
                          type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, CONFIG.TOAST_DURATION);
}

/**
 * Toggle sidebar (mobile)
 */
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.classList.toggle('active');
        
        // Close sidebar when clicking outside (mobile only)
        if (sidebar.classList.contains('active') && window.innerWidth <= 768) {
            setTimeout(() => {
                document.addEventListener('click', closeSidebarOutside);
            }, 100);
        } else {
            document.removeEventListener('click', closeSidebarOutside);
        }
    }
}

/**
 * Close sidebar when clicking outside
 */
function closeSidebarOutside(event) {
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menuToggle');
    
    if (sidebar && !sidebar.contains(event.target) && event.target !== menuToggle && !menuToggle?.contains(event.target)) {
        sidebar.classList.remove('active');
        document.removeEventListener('click', closeSidebarOutside);
    }
}

/**
 * Format date to Indonesian format
 */
function formatDate(dateString) {
    if (!dateString) return '-';
    
    const date = new Date(dateString);
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    
    return date.toLocaleDateString('id-ID', options);
}

/**
 * Format currency to IDR
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

/**
 * Calculate days between two dates
 */
function daysBetween(date1, date2) {
    const oneDay = 24 * 60 * 60 * 1000;
    const firstDate = new Date(date1);
    const secondDate = new Date(date2);
    
    return Math.round(Math.abs((firstDate - secondDate) / oneDay));
}

/**
 * Get user initials
 */
function getUserInitials(name) {
    if (!name) return 'U';
    return name.split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);
}

/**
 * Debounce function for search
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ============================================
// API Helper Functions
// ============================================

/**
 * Generic API fetch function
 */
async function apiFetch(endpoint, options = {}) {
    const defaultOptions = {
        headers: {
            'Authorization': `Bearer ${getToken()}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    };
    
    const mergedOptions = {
        ...defaultOptions,
        ...options,
        headers: {
            ...defaultOptions.headers,
            ...options.headers
        }
    };
    
    try {
        const response = await fetch(`${CONFIG.API_URL}${endpoint}`, mergedOptions);
        
        // Handle different HTTP status codes
        if (response.status === 401) {
            showToast('Sesi Anda telah berakhir. Silakan login kembali.', 'danger');
            removeToken();
            setTimeout(() => {
                window.location.href = '/';
            }, 1500);
            throw new Error('Unauthorized');
        }
        
        if (response.status === 403) {
            showToast('Anda tidak memiliki akses ke fitur ini.', 'warning');
            throw new Error('Forbidden');
        }
        
        if (response.status === 404) {
            showToast('Data tidak ditemukan.', 'warning');
            throw new Error('Not Found');
        }
        
        if (response.status === 500) {
            showToast('Terjadi kesalahan pada server. Silakan coba lagi.', 'danger');
            throw new Error('Internal Server Error');
        }
        
        const data = await response.json();
        return data;
        
    } catch (error) {
        // Network error
        if (error.message === 'Failed to fetch' || error.name === 'TypeError') {
            showToast('Tidak dapat terhubung ke server. Pastikan API berjalan di http://localhost:8000', 'danger');
        }
        
        console.error('API Error:', error);
        throw error;
    }
}

/**
 * GET request
 */
async function apiGet(endpoint) {
    return apiFetch(endpoint, { method: 'GET' });
}

/**
 * POST request
 */
async function apiPost(endpoint, data) {
    return apiFetch(endpoint, {
        method: 'POST',
        body: JSON.stringify(data)
    });
}

/**
 * PUT request
 */
async function apiPut(endpoint, data) {
    return apiFetch(endpoint, {
        method: 'PUT',
        body: JSON.stringify(data)
    });
}

/**
 * DELETE request
 */
async function apiDelete(endpoint) {
    return apiFetch(endpoint, { method: 'DELETE' });
}

// ============================================
// Form Validation
// ============================================

/**
 * Validate email format
 */
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Validate password strength
 */
function isValidPassword(password) {
    return password.length >= 8;
}

/**
 * Validate required fields
 */
function validateRequired(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = 'var(--danger)';
            isValid = false;
        } else {
            field.style.borderColor = 'var(--gray-200)';
        }
    });
    
    return isValid;
}

// ============================================
// Loading State
// ============================================

/**
 * Show loading overlay
 */
function showLoading() {
    let overlay = document.getElementById('loadingOverlay');
    
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        `;
        
        overlay.innerHTML = `
            <div style="background: white; padding: 32px; border-radius: 16px; text-align: center;">
                <i class="fas fa-spinner fa-spin" style="font-size: 48px; color: var(--primary);"></i>
                <div style="margin-top: 16px; font-weight: 600; color: var(--gray-700);">Loading...</div>
            </div>
        `;
        
        document.body.appendChild(overlay);
    }
    
    overlay.style.display = 'flex';
}

/**
 * Hide loading overlay
 */
function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

// ============================================
// Initialize
// ============================================

// Add custom CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Export to window for global access
window.CONFIG = CONFIG;
window.getToken = getToken;
window.setToken = setToken;
window.removeToken = removeToken;
window.getUser = getUser;
window.setUser = setUser;
window.isAuthenticated = isAuthenticated;
window.logout = logout;
window.showAlert = showAlert;
window.showToast = showToast;
window.toggleSidebar = toggleSidebar;
window.formatDate = formatDate;
window.formatCurrency = formatCurrency;
window.daysBetween = daysBetween;
window.getUserInitials = getUserInitials;
window.debounce = debounce;
window.apiGet = apiGet;
window.apiPost = apiPost;
window.apiPut = apiPut;
window.apiDelete = apiDelete;
window.isValidEmail = isValidEmail;
window.isValidPassword = isValidPassword;
window.validateRequired = validateRequired;
window.showLoading = showLoading;
window.hideLoading = hideLoading;

// Backward compatibility
window.API_URL = CONFIG.API_URL;

// ============================================
// Centralized User UI Init
// ============================================

/**
 * Inisialisasi UI berdasarkan data user yang login.
 * Panggil sekali di tiap halaman setelah DOM siap.
 *
 * @param {object} options
 *   hideMenuForRole: { peminjam: ['menuKategori','menuPengembalian','menuUsers','menuLaporan'],
 *                      petugas:  ['menuUsers'] }
 *   onUnauthorized: callback jika role tidak boleh akses halaman ini
 */
function initUserUI(options = {}) {
    const user = getUser();
    if (!user) {
        window.location.href = '/';
        return null;
    }

    // Set nama & role di header dan sidebar
    const setEl = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
    setEl('userName',        user.name);
    setEl('userRole',        user.role);
    setEl('sidebarUserName', user.name);
    setEl('sidebarUserRole', user.role);

    // Set inisial avatar
    const initials = getUserInitials(user.name);
    setEl('headerAvatar',  initials);
    setEl('sidebarAvatar', initials);

    // Default hide-menu rules sesuai tabel modul UJIKOM:
    // Admin    : semua menu
    // Petugas  : Alat (lihat), Peminjaman (approve), Pengembalian (pantau), Laporan (cetak)
    //            → Sembunyikan: Kategori, Manajemen User
    // Peminjam : Alat (lihat), Peminjaman (ajukan)
    //            → Sembunyikan: Kategori, Pengembalian, Manajemen User, Laporan
    const defaultHide = {
        peminjam: ['menuKategori', 'menuPengembalian', 'menuUsers', 'menuLaporan'],
        petugas:  ['menuKategori', 'menuUsers'],
    };
    const hideRules = options.hideMenuForRole || defaultHide;
    const toHide = hideRules[user.role] || [];
    toHide.forEach(id => document.getElementById(id)?.remove());

    // Mobile toggle
    function checkMobile() {
        const toggle = document.getElementById('menuToggle');
        if (toggle) toggle.style.display = window.innerWidth <= 768 ? 'flex' : 'none';
    }
    checkMobile();
    window.addEventListener('resize', checkMobile);

    return user;
}

window.initUserUI = initUserUI;

console.log('✅ App.js loaded successfully');
