@extends('layouts.app')

@section('title', 'Pengembalian - Sistem Peminjaman Alat')
@section('body-class', 'dashboard-page')

@section('content')
@include('layouts.sidebar')

<main class="main-content">
    @section('page-title', 'Pengembalian Alat')
    @include('layouts.topbar')

    <div class="content">

        <!-- Stats -->
        <div class="stats-grid" style="margin-bottom:32px;">
            <div class="stat-card gradient-warning">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-content">
                    <div class="stat-value" id="statBelumKembali">0</div>
                    <div class="stat-label">Belum Dikembalikan</div>
                </div>
            </div>
            <div class="stat-card gradient-danger">
                <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="stat-content">
                    <div class="stat-value" id="statTerlambat">0</div>
                    <div class="stat-label">Terlambat</div>
                </div>
            </div>
            <div class="stat-card gradient-success">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-content">
                    <div class="stat-value" id="statSudahKembali">0</div>
                    <div class="stat-label">Sudah Dikembalikan</div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="card">
            <div class="card-body">
                <div class="filters">
                    <div class="search-box" style="flex:1;">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput"
                               placeholder="Cari nama peminjam atau alat...">
                    </div>
                    <select class="filter-select" id="filterMode">
                        <option value="aktif">Belum Dikembalikan</option>
                        <option value="semua">Semua Data</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tabel -->
        <div class="card">
            <div class="card-header">
                <h3 id="tableTitle"><i class="fas fa-list"></i> Daftar Peminjaman Aktif</h3>
            </div>
            <div class="card-body no-padding">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Peminjam</th>
                                <th>Alat Dipinjam</th>
                                <th>Tgl Pinjam</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pengembalianTable">
                            <tr><td colspan="7" style="text-align:center;padding:40px;
                                                        color:var(--gray-500);">
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

<!-- ═══ Modal Konfirmasi Pengembalian ═══ -->
<div class="modal" id="kembalikanModal">
    <div class="modal-content" style="max-width:520px;">
        <div class="modal-header">
            <h3>Konfirmasi Pengembalian</h3>
            <button class="modal-close" onclick="closeKembalikanModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="kembalikanForm">
            <div class="modal-body">
                <input type="hidden" id="selectedPeminjamanId">

                <!-- Ringkasan peminjaman -->
                <div id="ringkasanPeminjaman"
                     style="padding:14px;background:var(--gray-50);
                            border-radius:10px;margin-bottom:20px;">
                </div>

                <!-- Tanggal kembali aktual — wajib -->
                <div class="form-group">
                    <label for="tanggal_kembali">
                        <i class="fas fa-calendar-check"></i> Tanggal Dikembalikan
                    </label>
                    <input type="date" id="tanggal_kembali" required>
                </div>

                <!-- Kondisi alat — in:baik,rusak sesuai API -->
                <div class="form-group">
                    <label for="kondisi_alat">
                        <i class="fas fa-clipboard-check"></i> Kondisi Alat
                    </label>
                    <select id="kondisi_alat" required>
                        <option value="">-- Pilih Kondisi --</option>
                        <option value="baik">Baik — alat kembali dalam kondisi normal</option>
                        <option value="rusak">Rusak — alat mengalami kerusakan</option>
                    </select>
                </div>

                <!-- Denda — muncul saat kondisi rusak atau terlambat -->
                <div class="form-group" id="dendaGroup" style="display:none;">
                    <label for="denda">
                        <i class="fas fa-money-bill-wave"></i> Denda (Rp)
                    </label>
                    <input type="number" id="denda" min="0" step="1000"
                           value="0" placeholder="0">
                    <div style="font-size:12px;color:var(--gray-500);margin-top:4px;">
                        Isi nominal denda jika ada
                    </div>
                </div>

                <!-- Keterangan opsional -->
                <div class="form-group">
                    <label for="keterangan">
                        <i class="fas fa-sticky-note"></i> Keterangan
                        <span style="font-weight:400;color:var(--gray-500);"> (opsional)</span>
                    </label>
                    <textarea id="keterangan" rows="3"
                              placeholder="Catatan kondisi alat, kerusakan, dll."
                              style="resize:vertical;"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        onclick="closeKembalikanModal()"
                        style="width:auto;padding:12px 24px;">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="submit" class="btn btn-success"
                        id="submitKembalikanBtn"
                        style="width:auto;padding:12px 24px;
                               background:var(--success);color:white;">
                    <i class="fas fa-check"></i> Konfirmasi Pengembalian
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    if (!getToken()) { window.location.href = '/'; return; }
    const user = initUserUI();
    if (!user) return;
    // Halaman ini hanya untuk admin & petugas
    if (user.role === 'peminjam') { window.location.href = '/dashboard'; return; }

    /* ─── State ─────────────────────────────────────────────────────────── */
    let allPeminjaman = [];

    /* ─── Helpers ──────────────────────────────────────────────────────── */
    function namaAlat(p) {
        if (!p.detail_pinjam || !p.detail_pinjam.length) return '-';
        return p.detail_pinjam.map(d => d.alat?.nama_alat || '-').join(', ');
    }

    function statusBadge(p) {
        if (p.status === 'dikembalikan')
            return `<span class="badge badge-success">
                        <i class="fas fa-check-circle"></i> Dikembalikan</span>`;
        if (p.status === 'terlambat')
            return `<span class="badge badge-danger">
                        <i class="fas fa-exclamation-circle"></i> Terlambat</span>`;
        return `<span class="badge badge-warning">
                    <i class="fas fa-clock"></i> Dipinjam</span>`;
    }

    /* ─── Load data dari /api/peminjaman ──────────────────────────────── */
    // Pengembalian dibuat dari konteks peminjaman yang aktif,
    // bukan dari /api/pengembalian (yang hanya list record pengembalian)
    async function loadData() {
        try {
            const res  = await fetch(`${API_URL}/peminjaman`, {
                headers: { Authorization: `Bearer ${getToken()}` }
            });
            const data = await res.json();
            if (data.success) {
                allPeminjaman = data.data;
                updateStats();
                applyFilter();
            } else {
                showError('Gagal memuat data');
            }
        } catch (e) {
            console.error('loadData:', e);
            showError('Tidak dapat terhubung ke server');
        }
    }

    function updateStats() {
        const aktif    = allPeminjaman.filter(p =>
            p.status === 'dipinjam' || p.status === 'terlambat');
        const terlambat = allPeminjaman.filter(p => p.status === 'terlambat');
        const kembali   = allPeminjaman.filter(p => p.status === 'dikembalikan');
        document.getElementById('statBelumKembali').textContent = aktif.length;
        document.getElementById('statTerlambat').textContent    = terlambat.length;
        document.getElementById('statSudahKembali').textContent = kembali.length;
    }

    function applyFilter() {
        const q    = document.getElementById('searchInput').value.toLowerCase();
        const mode = document.getElementById('filterMode').value;

        let data = allPeminjaman;

        // Filter mode
        if (mode === 'aktif') {
            data = data.filter(p =>
                p.status === 'dipinjam' || p.status === 'terlambat');
        }

        // Filter search
        if (q) {
            data = data.filter(p =>
                (p.user?.name || '').toLowerCase().includes(q) ||
                namaAlat(p).toLowerCase().includes(q));
        }

        renderTable(data);
    }

    function renderTable(data) {
        const tbody = document.getElementById('pengembalianTable');

        if (!data.length) {
            tbody.innerHTML = `<tr><td colspan="7" class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h3>Tidak Ada Data</h3>
                <p>Semua alat sudah dikembalikan</p>
            </td></tr>`;
            return;
        }

        tbody.innerHTML = data.map(p => {
            // Cek keterlambatan: deadline ada dan sudah lewat, belum dikembalikan
            const deadline    = p.tanggal_kembali;   // deadline, bukan tgl aktual kembali
            const isLate      = deadline
                && new Date(deadline) < new Date()
                && p.status !== 'dikembalikan';

            // Tombol kembalikan: hanya jika belum dikembalikan
            // p.pengembalian berisi data jika sudah ada record pengembalian
            const sudahKembali = !!p.pengembalian || p.status === 'dikembalikan';

            return `<tr>
                <td><strong>#${p.id}</strong></td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:36px;height:36px;border-radius:50%;
                                    background:linear-gradient(135deg,#667eea,#764ba2);
                                    display:flex;align-items:center;justify-content:center;
                                    color:white;font-weight:700;font-size:14px;">
                            ${(p.user?.name || 'U')[0].toUpperCase()}
                        </div>
                        <div>
                            <div style="font-weight:600;">${p.user?.name || '-'}</div>
                            <div style="font-size:12px;color:var(--gray-500);">
                                ${p.user?.email || ''}
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div style="font-weight:600;">${namaAlat(p)}</div>
                    ${p.detail_pinjam?.length > 1
                        ? `<div style="font-size:12px;color:var(--gray-500);">
                               ${p.detail_pinjam.length} item
                           </div>` : ''}
                </td>
                <td style="white-space:nowrap;">${p.tanggal_pinjam || '-'}</td>
                <td>
                    <div style="font-weight:600;
                                color:${isLate ? 'var(--danger)' : 'var(--gray-900)'};">
                        ${deadline || '-'}
                    </div>
                    ${isLate ? `<div style="font-size:11px;color:var(--danger);">
                                    <i class="fas fa-exclamation-triangle"></i> Terlambat
                                </div>` : ''}
                </td>
                <td>${statusBadge(p)}</td>
                <td>
                    ${!sudahKembali
                        ? `<button class="btn btn-success btn-sm"
                                   onclick="bukaModalKembalikan(${p.id})"
                                   style="white-space:nowrap;padding:8px 14px;
                                          background:var(--success);color:white;
                                          border:none;border-radius:8px;cursor:pointer;
                                          font-weight:600;font-size:13px;">
                               <i class="fas fa-undo-alt"></i> Kembalikan
                           </button>`
                        : `<span style="color:var(--gray-400);font-size:13px;">
                               <i class="fas fa-check"></i> Selesai
                           </span>`}
                </td>
            </tr>`;
        }).join('');
    }

    function showError(msg) {
        document.getElementById('pengembalianTable').innerHTML =
            `<tr><td colspan="7" class="empty-state">
                <i class="fas fa-exclamation-circle"></i><h3>${msg}</h3>
            </td></tr>`;
    }

    /* ─── Filter events ─────────────────────────────────────────────────── */
    document.getElementById('searchInput').addEventListener('input',  applyFilter);
    document.getElementById('filterMode').addEventListener('change', () => {
        const mode = document.getElementById('filterMode').value;
        document.getElementById('tableTitle').innerHTML =
            mode === 'aktif'
                ? '<i class="fas fa-list"></i> Daftar Peminjaman Aktif'
                : '<i class="fas fa-list"></i> Semua Peminjaman';
        applyFilter();
    });

    /* ─── Kondisi → tampil denda ─────────────────────────────────────────── */
    document.getElementById('kondisi_alat').addEventListener('change', function () {
        const rusak = this.value === 'rusak';
        document.getElementById('dendaGroup').style.display = rusak ? 'block' : 'none';
        if (!rusak) document.getElementById('denda').value = 0;
    });

    /* ─── Modal kembalikan ───────────────────────────────────────────────── */
    function bukaModalKembalikan(id) {
        const p = allPeminjaman.find(x => x.id === id);
        if (!p) return;

        document.getElementById('selectedPeminjamanId').value = id;

        // Ringkasan peminjaman
        const rows = (p.detail_pinjam || []).map(d => `
            <div style="display:flex;justify-content:space-between;
                        padding:4px 0;border-bottom:1px solid var(--gray-200);">
                <span style="font-weight:600;">${d.alat?.nama_alat || '-'}</span>
                <span style="color:var(--gray-500);">${d.jumlah_pinjam} unit</span>
            </div>`).join('');

        document.getElementById('ringkasanPeminjaman').innerHTML = `
            <div style="display:grid;gap:8px;">
                <div>
                    <div style="font-size:11px;color:var(--gray-500);">Peminjam</div>
                    <div style="font-weight:600;">${p.user?.name || '-'}</div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--gray-500);
                                margin-bottom:4px;">Alat Dipinjam</div>
                    ${rows || '-'}
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div>
                        <div style="font-size:11px;color:var(--gray-500);">Tgl Pinjam</div>
                        <div style="font-weight:600;">${p.tanggal_pinjam || '-'}</div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:var(--gray-500);">Deadline</div>
                        <div style="font-weight:600;
                                    color:${p.status === 'terlambat'
                                            ? 'var(--danger)' : 'var(--gray-900)'};">
                            ${p.tanggal_kembali || '-'}
                        </div>
                    </div>
                </div>
            </div>`;

        // Reset form, set tanggal kembali = hari ini
        document.getElementById('kembalikanForm').reset();
        document.getElementById('selectedPeminjamanId').value = id;
        document.getElementById('tanggal_kembali').value =
            new Date().toISOString().split('T')[0];
        document.getElementById('dendaGroup').style.display = 'none';

        document.getElementById('kembalikanModal').classList.add('active');
    }

    function closeKembalikanModal() {
        document.getElementById('kembalikanModal').classList.remove('active');
    }

    /* ─── Submit pengembalian ────────────────────────────────────────────── */
    document.getElementById('kembalikanForm').addEventListener('submit', async e => {
        e.preventDefault();

        const peminjamanId   = document.getElementById('selectedPeminjamanId').value;
        const tanggalKembali = document.getElementById('tanggal_kembali').value;
        const kondisiAlat    = document.getElementById('kondisi_alat').value;
        const keterangan     = document.getElementById('keterangan').value || null;
        const denda          = parseFloat(document.getElementById('denda').value) || 0;

        if (!tanggalKembali) { alert('Tanggal kembali wajib diisi'); return; }
        if (!kondisiAlat)    { alert('Kondisi alat wajib dipilih');   return; }

        // Payload sesuai StorePengembalianRequest:
        // peminjaman_id (required|exists:peminjaman)
        // tanggal_kembali (required|date)
        // kondisi_alat (required|in:baik,rusak)
        // keterangan (nullable|string)
        // denda (nullable|numeric|min:0)
        const payload = {
            peminjaman_id:  parseInt(peminjamanId),
            tanggal_kembali: tanggalKembali,
            kondisi_alat:   kondisiAlat,
            keterangan:     keterangan,
            denda:          kondisiAlat === 'rusak' ? denda : 0,
        };

        const btn = document.getElementById('submitKembalikanBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

        try {
            const res  = await fetch(`${API_URL}/pengembalian`, {
                method: 'POST',
                headers: {
                    Authorization: `Bearer ${getToken()}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            if (data.success) {
                closeKembalikanModal();
                await loadData();   // refresh tabel dan stats
                alert('Pengembalian berhasil dicatat!');
            } else {
                const msg = data.errors
                    ? Object.values(data.errors).flat().join('\n')
                    : (data.message || 'Gagal mencatat pengembalian');
                alert(msg);
            }
        } catch (e) {
            console.error('submit pengembalian:', e);
            alert('Terjadi kesalahan koneksi');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> Konfirmasi Pengembalian';
        }
    });

    /* ─── Expose ke window (untuk onclick inline di template string JS) ─── */
    window.bukaModalKembalikan = bukaModalKembalikan;
    window.closeKembalikanModal = closeKembalikanModal;

    /* ─── Init ──────────────────────────────────────────────────────────── */
    loadData();
})();
</script>
@endpush
@endsection
