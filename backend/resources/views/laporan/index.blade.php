@extends('layouts.app')

@section('title', 'Laporan - Sistem Peminjaman Alat')

@section('body-class', 'dashboard-page')

@section('content')
@include('layouts.sidebar')

<!-- Main Content -->
<main class="main-content">
    @section('page-title', 'Laporan')
    @include('layouts.topbar')

    <!-- Content -->
    <div class="content">
        <!-- Filter Periode -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-filter"></i> Filter Periode</h3>
            </div>
            <div class="card-body">
                <div class="filters">
                    <div class="form-group" style="margin: 0; flex: 1; min-width: 200px;">
                        <label for="tanggal_awal" style="margin-bottom: 8px;">Tanggal Awal</label>
                        <input type="date" id="tanggal_awal" style="width: 100%; padding: 12px; border: 2px solid var(--gray-200); border-radius: 12px;">
                    </div>
                    <div class="form-group" style="margin: 0; flex: 1; min-width: 200px;">
                        <label for="tanggal_akhir" style="margin-bottom: 8px;">Tanggal Akhir</label>
                        <input type="date" id="tanggal_akhir" style="width: 100%; padding: 12px; border: 2px solid var(--gray-200); border-radius: 12px;">
                    </div>
                    <button class="btn btn-primary" onclick="filterLaporan()" style="width: auto; padding: 12px 24px; align-self: flex-end;">
                        <i class="fas fa-search"></i>
                        <span>Tampilkan</span>
                    </button>
                    <button class="btn btn-success" onclick="exportLaporan()" style="width: auto; padding: 12px 24px; align-self: flex-end;">
                        <i class="fas fa-file-excel"></i>
                        <span>Export Excel</span>
                    </button>
                    <button class="btn btn-secondary" onclick="exportLaporanPDF()" style="width: auto; padding: 12px 24px; align-self: flex-end; background: #7c3aed; color: white; border-color: #7c3aed;">
                        <i class="fas fa-print"></i>
                        <span>Cetak PDF</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="stats-grid">
            <div class="stat-card gradient-primary">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="totalTransaksi">0</div>
                    <div class="stat-label">Total Transaksi</div>
                </div>
            </div>
            <div class="stat-card gradient-success">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="totalSelesai">0</div>
                    <div class="stat-label">Selesai</div>
                </div>
            </div>
            <div class="stat-card gradient-warning">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="totalProses">0</div>
                    <div class="stat-label">Dalam Proses</div>
                </div>
            </div>
            <div class="stat-card gradient-danger">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="totalTerlambat">0</div>
                    <div class="stat-label">Terlambat</div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px; margin-bottom: 24px;">
            <!-- Alat Terpopuler -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-fire"></i> Alat Terpopuler</h3>
                </div>
                <div class="card-body" id="popularAlat">
                    <div class="loading">
                        <i class="fas fa-spinner"></i> Loading...
                    </div>
                </div>
            </div>

            <!-- Peminjam Teraktif -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-star"></i> Peminjam Teraktif</h3>
                </div>
                <div class="card-body" id="topUsers">
                    <div class="loading">
                        <i class="fas fa-spinner"></i> Loading...
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Laporan -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-table"></i> Detail Transaksi</h3>
            </div>
            <div class="card-body no-padding">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tanggal</th>
                                <th>Peminjam</th>
                                <th>Alat</th>
                                <th>Durasi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="laporanTable">
                            <tr>
                                <td colspan="6" class="loading">
                                    <i class="fas fa-spinner"></i> Loading...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

@push('styles')
<style>
    .rank-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px;
        background: var(--gray-50);
        border-radius: 12px;
        margin-bottom: 12px;
        transition: all 0.3s;
    }

    .rank-item:hover {
        background: var(--gray-100);
        transform: translateX(4px);
    }

    .rank-number {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 18px;
    }

    .rank-content {
        flex: 1;
    }

    .rank-name {
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 4px;
    }

    .rank-stat {
        font-size: 13px;
        color: var(--gray-600);
    }

    .rank-value {
        font-weight: 700;
        font-size: 20px;
        color: var(--primary);
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    if (!getToken()) { window.location.href = '/'; return; }

    const user = initUserUI();
    if (!user) return;
    if (user.role !== 'admin' && user.role !== 'petugas') {
        window.location.href = '/dashboard';
        return;
    }

    let allData = [];

    // Set default dates — rentang 60 hari supaya data dummy pasti masuk
    const today = new Date();
    const past60 = new Date(today);
    past60.setDate(today.getDate() - 60);

    const fmt = d => d.toISOString().split('T')[0];
    document.getElementById('tanggal_akhir').value = fmt(today);
    document.getElementById('tanggal_awal').value  = fmt(past60);

    // Helper: ambil nama alat dari detail_pinjam (struktur API)
    function getAlatNames(p) {
        if (!p.detail_pinjam || !p.detail_pinjam.length) return '-';
        return p.detail_pinjam.map(d => d.alat?.nama_alat || '-').join(', ');
    }

    async function loadLaporan() {
        try {
            const response = await fetch(`${API_URL}/peminjaman`, {
                headers: { 'Authorization': `Bearer ${getToken()}`, 'Accept': 'application/json' }
            });
            const data = await response.json();

            if (data.success) {
                allData = data.data;
                filterLaporan();
            } else {
                console.error('API error:', data.message);
            }
        } catch (error) {
            console.error('Fetch error:', error);
        }
    }

    function filterLaporan() {
        const awal  = document.getElementById('tanggal_awal').value;
        const akhir = document.getElementById('tanggal_akhir').value;

        let filtered = allData;
        if (awal && akhir) {
            filtered = allData.filter(p => {
                if (!p.tanggal_pinjam) return false;
                return p.tanggal_pinjam >= awal && p.tanggal_pinjam <= akhir;
            });
        }

        updateStats(filtered);
        updateCharts(filtered);
        renderTable(filtered);
    }

    function updateStats(data) {
        const total = data.length;
        const selesai = data.filter(p => p.status === 'dikembalikan').length;
        const proses = data.filter(p => p.status === 'dipinjam').length;
        const terlambat = data.filter(p => p.status === 'terlambat').length;

        document.getElementById('totalTransaksi').textContent = total;
        document.getElementById('totalSelesai').textContent = selesai;
        document.getElementById('totalProses').textContent = proses;
        document.getElementById('totalTerlambat').textContent = terlambat;
    }

    function updateCharts(data) {
        // ─── Alat Terpopuler — hitung dari detail_pinjam[].alat.nama_alat ───
        const alatCount = {};
        data.forEach(p => {
            (p.detail_pinjam || []).forEach(d => {
                const nama = d.alat?.nama_alat || 'Unknown';
                alatCount[nama] = (alatCount[nama] || 0) + (d.jumlah_pinjam || 1);
            });
        });

        const topAlat = Object.entries(alatCount)
            .sort((a, b) => b[1] - a[1])
            .slice(0, 5);

        document.getElementById('popularAlat').innerHTML = topAlat.length > 0
            ? topAlat.map((item, index) => `
                <div class="rank-item">
                    <div class="rank-number">${index + 1}</div>
                    <div class="rank-content">
                        <div class="rank-name">${item[0]}</div>
                        <div class="rank-stat">${item[1]} unit dipinjam</div>
                    </div>
                    <div class="rank-value">${item[1]}</div>
                </div>`).join('')
            : '<div class="empty-state"><i class="fas fa-inbox"></i><p>Tidak ada data</p></div>';

        // ─── Peminjam Teraktif ───────────────────────────────────────────────
        const userCount = {};
        data.forEach(p => {
            const nama = p.user?.name || 'Unknown';
            userCount[nama] = (userCount[nama] || 0) + 1;
        });

        const topUsers = Object.entries(userCount)
            .sort((a, b) => b[1] - a[1])
            .slice(0, 5);

        document.getElementById('topUsers').innerHTML = topUsers.length > 0
            ? topUsers.map((item, index) => `
                <div class="rank-item">
                    <div class="rank-number">${index + 1}</div>
                    <div class="rank-content">
                        <div class="rank-name">${item[0]}</div>
                        <div class="rank-stat">${item[1]} peminjaman</div>
                    </div>
                    <div class="rank-value">${item[1]}</div>
                </div>`).join('')
            : '<div class="empty-state"><i class="fas fa-inbox"></i><p>Tidak ada data</p></div>';
    }

    function renderTable(data) {
        const tbody = document.getElementById('laporanTable');
        
        if (data.length === 0) {
            tbody.innerHTML = `
                <tr><td colspan="6" class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Tidak Ada Data</h3>
                    <p>Tidak ada transaksi pada periode ini</p>
                </td></tr>
            `;
            return;
        }

        tbody.innerHTML = data.map(p => {
            const tglPinjam = new Date(p.tanggal_pinjam);
            const tglKembali = new Date(p.tanggal_kembali);
            const durasi = Math.ceil((tglKembali - tglPinjam) / (1000 * 60 * 60 * 24));

            return `
                <tr>
                    <td><strong>#${p.id}</strong></td>
                    <td>${p.tanggal_pinjam || '-'}</td>
                    <td>
                        <div style="font-weight: 600;">${p.user?.name || '-'}</div>
                        <div style="font-size: 13px; color: var(--gray-500);">${p.user?.email || ''}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">${getAlatNames(p)}</div>
                        <div style="font-size: 13px; color: var(--gray-500);">
                            ${(p.detail_pinjam || []).map(d => d.alat?.kategori?.nama_kategori).filter(Boolean).filter((v,i,a)=>a.indexOf(v)===i).join(', ')}
                        </div>
                    </td>
                    <td>${durasi} hari</td>
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
            `;
        }).join('');
    }

    async function exportLaporan() {
        const awal  = document.getElementById('tanggal_awal').value;
        const akhir = document.getElementById('tanggal_akhir').value;

        if (!awal || !akhir) {
            showToast('Pilih periode tanggal terlebih dahulu!', 'warning');
            return;
        }

        // Disable tombol & tampilkan loading
        const btn = document.querySelector('button[onclick="exportLaporan()"]');
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Membuat file...</span>';

        try {
            const url = `${API_URL}/laporan/export-peminjaman?tanggal_mulai=${awal}&tanggal_selesai=${akhir}`;

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${getToken()}`,
                    'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                },
            });

            if (!response.ok) {
                const err = await response.json().catch(() => ({}));
                showToast(err.message || 'Gagal mengunduh laporan!', 'danger');
                return;
            }

            // Ambil blob dan trigger download
            const blob     = await response.blob();
            const objectUrl = URL.createObjectURL(blob);
            const link      = document.createElement('a');
            link.href        = objectUrl;
            link.download    = `laporan_peminjaman_${awal}_sd_${akhir}.xlsx`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(objectUrl);

            showToast('File Excel berhasil diunduh!', 'success');
        } catch (error) {
            console.error('Export error:', error);
            showToast('Terjadi kesalahan saat mengunduh file!', 'danger');
        } finally {
            btn.disabled  = false;
            btn.innerHTML = originalHTML;
        }
    }

    function exportLaporanPDF() {
        const awal  = document.getElementById('tanggal_awal').value;
        const akhir = document.getElementById('tanggal_akhir').value;

        if (!awal || !akhir) {
            showToast('Pilih periode tanggal terlebih dahulu!', 'warning');
            return;
        }

        let filtered = allData;
        if (awal && akhir) {
            filtered = allData.filter(p => {
                const tgl = new Date(p.tanggal_pinjam);
                return tgl >= new Date(awal) && tgl <= new Date(akhir);
            });
        }

        if (filtered.length === 0) {
            showToast('Tidak ada data untuk dicetak!', 'warning');
            return;
        }

        const selesai   = filtered.filter(p => p.status === 'dikembalikan').length;
        const proses    = filtered.filter(p => p.status === 'dipinjam').length;
        const terlambat = filtered.filter(p => p.status === 'terlambat').length;

        const rows = filtered.map((p, i) => {
            let durasi = '-';
            if (p.tanggal_pinjam && p.tanggal_kembali) {
                durasi = Math.ceil((new Date(p.tanggal_kembali) - new Date(p.tanggal_pinjam)) / (1000 * 60 * 60 * 24)) + ' hari';
            }
            const statusColor = p.status === 'dikembalikan' ? '#10b981' : p.status === 'terlambat' ? '#ef4444' : '#f59e0b';
            return `
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 10px 12px;">${i + 1}</td>
                    <td style="padding: 10px 12px;">${p.tanggal_pinjam || '-'}</td>
                    <td style="padding: 10px 12px;">${p.tanggal_kembali || '-'}</td>
                    <td style="padding: 10px 12px; font-weight: 600;">${p.user?.name || '-'}</td>
                    <td style="padding: 10px 12px;">${p.keperluan || '-'}</td>
                    <td style="padding: 10px 12px;">${durasi}</td>
                    <td style="padding: 10px 12px;"><span style="color: ${statusColor}; font-weight: 600;">${p.status}</span></td>
                </tr>
            `;
        }).join('');

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Laporan Peminjaman ${awal} s/d ${akhir}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 40px; color: #111; }
                    h1 { color: #4f46e5; margin-bottom: 4px; }
                    .subtitle { color: #6b7280; margin-bottom: 24px; font-size: 14px; }
                    .summary { display: flex; gap: 24px; margin-bottom: 28px; }
                    .sum-box { padding: 16px 24px; border-radius: 10px; min-width: 120px; text-align: center; }
                    .sum-box .val { font-size: 28px; font-weight: 700; }
                    .sum-box .lbl { font-size: 12px; margin-top: 4px; }
                    table { width: 100%; border-collapse: collapse; font-size: 13px; }
                    th { background: #4f46e5; color: white; padding: 12px; text-align: left; }
                    tr:nth-child(even) { background: #f9fafb; }
                    .footer { margin-top: 32px; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 16px; }
                    @media print { .no-print { display: none; } }
                </style>
            </head>
            <body>
                <h1>Laporan Peminjaman Alat</h1>
                <div class="subtitle">Periode: ${awal} s/d ${akhir} &nbsp;|&nbsp; Dicetak: ${new Date().toLocaleDateString('id-ID')}</div>
                <div class="summary">
                    <div class="sum-box" style="background:#ede9fe; color:#4f46e5;">
                        <div class="val">${filtered.length}</div><div class="lbl">Total Transaksi</div>
                    </div>
                    <div class="sum-box" style="background:#d1fae5; color:#065f46;">
                        <div class="val">${selesai}</div><div class="lbl">Selesai</div>
                    </div>
                    <div class="sum-box" style="background:#fef3c7; color:#92400e;">
                        <div class="val">${proses}</div><div class="lbl">Dalam Proses</div>
                    </div>
                    <div class="sum-box" style="background:#fee2e2; color:#991b1b;">
                        <div class="val">${terlambat}</div><div class="lbl">Terlambat</div>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali</th>
                            <th>Peminjam</th>
                            <th>Keperluan</th>
                            <th>Durasi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
                <div class="footer">Sistem Peminjaman Alat &mdash; Laporan dibuat otomatis</div>
                <script>window.onload = () => { window.print(); }<\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
    }

    // Expose ke window — dibutuhkan karena onclick di HTML memanggil dari luar IIFE
    window.filterLaporan     = filterLaporan;
    window.exportLaporan     = exportLaporan;
    window.exportLaporanPDF  = exportLaporanPDF;

    loadLaporan();
})();
</script>
@endpush
@endsection
