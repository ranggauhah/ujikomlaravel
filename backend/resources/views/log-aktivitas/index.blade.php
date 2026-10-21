@extends('layouts.app')

@section('title', 'Log Aktivitas - Sistem Peminjaman Alat')
@section('body-class', 'dashboard-page')

@section('content')
@include('layouts.sidebar')

<main class="main-content">
    @section('page-title', 'Log Aktivitas')
    @include('layouts.topbar')

    <div class="content">

        <!-- Stats -->
        <div class="stats-grid" style="margin-bottom:32px;">
            <div class="stat-card gradient-primary">
                <div class="stat-icon"><i class="fas fa-list-alt"></i></div>
                <div class="stat-content">
                    <div class="stat-value" id="statTotal">0</div>
                    <div class="stat-label">Total Log</div>
                </div>
            </div>
            <div class="stat-card gradient-success">
                <div class="stat-icon"><i class="fas fa-plus-circle"></i></div>
                <div class="stat-content">
                    <div class="stat-value" id="statCreate">0</div>
                    <div class="stat-label">Create</div>
                </div>
            </div>
            <div class="stat-card gradient-warning">
                <div class="stat-icon"><i class="fas fa-edit"></i></div>
                <div class="stat-content">
                    <div class="stat-value" id="statUpdate">0</div>
                    <div class="stat-label">Update</div>
                </div>
            </div>
            <div class="stat-card gradient-danger">
                <div class="stat-icon"><i class="fas fa-trash-alt"></i></div>
                <div class="stat-content">
                    <div class="stat-value" id="statDelete">0</div>
                    <div class="stat-label">Delete</div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card-body">
                <div class="filters" style="flex-wrap:wrap;gap:12px;">
                    <!-- Search user -->
                    <div class="search-box" style="flex:1;min-width:200px;">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Cari nama user atau tabel...">
                    </div>
                    <!-- Filter aksi -->
                    <select class="filter-select" id="filterAksi" style="min-width:160px;">
                        <option value="">Semua Aksi</option>
                        <option value="create">Create</option>
                        <option value="update">Update</option>
                        <option value="delete">Delete</option>
                    </select>
                    <!-- Filter tabel -->
                    <select class="filter-select" id="filterTabel" style="min-width:180px;">
                        <option value="">Semua Tabel</option>
                        <option value="alat">Alat</option>
                        <option value="kategori_alat">Kategori Alat</option>
                        <option value="peminjaman">Peminjaman</option>
                        <option value="pengembalian">Pengembalian</option>
                        <option value="users">Users</option>
                    </select>
                    <!-- Filter tanggal -->
                    <input type="date" id="filterTanggalAwal"
                           style="padding:10px 14px;border:2px solid var(--gray-200);
                                  border-radius:12px;font-size:14px;outline:none;"
                           title="Tanggal awal">
                    <input type="date" id="filterTanggalAkhir"
                           style="padding:10px 14px;border:2px solid var(--gray-200);
                                  border-radius:12px;font-size:14px;outline:none;"
                           title="Tanggal akhir">
                    <!-- Tombol reset -->
                    <button class="btn btn-secondary" onclick="resetFilter()"
                            style="width:auto;padding:10px 20px;">
                        <i class="fas fa-times"></i> Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabel -->
        <div class="card">
            <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
                <h3><i class="fas fa-history"></i> Riwayat Aktivitas</h3>
                <span id="totalTampil"
                      style="font-size:13px;color:var(--gray-500);font-weight:500;"></span>
            </div>
            <div class="card-body no-padding">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:60px;">No</th>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Role</th>
                                <th>Aksi</th>
                                <th>Tabel</th>
                                <th style="width:80px;">Detail</th>
                            </tr>
                        </thead>
                        <tbody id="logTable">
                            <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--gray-500);">
                                <i class="fas fa-spinner fa-spin" style="font-size:22px;"></i>
                                <div style="margin-top:8px;">Memuat data...</div>
                            </td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- ═══ Modal Detail Log ═══ -->
<div class="modal" id="detailModal">
    <div class="modal-content" style="max-width:620px;">
        <div class="modal-header">
            <h3><i class="fas fa-file-alt"></i> Detail Log Aktivitas</h3>
            <button class="modal-close" onclick="closeModal('detailModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="detailContent"></div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary"
                    onclick="closeModal('detailModal')"
                    style="width:auto;padding:12px 24px;">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
.badge-create { background:#d1fae5; color:#065f46; }
.badge-update { background:#fef3c7; color:#92400e; }
.badge-delete { background:#fee2e2; color:#991b1b; }
.badge-role-admin   { background:#ede9fe; color:#4f46e5; }
.badge-role-petugas { background:#dbeafe; color:#1e40af; }
.badge-role-peminjam{ background:#f0fdf4; color:#166534; }

.log-badge {
    display:inline-flex;align-items:center;gap:5px;
    padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;
}

.diff-box {
    background:var(--gray-50);border:1px solid var(--gray-200);
    border-radius:8px;padding:12px;font-size:12px;font-family:monospace;
    max-height:260px;overflow-y:auto;white-space:pre-wrap;word-break:break-all;
    line-height:1.6;
}
.diff-box::-webkit-scrollbar { width:4px; }
.diff-box::-webkit-scrollbar-thumb { background:var(--gray-300);border-radius:4px; }

.tabel-badge {
    background:var(--gray-100);color:var(--gray-700);
    padding:3px 8px;border-radius:6px;font-size:12px;font-family:monospace;
}
</style>
@endpush

@push('scripts')
<script>
(function () {
    if (!getToken()) { window.location.href = '/'; return; }
    const user = initUserUI();
    if (!user) return;

    // Hanya admin yang boleh akses halaman ini
    if (user.role !== 'admin') {
        window.location.href = '/dashboard';
        return;
    }

    /* ─── State ─────────────────────────────────────────────────────── */
    let allLogs     = [];
    let filteredLogs = [];

    /* ─── Helpers ────────────────────────────────────────────────────── */
    function aksiBadge(aksi) {
        const map = {
            create: ['create', 'plus-circle',  'Create'],
            update: ['update', 'edit',         'Update'],
            delete: ['delete', 'trash-alt',    'Delete'],
        };
        const [cls, ic, label] = map[aksi] || ['update', 'question', aksi];
        return `<span class="log-badge badge-${cls}">
                    <i class="fas fa-${ic}"></i> ${label}
                </span>`;
    }

    function roleBadge(role) {
        const label = role ? role.charAt(0).toUpperCase() + role.slice(1) : '-';
        return `<span class="log-badge badge-role-${role || 'peminjam'}">${label}</span>`;
    }

    function tabelBadge(tabel) {
        return `<span class="tabel-badge">${tabel}</span>`;
    }

    function formatWaktu(ts) {
        if (!ts) return '-';
        const d = new Date(ts);
        return d.toLocaleDateString('id-ID', {
            day:'2-digit', month:'short', year:'numeric'
        }) + ' ' + d.toLocaleTimeString('id-ID', {
            hour:'2-digit', minute:'2-digit', second:'2-digit'
        });
    }

    function prettyJson(str) {
        try {
            return JSON.stringify(JSON.parse(str), null, 2);
        } catch {
            return str || '-';
        }
    }

    /* ─── Load data ──────────────────────────────────────────────────── */
    async function loadLogs() {
        try {
            const res  = await fetch(`${API_URL}/laporan/log-aktivitas`, {
                headers: { Authorization: `Bearer ${getToken()}` }
            });
            const data = await res.json();
            if (data.success) {
                allLogs = data.data;
                updateStats(allLogs);
                applyFilter();
            } else {
                showError('Gagal memuat log aktivitas');
            }
        } catch (e) {
            console.error('loadLogs:', e);
            showError('Tidak dapat terhubung ke server');
        }
    }

    function updateStats(data) {
        document.getElementById('statTotal').textContent  = data.length;
        document.getElementById('statCreate').textContent = data.filter(l => l.aksi === 'create').length;
        document.getElementById('statUpdate').textContent = data.filter(l => l.aksi === 'update').length;
        document.getElementById('statDelete').textContent = data.filter(l => l.aksi === 'delete').length;
    }

    /* ─── Filter ─────────────────────────────────────────────────────── */
    ['searchInput','filterAksi','filterTabel',
     'filterTanggalAwal','filterTanggalAkhir'].forEach(id => {
        document.getElementById(id).addEventListener('input',  applyFilter);
        document.getElementById(id).addEventListener('change', applyFilter);
    });

    function applyFilter() {
        const q      = document.getElementById('searchInput').value.toLowerCase();
        const aksi   = document.getElementById('filterAksi').value;
        const tabel  = document.getElementById('filterTabel').value;
        const tAwal  = document.getElementById('filterTanggalAwal').value;
        const tAkhir = document.getElementById('filterTanggalAkhir').value;

        filteredLogs = allLogs.filter(l => {
            const nama   = (l.user?.name  || '').toLowerCase();
            const email  = (l.user?.email || '').toLowerCase();
            const tblNm  = (l.tabel       || '').toLowerCase();
            const matchQ = !q || nama.includes(q) || email.includes(q) || tblNm.includes(q);
            const matchA = !aksi  || l.aksi  === aksi;
            const matchT = !tabel || l.tabel === tabel;

            let matchD = true;
            if (tAwal || tAkhir) {
                const logDate = l.created_at ? l.created_at.substring(0, 10) : '';
                if (tAwal  && logDate < tAwal)  matchD = false;
                if (tAkhir && logDate > tAkhir) matchD = false;
            }

            return matchQ && matchA && matchT && matchD;
        });

        updateStats(filteredLogs);
        renderTable(filteredLogs);
    }

    function resetFilter() {
        document.getElementById('searchInput').value       = '';
        document.getElementById('filterAksi').value        = '';
        document.getElementById('filterTabel').value       = '';
        document.getElementById('filterTanggalAwal').value  = '';
        document.getElementById('filterTanggalAkhir').value = '';
        applyFilter();
    }

    /* ─── Render tabel ───────────────────────────────────────────────── */
    function renderTable(data) {
        const tbody = document.getElementById('logTable');
        document.getElementById('totalTampil').textContent =
            `Menampilkan ${data.length} dari ${allLogs.length} log`;

        if (!data.length) {
            tbody.innerHTML = `
                <tr><td colspan="7" class="empty-state">
                    <i class="fas fa-search"></i>
                    <h3>Tidak Ada Log</h3>
                    <p>Tidak ada aktivitas yang cocok dengan filter ini</p>
                </td></tr>`;
            return;
        }

        tbody.innerHTML = data.map((l, i) => `
            <tr>
                <td style="text-align:center;color:var(--gray-500);font-size:13px;">
                    ${i + 1}
                </td>
                <td style="white-space:nowrap;font-size:13px;">
                    <div style="font-weight:600;">${formatWaktu(l.created_at)}</div>
                </td>
                <td>
                    <div style="display:flex;align-items:center;gap:9px;">
                        <div style="width:34px;height:34px;border-radius:50%;flex-shrink:0;
                                    background:linear-gradient(135deg,#667eea,#764ba2);
                                    display:flex;align-items:center;justify-content:center;
                                    color:white;font-weight:700;font-size:13px;">
                            ${(l.user?.name || 'U')[0].toUpperCase()}
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:14px;">${l.user?.name || '-'}</div>
                            <div style="font-size:12px;color:var(--gray-500);">${l.user?.email || ''}</div>
                        </div>
                    </div>
                </td>
                <td>${roleBadge(l.user?.role)}</td>
                <td>${aksiBadge(l.aksi)}</td>
                <td>${tabelBadge(l.tabel)}</td>
                <td style="text-align:center;">
                    <button class="btn-icon btn-view"
                            onclick="showDetail(${l.id})" title="Lihat Detail">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>`).join('');
    }

    function showError(msg) {
        document.getElementById('logTable').innerHTML =
            `<tr><td colspan="7" class="empty-state">
                <i class="fas fa-exclamation-circle"></i><h3>${msg}</h3>
            </td></tr>`;
    }

    /* ─── Modal Detail ───────────────────────────────────────────────── */
    function showDetail(id) {
        const l = allLogs.find(x => x.id === id);
        if (!l) return;

        const dataBaru = prettyJson(l.data_baru);
        const dataLama = prettyJson(l.data_lama);

        document.getElementById('detailContent').innerHTML = `
            <div style="display:grid;gap:14px;">
                <!-- Info utama -->
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;">
                    <div style="padding:12px;background:var(--gray-50);border-radius:10px;">
                        <div style="font-size:11px;color:var(--gray-500);margin-bottom:6px;">ID Log</div>
                        <div style="font-weight:700;font-size:16px;">#${l.id}</div>
                    </div>
                    <div style="padding:12px;background:var(--gray-50);border-radius:10px;">
                        <div style="font-size:11px;color:var(--gray-500);margin-bottom:6px;">Aksi</div>
                        ${aksiBadge(l.aksi)}
                    </div>
                    <div style="padding:12px;background:var(--gray-50);border-radius:10px;">
                        <div style="font-size:11px;color:var(--gray-500);margin-bottom:6px;">Tabel</div>
                        ${tabelBadge(l.tabel)}
                    </div>
                </div>

                <!-- User info -->
                <div style="padding:12px;background:var(--gray-50);border-radius:10px;">
                    <div style="font-size:11px;color:var(--gray-500);margin-bottom:6px;">Dilakukan oleh</div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:38px;height:38px;border-radius:50%;
                                    background:linear-gradient(135deg,#667eea,#764ba2);
                                    display:flex;align-items:center;justify-content:center;
                                    color:white;font-weight:700;font-size:15px;">
                            ${(l.user?.name || 'U')[0].toUpperCase()}
                        </div>
                        <div>
                            <div style="font-weight:600;">${l.user?.name || '-'}</div>
                            <div style="font-size:12px;color:var(--gray-500);">
                                ${l.user?.email || ''} &nbsp;·&nbsp; ${roleBadge(l.user?.role)}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Waktu -->
                <div style="padding:12px;background:var(--gray-50);border-radius:10px;">
                    <div style="font-size:11px;color:var(--gray-500);margin-bottom:6px;">Waktu Aktivitas</div>
                    <div style="font-weight:600;">${formatWaktu(l.created_at)}</div>
                </div>

                ${l.aksi === 'update' && l.data_lama ? `
                <!-- Data lama -->
                <div>
                    <div style="font-size:12px;font-weight:600;color:var(--danger);
                                margin-bottom:6px;">
                        <i class="fas fa-minus-circle"></i> Data Sebelum Diubah
                    </div>
                    <div class="diff-box" style="border-color:#fca5a5;">${dataLama}</div>
                </div>` : ''}

                ${l.data_baru ? `
                <!-- Data baru -->
                <div>
                    <div style="font-size:12px;font-weight:600;color:var(--success);
                                margin-bottom:6px;">
                        <i class="fas fa-plus-circle"></i>
                        ${l.aksi === 'update' ? 'Data Setelah Diubah' :
                          l.aksi === 'delete' ? 'Data Yang Dihapus'  : 'Data Yang Dibuat'}
                    </div>
                    <div class="diff-box" style="border-color:#6ee7b7;">${dataBaru}</div>
                </div>` : ''}
            </div>`;

        document.getElementById('detailModal').classList.add('active');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }

    /* ─── Init ───────────────────────────────────────────────────────── */
    window.showDetail  = showDetail;
    window.closeModal  = closeModal;
    window.resetFilter = resetFilter;

    loadLogs();
})();
</script>
@endpush
@endsection
