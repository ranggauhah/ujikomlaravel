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
