<header class="header">
    <div class="header-left">
        <button class="icon-btn" onclick="toggleSidebar()" style="display:none; margin-right:12px;" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>
        <h2>@yield('page-title', 'Dashboard')</h2>
    </div>
    <div class="header-right">

        {{-- ── Lonceng Notifikasi ── --}}
        <div class="notif-wrapper" id="notifWrapper">
            <button class="icon-btn notif-btn" id="notifBtn" onclick="toggleNotifDropdown()" title="Notifikasi">
                <i class="fas fa-bell"></i>
                {{-- Badge jumlah unread, disembunyikan via JS jika 0 --}}
                <span class="notif-badge" id="notifBadge" style="display:none;">0</span>
            </button>

            {{-- Dropdown panel notifikasi --}}
            <div class="notif-dropdown" id="notifDropdown" style="display:none;">
                <div class="notif-dropdown-header">
                    <span><i class="fas fa-bell"></i> Notifikasi</span>
                    <button class="notif-read-all-btn" id="notifReadAllBtn" onclick="markAllReadDropdown()" title="Tandai semua dibaca">
                        <i class="fas fa-check-double"></i> Semua dibaca
                    </button>
                </div>
                <div class="notif-dropdown-body" id="notifDropdownBody">
                    <div class="notif-loading">
                        <i class="fas fa-spinner fa-spin"></i> Memuat...
                    </div>
                </div>
                <div class="notif-dropdown-footer">
                    <a href="/notifikasi" class="notif-lihat-semua">
                        <i class="fas fa-list"></i> Lihat semua notifikasi
                    </a>
                </div>
            </div>
        </div>

        {{-- ── Profil User ── --}}
        <div class="user-info">
            <div class="user-avatar" id="headerAvatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="user-details">
                <span class="user-name" id="userName">Loading...</span>
                <span class="user-role" id="userRole">-</span>
            </div>
        </div>

    </div>
</header>

@push('styles')
<style>
/* ── Wrapper lonceng ────────────────────────────────────────────── */
.notif-wrapper {
    position: relative;
}

.notif-btn {
    position: relative;
    width: 44px;
    height: 44px;
}

/* Badge angka di atas lonceng */
.notif-badge {
    position: absolute;
    top: 4px;
    right: 4px;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    background: var(--danger);
    color: #fff;
    border-radius: 9px;
    font-size: 10px;
    font-weight: 700;
    line-height: 18px;
    text-align: center;
    border: 2px solid #fff;
    pointer-events: none;
    animation: badgePop 0.3s cubic-bezier(0.34,1.56,0.64,1);
}

@keyframes badgePop {
    from { transform: scale(0); }
    to   { transform: scale(1); }
}

/* ── Dropdown panel ─────────────────────────────────────────────── */
.notif-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    width: 360px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15), 0 0 0 1px var(--gray-200);
    z-index: 3000;
    overflow: hidden;
    animation: dropdownIn 0.2s cubic-bezier(0.16,1,0.3,1);
}

@keyframes dropdownIn {
    from { opacity: 0; transform: translateY(-8px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0)    scale(1);    }
}

.notif-dropdown-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    border-bottom: 1px solid var(--gray-200);
    font-weight: 700;
    font-size: 15px;
    color: var(--gray-900);
    background: var(--gray-50);
}

.notif-read-all-btn {
    background: none;
    border: none;
    color: var(--primary);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 6px;
    transition: background 0.2s;
}
.notif-read-all-btn:hover { background: rgba(99,102,241,0.1); }

.notif-dropdown-body {
    max-height: 340px;
    overflow-y: auto;
}
.notif-dropdown-body::-webkit-scrollbar { width: 4px; }
.notif-dropdown-body::-webkit-scrollbar-thumb {
    background: var(--gray-300); border-radius: 4px;
}

.notif-loading {
    text-align: center;
    padding: 24px;
    color: var(--gray-500);
    font-size: 14px;
}

/* Item satu notifikasi di dropdown */
.notif-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 13px 16px;
    border-bottom: 1px solid var(--gray-100);
    cursor: pointer;
    transition: background 0.15s;
    text-decoration: none;
    color: inherit;
}
.notif-item:last-child { border-bottom: none; }
.notif-item:hover { background: var(--gray-50); }

/* Warna berbeda untuk yang belum dibaca */
.notif-item.unread {
    background: rgba(99,102,241,0.04);
    border-left: 3px solid var(--primary);
}
.notif-item.unread:hover { background: rgba(99,102,241,0.08); }

/* Ikon tipe notifikasi */
.notif-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
    margin-top: 2px;
}
.notif-icon.pengajuan  { background:#ede9fe; color:#7c3aed; }
.notif-icon.disetujui  { background:#d1fae5; color:#065f46; }
.notif-icon.ditolak    { background:#fee2e2; color:#991b1b; }
.notif-icon.jatuh_tempo{ background:#fef3c7; color:#92400e; }
.notif-icon.terlambat  { background:#fee2e2; color:#dc2626; }

.notif-content { flex: 1; min-width: 0; }
.notif-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.notif-pesan {
    font-size: 12px;
    color: var(--gray-600);
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.notif-waktu {
    font-size: 11px;
    color: var(--gray-400);
    margin-top: 4px;
    white-space: nowrap;
}

.notif-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--primary);
    flex-shrink: 0;
    margin-top: 6px;
}

.notif-empty {
    text-align: center;
    padding: 32px 16px;
    color: var(--gray-400);
    font-size: 14px;
}
.notif-empty i { font-size: 36px; display: block; margin-bottom: 8px; opacity: 0.5; }

.notif-dropdown-footer {
    padding: 10px 16px;
    border-top: 1px solid var(--gray-200);
    background: var(--gray-50);
}
.notif-lihat-semua {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
    color: var(--primary);
    text-decoration: none;
    padding: 6px;
    border-radius: 8px;
    transition: background 0.2s;
}
.notif-lihat-semua:hover { background: rgba(99,102,241,0.1); }
</style>
@endpush

@push('scripts')
<script>
/* ─────────────────────────────────────────────────────────────────
   Notifikasi Lonceng — polling setiap 30 detik
   ───────────────────────────────────────────────────────────────── */
(function () {
    // Ikon per tipe notifikasi
    const TIPE_ICON = {
        pengajuan  : 'fa-clipboard-list',
        disetujui  : 'fa-check-circle',
        ditolak    : 'fa-times-circle',
        jatuh_tempo: 'fa-clock',
        terlambat  : 'fa-exclamation-triangle',
    };

    // Format waktu relatif (misal: "2 jam lalu")
    function relativeTime(ts) {
        if (!ts) return '';
        const diff = Math.floor((Date.now() - new Date(ts)) / 1000);
        if (diff < 60)   return 'baru saja';
        if (diff < 3600) return `${Math.floor(diff/60)} menit lalu`;
        if (diff < 86400)return `${Math.floor(diff/3600)} jam lalu`;
        return `${Math.floor(diff/86400)} hari lalu`;
    }

    // Update badge angka di lonceng
    function updateBadge(count) {
        const badge = document.getElementById('notifBadge');
        if (!badge) return;
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }

    // Render item di dropdown
    function renderItems(items) {
        const body = document.getElementById('notifDropdownBody');
        if (!body) return;

        if (!items || !items.length) {
            body.innerHTML = `<div class="notif-empty">
                <i class="fas fa-bell-slash"></i>
                Tidak ada notifikasi baru
            </div>`;
            return;
        }

        body.innerHTML = items.map(n => {
            const icon = TIPE_ICON[n.tipe] || 'fa-bell';
            return `
            <div class="notif-item ${n.dibaca ? '' : 'unread'}"
                 onclick="handleNotifClick(${n.id}, ${n.peminjaman_id || 'null'}, '${n.tipe}')">
                <div class="notif-icon ${n.tipe}">
                    <i class="fas ${icon}"></i>
                </div>
                <div class="notif-content">
                    <div class="notif-title">${n.judul}</div>
                    <div class="notif-pesan">${n.pesan}</div>
                    <div class="notif-waktu">${relativeTime(n.created_at)}</div>
                </div>
                ${n.dibaca ? '' : '<div class="notif-dot"></div>'}
            </div>`;
        }).join('');
    }

    // Ambil notifikasi unread dari API
    async function fetchUnread() {
        if (!getToken()) return;
        try {
            const res  = await fetch(`${API_URL}/notifikasi/unread`, {
                headers: { Authorization: `Bearer ${getToken()}` }
            });
            const json = await res.json();
            if (json.success) {
                updateBadge(json.total_unread);
                // Simpan ke cache agar dropdown bisa render tanpa fetch ulang
                window._notifCache = json.data;
                window._notifUnreadCount = json.total_unread;
            }
        } catch (e) { /* silent — jangan ganggu halaman utama */ }
    }

    // Toggle buka/tutup dropdown
    window.toggleNotifDropdown = function () {
        const dd = document.getElementById('notifDropdown');
        if (!dd) return;

        const isOpen = dd.style.display !== 'none';
        dd.style.display = isOpen ? 'none' : 'block';

        if (!isOpen) {
            // Render dari cache lalu fetch ulang di background
            renderItems(window._notifCache || []);
            fetchUnread().then(() => renderItems(window._notifCache || []));
        }
    };

    // Klik item: tandai dibaca → redirect ke halaman terkait
    window.handleNotifClick = async function (id, peminjamanId, tipe) {
        // Tandai dibaca di server
        try {
            await fetch(`${API_URL}/notifikasi/${id}/read`, {
                method: 'PATCH',
                headers: { Authorization: `Bearer ${getToken()}` }
            });
        } catch (e) { /* lanjut meski gagal */ }

        // Tutup dropdown
        const dd = document.getElementById('notifDropdown');
        if (dd) dd.style.display = 'none';

        // Refresh badge
        fetchUnread();

        // Navigasi ke halaman terkait
        if (peminjamanId) {
            if (['pengajuan', 'disetujui', 'ditolak', 'jatuh_tempo', 'terlambat'].includes(tipe)) {
                window.location.href = '/peminjaman';
                return;
            }
        }
        window.location.href = '/notifikasi';
    };

    // Tandai semua dibaca dari dropdown
    window.markAllReadDropdown = async function () {
        try {
            await fetch(`${API_URL}/notifikasi/read-all`, {
                method: 'POST',
                headers: { Authorization: `Bearer ${getToken()}` }
            });
            updateBadge(0);
            window._notifCache = (window._notifCache || []).map(n => ({...n, dibaca: true}));
            renderItems(window._notifCache);
            showToast('Semua notifikasi ditandai sudah dibaca', 'success');
        } catch (e) {
            showToast('Gagal menandai notifikasi', 'danger');
        }
    };

    // Tutup dropdown jika klik di luar
    document.addEventListener('click', function (e) {
        const wrapper = document.getElementById('notifWrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            const dd = document.getElementById('notifDropdown');
            if (dd) dd.style.display = 'none';
        }
    });

    // Polling tiap 30 detik
    fetchUnread();
    setInterval(fetchUnread, 30000);
})();
</script>
@endpush
