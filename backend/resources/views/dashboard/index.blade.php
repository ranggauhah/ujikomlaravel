@extends('layouts.app')

@section('title', 'Dashboard - Sistem Peminjaman Alat')

@section('body-class', 'dashboard-page')

@section('content')
@include('layouts.sidebar')

<!-- Main Content -->
<main class="main-content">
    @section('page-title', 'Dashboard')
    @include('layouts.topbar')

    <!-- Dashboard Content -->
    <div class="content">
        <!-- Welcome Card -->
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; margin-bottom: 32px;">
            <div class="card-body" style="padding: 32px;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h1 style="color: white; margin-bottom: 10px; font-size: 32px; font-weight: 700;">
                            Selamat Datang, <span id="welcomeName">User</span>! 👋
                        </h1>
                        <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 16px;">
                            Sistem Manajemen Peminjaman Alat - Dashboard Overview
                        </p>
                    </div>
                    <div style="font-size: 80px; opacity: 0.2;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card gradient-primary">
                <div class="stat-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="totalAlat">0</div>
                    <div class="stat-label">Total Alat</div>
                    <div class="stat-trend up">
                        <i class="fas fa-arrow-up"></i>
                        <span>Semua alat tersedia</span>
                    </div>
                </div>
            </div>

            <div class="stat-card gradient-success">
                <div class="stat-icon">
                    <i class="fas fa-hand-holding"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="totalPeminjaman">0</div>
                    <div class="stat-label">Total Peminjaman</div>
                    <div class="stat-trend">
                        <i class="fas fa-history"></i>
                        <span>Semua transaksi</span>
                    </div>
                </div>
            </div>

            <div class="stat-card gradient-warning">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="sedangDipinjam">0</div>
                    <div class="stat-label">Sedang Dipinjam</div>
                    <div class="stat-trend">
                        <i class="fas fa-sync-alt"></i>
                        <span>Dalam proses</span>
                    </div>
                </div>
            </div>

            <div class="stat-card gradient-purple">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="totalUsers">0</div>
                    <div class="stat-label">Total User</div>
                    <div class="stat-trend">
                        <i class="fas fa-user-check"></i>
                        <span>User terdaftar</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-history"></i> Peminjaman Terbaru</h3>
                <a href="{{ route('peminjaman') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-eye"></i>
                    Lihat Semua
                </a>
            </div>
            <div class="card-body no-padding">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Peminjam</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="recentPeminjaman">
                            <tr>
                                <td colspan="5" class="loading">
                                    <i class="fas fa-spinner"></i> &nbsp; Loading...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script>
(function() {
    // Check authentication
    if (!getToken()) { window.location.href = '/'; return; }

    const user = initUserUI();
    if (!user) return; // initUserUI sudah redirect ke '/' jika tidak login
    document.getElementById('welcomeName').textContent = user.name;

    // Load dashboard data
    async function loadDashboard() {
        try {
            // Load stats
            const [alatRes, peminjamanRes, usersRes] = await Promise.all([
                fetch(`${API_URL}/alat`, {
                    headers: { 'Authorization': `Bearer ${getToken()}` }
                }),
                fetch(`${API_URL}/peminjaman`, {
                    headers: { 'Authorization': `Bearer ${getToken()}` }
                }),
                fetch(`${API_URL}/users`, {
                    headers: { 'Authorization': `Bearer ${getToken()}` }
                }).catch(() => ({ json: async () => ({ data: [] }) }))
            ]);

            const alatData = await alatRes.json();
            const peminjamanData = await peminjamanRes.json();
            const usersData = await usersRes.json();

            // Update stats
            document.getElementById('totalAlat').textContent = alatData.data?.length || 0;
            document.getElementById('totalPeminjaman').textContent = peminjamanData.data?.length || 0;
            document.getElementById('sedangDipinjam').textContent = 
                peminjamanData.data?.filter(p => p.status === 'dipinjam').length || 0;
            document.getElementById('totalUsers').textContent = usersData.data?.length || 0;

            // Load recent peminjaman
            const tbody = document.getElementById('recentPeminjaman');
            if (peminjamanData.data && peminjamanData.data.length > 0) {
                tbody.innerHTML = peminjamanData.data.slice(0, 5).map(p => `
                    <tr>
                        <td><strong>#${p.id}</strong></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 14px;">
                                    ${(p.user?.name || 'U')[0].toUpperCase()}
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: var(--gray-900);">${p.user?.name || '-'}</div>
                                    <div style="font-size: 13px; color: var(--gray-500);">${p.user?.email || ''}</div>
                                </div>
                            </div>
                        </td>
                        <td>${p.tanggal_pinjam || '-'}</td>
                        <td>${p.tanggal_kembali || '-'}</td>
                        <td>
                            <span class="badge badge-${
                                p.status === 'dikembalikan' ? 'success' : 
                                p.status === 'terlambat' ? 'danger' : 'warning'
                            }">
                                <i class="fas fa-${
                                    p.status === 'dikembalikan' ? 'check-circle' : 
                                    p.status === 'terlambat' ? 'exclamation-circle' : 'clock'
                                }"></i>
                                ${p.status}
                            </span>
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h3>Belum Ada Peminjaman</h3>
                            <p>Belum ada data peminjaman yang tersedia</p>
                        </td>
                    </tr>
                `;
            }
        } catch (error) {
            console.error('Error loading dashboard:', error);
        }
    }

    loadDashboard();
})();
</script>
@endpush
@endsection
