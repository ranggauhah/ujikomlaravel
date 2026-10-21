@extends('layouts.app')

@section('title', 'Notifikasi - Sistem Peminjaman Alat')
@section('body-class', 'dashboard-page')

@section('content')
@include('layouts.sidebar')

<main class="main-content">
    @section('page-title', 'Notifikasi')
    @include('layouts.topbar')

    <div class="content">

        {{-- ── Stats ── --}}
        <div class="stats-grid" style="margin-bottom:32px;">
            <div class="stat-card gradient-primary">
                <div class="stat-icon"><i class="fas fa-bell"></i></div>
                <div class="stat-content">
                    <div class="stat-value" id="statTotal">0</div>
                    <div class="stat-label">Total Notifikasi</div>
                </div>
            </div>
            <div class="stat-card gradient-danger">
                <div class="stat-icon"><i class="fas fa-circle"></i></div>
                <div class="stat-content">
                    <div class="stat-value" id="statUnread">0</div>
                    <div class="stat-label">Belum Dibaca</div>
                </div>
            </div>
            <div class="stat-card gradient-success">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-content">
                    <div class="stat-value" id="statRead">0</div>
                    <div class="stat-label">Sudah Dibaca</div>
                </div>
            </div>
            <div class="stat-card gradient-warning">
                <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="stat-content">
                    <div class="stat-value" id="statTerlambat">0</div>
                    <div class="stat-label">Terlambat</div>
                </div>
            </div>
        </div>

        {{-- ── Filter & Aksi ── --}}
        <div class="card" style="margin-bottom:24px;">
            <div class="card-body">
                <div class="filters" style="flex-wrap:wrap;gap:12px;">
                    <div class="search-box" style="flex:1;min-width:220px;">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Cari judul atau pesan...">
                    </div>
                    <select class="filter-select" id="filterTipe">
                        <option value="">Semua Tipe</option>
                        <option value="pengajuan">Pengajuan</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="ditolak">Ditolak</option>
                        <option value="jatuh_tempo">Jatuh Tempo</option>
                        <option value="terlambat">Terlambat</option>
                    </select>
                    <select class="filter-select" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="unread">Belum Dibaca</option>
                        <option value="read">Sudah Dibaca</option>
                    </select>
                    {{-- Aksi massal --}}
                    <button class="btn btn-secondary" onclick="markAllRead()"
                            style="width:auto;padding:10px 20px;">
                        <i class="fas fa-check-double"></i> Tandai Semua Dibaca
                    </button>
                    <button class="btn btn-danger" onclick="hapusSudahDibaca()"
                            style="width:auto;padding:10px 20px;background:var(--danger);
                                   color:white;border-color:var(--danger);">
                        <i class="fas fa-trash-alt"></i> Hapus Sudah Dibaca
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Tabel Notifikasi ── --}}
        <div class="card">
            <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
                <h3><i class="fas fa-bell"></i> Daftar Notifikasi</h3>
                <span id="totalTampil" style="font-size:13px;color:var(--gray-500);font-weight:500;"></span>
            </div>
            <div class="card-body no-padding">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:44px;"></th>
                                <th>Notifikasi</th>
                                <th style="width:120px;">Tipe</th>
                                <th style="width:130px;">Waktu</th>
                                <th style="width:100px;">Status</th>
                                <th style="width:80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="notifTable">
                            <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--gray-500);">
                                <i class="fas fa-spinner fa-spin" style="font-size:22px;"></i>
                                <div style="margin-top:8px;">Memuat notifikasi...</div>
                            </td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- Pagination --}}
            <div id="paginationWrap"
                 style="display:none;padding:16px 24px;border-top:1px solid var(--gray-200);
                        display:flex;justify-content:center;gap:8px;flex-wrap:wrap;">
            </div>
        </div>

    </div>
</main>

@push('styles')
<style>
/* Baris unread punya latar sedikit berwarna */
.notif-row-unread {
    background: rgba(99,102,241,0.04) !important;
    border-left: 3px solid var(--primary);
}
.notif-row-unread:hover { background: rgba(99,102,241,0.08) !important; }

/* Ikon tipe di tabel */
.tipe-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}
.tipe-pengajuan   { background:#ede9fe; color:#7c3aed; }
.tipe-disetujui   { background:#d1fae5; color:#065f46; }
.tipe-ditolak     { background:#fee2e2; color:#991b1b; }
.tipe-jatuh_tempo { background:#fef3c7; color:#92400e; }
.tipe-terlambat   { background:#fee2e2; color:#dc2626; }

/* Pagination */
.page-btn {
    padding: 6px 12px;
    border: 2px solid var(--gray-200);
    border-radius: 8px;
    background: white;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-700);
    transition: all 0.2s;
}
.page-btn:hover, .page-btn.active {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}
.page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
</style>
@endpush

@push('scripts')
<script>
(function () {
    if (!getToken()) { window.location.href = '/'; return; }
    const user = initUserUI();
    if (!user) return;

    /* ── State ─────────────────────────────────────────────────── */
    let allNotif    = [];
    let filtered    = [];
    let currentPage = 1;
    const PER_PAGE  = 15;

    /* ── Helpers ────────────────────────────────────────────────── */
    const TIPE_ICON = {
        pengajuan  : 'fa-clipboard-list',
        disetujui  : 'fa-check-circle',
        ditolak    : 'fa-times-circle',
        jatuh_tempo: 'fa-clock',
        terlambat  : 'fa-exclamation-triangle',
    };
    const TIPE_LABEL = {
        pengajuan  : 'Pengajuan',
        disetujui  : 'Disetujui',
        ditolak    : 'Ditolak',
        jatuh_tempo: 'Jatuh Tempo',
        terlambat  : 'Terlambat',
    };

    function formatWaktu(ts) {
        if (!ts) return '-';
        const d = new Date(ts);
        return d.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' })
             + '<br><span style="font-size:11px;color:var(--gray-400);">'
             + d.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' })
             + '</span>';
    }

    /* ── Load semua notifikasi (pagination server-side halaman 1) ─ */
    async function loadNotifikasi(page = 1) {
        try {
            const res  = await fetch(`${API_URL}/notifikasi?page=${page}`, {
                headers: { Authorization: `Bearer ${getToken()}` }
            });
            const json = await res.json();
            if (json.success) {
                // Simpan data halaman ini dan metadata pagination
                allNotif = json.data.data || [];
                window._paginateMeta = {
                    currentPage: json.data.current_page,
                    lastPage:    json.data.last_page,
                    total:       json.data.total,
                };
                currentPage = json.data.current_page;
                applyFilter();
                renderPagination();
            }
        } catch (e) {
            document.getElementById('notifTable').innerHTML =
                `<tr><td colspan="6" class="empty-state">
                    <i class="fas fa-exclamation-circle"></i>
                    <h3>Gagal memuat notifikasi</h3>
                 </td></tr>`;
        }
    }

    /* ── Filter lokal (dalam satu halaman) ──────────────────────── */
    ['searchInput', 'filterTipe', 'filterStatus'].forEach(id => {
        document.getElementById(id).addEventListener('input',  applyFilter);
        document.getElementById(id).addEventListener('change', applyFilter);
    });

    function applyFilter() {
        const q      = document.getElementById('searchInput').value.toLowerCase();
        const tipe   = document.getElementById('filterTipe').value;
        const status = document.getElementById('filterStatus').value;

        filtered = allNotif.filter(n => {
            const matchQ = !q
                || n.judul.toLowerCase().includes(q)
                || n.pesan.toLowerCase().includes(q);
            const matchT = !tipe   || n.tipe === tipe;
            const matchS = !status
                || (status === 'unread' && !n.dibaca)
                || (status === 'read'   &&  n.dibaca);
            return matchQ && matchT && matchS;
        });

        updateStats();
        renderTable();
    }

    /* ── Stats ──────────────────────────────────────────────────── */
    function updateStats() {
        const meta = window._paginateMeta || {};
        document.getElementById('statTotal').textContent     = meta.total || allNotif.length;
        document.getElementById('statUnread').textContent    = allNotif.filter(n => !n.dibaca).length;
        document.getElementById('statRead').textContent      = allNotif.filter(n =>  n.dibaca).length;
        document.getElementById('statTerlambat').textContent = allNotif.filter(n => n.tipe === 'terlambat').length;
    }

    /* ── Render tabel ────────────────────────────────────────────── */
    function renderTable() {
        const tbody = document.getElementById('notifTable');
        document.getElementById('totalTampil').textContent =
            `Menampilkan ${filtered.length} notifikasi`;

        if (!filtered.length) {
            tbody.innerHTML = `
                <tr><td colspan="6" class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <h3>Tidak Ada Notifikasi</h3>
                    <p>Belum ada notifikasi yang sesuai filter</p>
                </td></tr>`;
            return;
        }

        tbody.innerHTML = filtered.map(n => {
            const icon  = TIPE_ICON[n.tipe]  || 'fa-bell';
            const label = TIPE_LABEL[n.tipe] || n.tipe;
            return `
            <tr class="${n.dibaca ? '' : 'notif-row-unread'}">
                <td style="text-align:center;">
                    <div style="width:36px;height:36px;border-radius:10px;
                                display:flex;align-items:center;justify-content:center;
                                font-size:15px;"
                         class="tipe-badge tipe-${n.tipe}" style="padding:0;">
                        <i class="fas ${icon}"></i>
                    </div>
                </td>
                <td>
                    <div style="font-weight:${n.dibaca ? '500' : '700'};
                                color:var(--gray-900);margin-bottom:3px;">
                        ${n.judul}
                    </div>
                    <div style="font-size:13px;color:var(--gray-600);line-height:1.4;">
                        ${n.pesan}
                    </div>
                </td>
                <td>
                    <span class="tipe-badge tipe-${n.tipe}">
                        <i class="fas ${icon}"></i> ${label}
                    </span>
                </td>
                <td style="font-size:13px;white-space:nowrap;">${formatWaktu(n.created_at)}</td>
                <td>
                    ${n.dibaca
                        ? `<span class="badge badge-success"><i class="fas fa-check"></i> Dibaca</span>`
                        : `<span class="badge badge-primary"><i class="fas fa-circle" style="font-size:8px;"></i> Baru</span>`
                    }
                </td>
                <td>
                    <div class="action-buttons">
                        ${!n.dibaca ? `
                        <button class="btn-icon btn-view"
                                onclick="tandaiDibaca(${n.id})"
                                title="Tandai dibaca"
                                style="background:rgba(99,102,241,0.1);color:var(--primary);">
                            <i class="fas fa-check"></i>
                        </button>` : ''}
                        <button class="btn-icon btn-delete"
                                onclick="hapusSatu(${n.id})"
                                title="Hapus">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
        }).join('');
    }

    /* ── Pagination render ──────────────────────────────────────── */
    function renderPagination() {
        const meta = window._paginateMeta;
        const wrap = document.getElementById('paginationWrap');
        if (!meta || meta.lastPage <= 1) {
            wrap.style.display = 'none';
            return;
        }
        wrap.style.display = 'flex';

        let html = `
            <button class="page-btn" ${meta.currentPage===1 ? 'disabled' : ''}
                    onclick="goPage(${meta.currentPage - 1})">
                <i class="fas fa-chevron-left"></i>
            </button>`;

        for (let p = 1; p <= meta.lastPage; p++) {
            html += `<button class="page-btn ${p === meta.currentPage ? 'active' : ''}"
                             onclick="goPage(${p})">${p}</button>`;
        }

        html += `
            <button class="page-btn" ${meta.currentPage===meta.lastPage ? 'disabled' : ''}
                    onclick="goPage(${meta.currentPage + 1})">
                <i class="fas fa-chevron-right"></i>
            </button>`;

        wrap.innerHTML = html;
    }

    window.goPage = (p) => loadNotifikasi(p);

    /* ── Aksi ───────────────────────────────────────────────────── */

    // Tandai satu dibaca
    window.tandaiDibaca = async function (id) {
        try {
            await fetch(`${API_URL}/notifikasi/${id}/read`, {
                method: 'PATCH',
                headers: { Authorization: `Bearer ${getToken()}` }
            });
            // Update lokal tanpa reload
            const n = allNotif.find(x => x.id === id);
            if (n) n.dibaca = true;
            applyFilter();
            showToast('Notifikasi ditandai sudah dibaca', 'success');
        } catch (e) { showToast('Gagal menandai', 'danger'); }
    };

    // Tandai semua dibaca
    window.markAllRead = async function () {
        try {
            const res  = await fetch(`${API_URL}/notifikasi/read-all`, {
                method: 'POST',
                headers: { Authorization: `Bearer ${getToken()}` }
            });
            const json = await res.json();
            if (json.success) {
                allNotif.forEach(n => n.dibaca = true);
                applyFilter();
                showToast(json.message, 'success');
            }
        } catch (e) { showToast('Gagal menandai semua', 'danger'); }
    };

    // Hapus satu
    window.hapusSatu = async function (id) {
        if (!confirm('Hapus notifikasi ini?')) return;
        try {
            const res  = await fetch(`${API_URL}/notifikasi/${id}`, {
                method: 'DELETE',
                headers: { Authorization: `Bearer ${getToken()}` }
            });
            const json = await res.json();
            if (json.success) {
                allNotif = allNotif.filter(n => n.id !== id);
                applyFilter();
                showToast('Notifikasi dihapus', 'success');
            } else {
                showToast(json.message || 'Gagal menghapus', 'danger');
            }
        } catch (e) { showToast('Gagal menghapus', 'danger'); }
    };

    // Hapus semua yang sudah dibaca
    window.hapusSudahDibaca = async function () {
        const jumlah = allNotif.filter(n => n.dibaca).length;
        if (!jumlah) { showToast('Tidak ada notifikasi yang sudah dibaca', 'warning'); return; }
        if (!confirm(`Hapus ${jumlah} notifikasi yang sudah dibaca?`)) return;
        try {
            const res  = await fetch(`${API_URL}/notifikasi/hapus-semua`, {
                method: 'DELETE',
                headers: { Authorization: `Bearer ${getToken()}` }
            });
            const json = await res.json();
            if (json.success) {
                allNotif = allNotif.filter(n => !n.dibaca);
                applyFilter();
                showToast(json.message, 'success');
            }
        } catch (e) { showToast('Gagal menghapus', 'danger'); }
    };

    /* ── Init ───────────────────────────────────────────────────── */
    loadNotifikasi(1);
})();
</script>
@endpush
@endsection
