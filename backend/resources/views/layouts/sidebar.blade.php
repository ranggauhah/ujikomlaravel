<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <i class="fas fa-cube"></i>
            <span>Sistem Alat</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('kategori') }}" class="nav-item {{ request()->routeIs('kategori') ? 'active' : '' }}" id="menuKategori">
            <i class="fas fa-tags"></i>
            <span>Kategori Alat</span>
        </a>
        <a href="{{ route('alat') }}" class="nav-item {{ request()->routeIs('alat') ? 'active' : '' }}">
            <i class="fas fa-boxes"></i>
            <span>Data Alat</span>
        </a>
        <a href="{{ route('peminjaman') }}" class="nav-item {{ request()->routeIs('peminjaman') ? 'active' : '' }}">
            <i class="fas fa-hand-holding"></i>
            <span>Peminjaman</span>
        </a>
        <a href="{{ route('pengembalian') }}" class="nav-item {{ request()->routeIs('pengembalian') ? 'active' : '' }}" id="menuPengembalian">
            <i class="fas fa-undo-alt"></i>
            <span>Pengembalian</span>
        </a>
        <a href="{{ route('users') }}" class="nav-item {{ request()->routeIs('users') ? 'active' : '' }}" id="menuUsers">
            <i class="fas fa-users-cog"></i>
            <span>Manajemen User</span>
        </a>
        <a href="{{ route('laporan') }}" class="nav-item {{ request()->routeIs('laporan') ? 'active' : '' }}" id="menuLaporan">
            <i class="fas fa-chart-bar"></i>
            <span>Laporan</span>
        </a>
        <a href="{{ route('log-aktivitas') }}" class="nav-item {{ request()->routeIs('log-aktivitas') ? 'active' : '' }}" id="menuLogAktivitas" style="display:none;">
            <i class="fas fa-history"></i>
            <span>Log Aktivitas</span>
        </a>
        <a href="{{ route('notifikasi') }}" class="nav-item {{ request()->routeIs('notifikasi') ? 'active' : '' }}" id="menuNotifikasi">
            <i class="fas fa-bell"></i>
            <span>Notifikasi</span>
            <span class="nav-badge" id="sidebarNotifBadge" style="display:none;">0</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="user-profile-avatar" id="sidebarAvatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="user-profile-info">
                <div class="user-profile-name" id="sidebarUserName">Loading...</div>
                <div class="user-profile-role" id="sidebarUserRole">-</div>
            </div>
        </div>
        <button class="btn-logout" onclick="logout()">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </button>
    </div>
</aside>

<script>
(function () {
    /*
    | Aturan visibilitas menu per role:
    |
    | Admin   : Dashboard, Kategori Alat, Data Alat, Peminjaman,
    |           Pengembalian (lihat saja), Manajemen User, Log Aktivitas, Notifikasi
    |
    | Petugas : Dashboard, Data Alat, Peminjaman,
    |           Pengembalian (lihat + kembalikan), Laporan, Notifikasi
    |
    | Peminjam: Dashboard, Data Alat, Peminjaman, Notifikasi
    */
    const stored = localStorage.getItem('user');
    if (!stored) return;
    try {
        const u = JSON.parse(stored);

        // Kategori Alat — hanya admin
        const menuKat = document.getElementById('menuKategori');
        if (menuKat) menuKat.style.display = u.role === 'admin' ? '' : 'none';

        // Pengembalian — admin & petugas (peminjam tidak bisa)
        const menuPengembalian = document.getElementById('menuPengembalian');
        if (menuPengembalian) menuPengembalian.style.display =
            (u.role === 'admin' || u.role === 'petugas') ? '' : 'none';

        // Manajemen User — hanya admin
        const menuUsers = document.getElementById('menuUsers');
        if (menuUsers) menuUsers.style.display = u.role === 'admin' ? '' : 'none';

        // Laporan — hanya petugas
        const menuLaporan = document.getElementById('menuLaporan');
        if (menuLaporan) menuLaporan.style.display = u.role === 'petugas' ? '' : 'none';

        // Log Aktivitas — hanya admin
        const menuLog = document.getElementById('menuLogAktivitas');
        if (menuLog) menuLog.style.display = u.role === 'admin' ? '' : 'none';

    } catch (e) { /* silent */ }
})();

// Update badge notifikasi di sidebar setiap 30 detik
(function updateSidebarBadge() {
    function syncBadge() {
        const count = window._notifUnreadCount || 0;
        const el = document.getElementById('sidebarNotifBadge');
        if (!el) return;
        if (count > 0) {
            el.textContent = count > 99 ? '99+' : count;
            el.style.display = 'flex';
        } else {
            el.style.display = 'none';
        }
    }
    // Sync tiap 2 detik (ikut polling topbar yg isi _notifUnreadCount)
    setInterval(syncBadge, 2000);
    syncBadge();
})();
</script>
