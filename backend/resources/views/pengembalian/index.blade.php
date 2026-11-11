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
            <div class="stat-card gradient-primary">
                <div class="stat-icon"><i class="fas fa-search"></i></div>
                <div class="stat-content">
                    <div class="stat-value" id="statMenungguCek">0</div>
                    <div class="stat-label">Menunggu Pengecekan</div>
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
                    <div class="stat-label">Sudah Diverifikasi</div>
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
                        <option value="menunggu_cek">Menunggu Pengecekan</option>
                        <option value="aktif">Belum Dikembalikan</option>
                        <option value="semua">Semua Data</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tabel -->
        <div class="card">
            <div class="card-header">
                <h3 id="tableTitle">
                    <i class="fas fa-search"></i> Menunggu Pengecekan Petugas
                </h3>
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
                                <th>Tgl Kembali</th>
                                <th>Status</th>
                                <th>Kondisi & Denda</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pengembalianTable">
                            <tr><td colspan="8" style="text-align:center;padding:40px;
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

{{-- ══════════════════════════════════════════════════════════════
     Modal Verifikasi Kondisi (Petugas / Admin)
     Dibuka saat alat sudah dikembalikan peminjam tapi belum dicek
══════════════════════════════════════════════════════════════ --}}
<div class="modal" id="verifikasiModal">
    <div class="modal-content" style="max-width:520px;">
        <div class="modal-header" style="
            background:linear-gradient(135deg,var(--primary),var(--secondary));
            padding:20px 24px;">
            <h3 style="color:white;font-size:18px;">
                <i class="fas fa-clipboard-check"></i> Verifikasi Kondisi Alat
            </h3>
            <button class="modal-close" onclick="tutupVerifikasi()"
                style="color:white;background:rgba(255,255,255,0.2);">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="verifikasiForm" style="padding:24px;">
            <input type="hidden" id="verifikasiPengembalianId">

            <!-- Ringkasan -->
            <div id="verifikasiRingkasan"
                 style="padding:14px;background:var(--gray-50);border-radius:10px;
                        margin-bottom:20px;font-size:14px;">
            </div>

            <!-- Kondisi alat -->
            <div class="form-group">
                <label for="verifikasiKondisi">
                    <i class="fas fa-clipboard-check"></i> Kondisi Alat Setelah Diperiksa
                </label>
                <select id="verifikasiKondisi" required onchange="onKondisiChange(this.value)">
                    <option value="">-- Pilih Kondisi --</option>
                    <option value="baik">✅ Baik — alat kembali dalam kondisi normal</option>
                    <option value="rusak">⚠️ Rusak — alat mengalami kerusakan</option>
                </select>
            </div>

            <!-- Denda (muncul jika rusak) -->
            <div id="verifikasiDendaGroup" style="display:none;">
                <div style="padding:12px 14px;background:#fee2e2;border:1px solid #fca5a5;
                             border-radius:10px;margin-bottom:14px;font-size:13px;color:#991b1b;">
                    <i class="fas fa-exclamation-triangle"></i>
                    Alat rusak — tentukan nominal denda yang harus dibayar peminjam.
                </div>
                <div class="form-group">
                    <label for="verifikasiDenda">
                        <i class="fas fa-money-bill-wave"></i> Nominal Denda (Rp)
                    </label>
                    <input type="number" id="verifikasiDenda"
                           min="0" step="1000" value="0"
                           placeholder="Contoh: 50000">
                </div>
            </div>

            <!-- Keterangan -->
            <div class="form-group">
                <label for="verifikasiKeterangan">
                    <i class="fas fa-sticky-note"></i> Keterangan Petugas
                    <span style="font-weight:400;color:var(--gray-500);"> (opsional)</span>
                </label>
                <textarea id="verifikasiKeterangan" rows="2"
                          placeholder="Catatan kondisi alat, kerusakan, dll."
                          style="resize:vertical;"></textarea>
            </div>

            <div style="display:flex;gap:12px;justify-content:flex-end;">
                <button type="button" class="btn btn-secondary"
                        onclick="tutupVerifikasi()"
                        style="width:auto;padding:12px 24px;">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="submit" class="btn btn-primary"
                        id="btnSubmitVerifikasi"
                        style="width:auto;padding:12px 24px;">
                    <i class="fas fa-check"></i> Simpan Verifikasi
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
    if (user.role === 'peminjam') { window.location.href = '/dashboard'; return; }

    /* ─── State ─────────────────────────────────────────────────────────── */
    let allPeminjaman = [];

    /* ─── Helpers ──────────────────────────────────────────────────────── */
    function namaAlat(p) {
        if (!p.detail_pinjam?.length) return '-';
        return p.detail_pinjam.map(d => d.alat?.nama_alat || '-').join(', ');
    }

    function buildAvatar(u) {
        const initials = (u?.name || 'U')[0].toUpperCase();
        const style = 'width:36px;height:36px;border-radius:50%;flex-shrink:0;overflow:hidden;' +
                      'background:linear-gradient(135deg,#667eea,#764ba2);' +
                      'display:flex;align-items:center;justify-content:center;' +
                      'color:white;font-weight:700;font-size:14px;';
        const fotoUrl = u?.foto || null;
        if (fotoUrl) {
            return `<div style="${style}">
                <img src="${fotoUrl}" style="width:100%;height:100%;object-fit:cover;"
                     onerror="this.parentElement.innerHTML='${initials}'">
            </div>`;
        }
        return `<div style="${style}">${initials}</div>`;
    }

    function statusBadge(p) {
        if (p.status === 'dikembalikan') {
            const cek = p.pengembalian?.status_pengecekan;
            if (cek === 'menunggu_pengecekan') {
                return `<span class="badge" style="background:rgba(99,102,241,0.12);color:var(--primary);">
                            <i class="fas fa-search"></i> Menunggu Cek</span>`;
            }
            return `<span class="badge badge-success">
                        <i class="fas fa-check-circle"></i> Selesai</span>`;
        }
        if (p.status === 'terlambat')
            return `<span class="badge badge-danger">
                        <i class="fas fa-exclamation-circle"></i> Terlambat</span>`;
        return `<span class="badge badge-warning">
                    <i class="fas fa-clock"></i> Dipinjam</span>`;
    }

    /* ─── Load data ─────────────────────────────────────────────────────── */
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
        const belumKembali  = allPeminjaman.filter(p =>
            p.status === 'dipinjam' || p.status === 'terlambat');
        const menungguCek   = allPeminjaman.filter(p =>
            p.pengembalian?.status_pengecekan === 'menunggu_pengecekan');
        const terlambat     = allPeminjaman.filter(p => p.status === 'terlambat');
        const sudahVerif    = allPeminjaman.filter(p =>
            p.pengembalian?.status_pengecekan === 'selesai');

        document.getElementById('statBelumKembali').textContent = belumKembali.length;
        document.getElementById('statMenungguCek').textContent  = menungguCek.length;
        document.getElementById('statTerlambat').textContent    = terlambat.length;
        document.getElementById('statSudahKembali').textContent = sudahVerif.length;
    }

    function applyFilter() {
        const q    = document.getElementById('searchInput').value.toLowerCase();
        const mode = document.getElementById('filterMode').value;

        let data = allPeminjaman;

        if (mode === 'menunggu_cek') {
            data = data.filter(p =>
                p.pengembalian?.status_pengecekan === 'menunggu_pengecekan');
        } else if (mode === 'aktif') {
            data = data.filter(p =>
                p.status === 'dipinjam' || p.status === 'terlambat');
        }

        if (q) {
            data = data.filter(p =>
                (p.user?.name  || '').toLowerCase().includes(q) ||
                namaAlat(p).toLowerCase().includes(q));
        }

        renderTable(data);
    }

    function renderTable(data) {
        const tbody = document.getElementById('pengembalianTable');

        if (!data.length) {
            tbody.innerHTML = `<tr><td colspan="8" class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h3>Tidak Ada Data</h3>
                <p>Tidak ada data yang sesuai filter</p>
            </td></tr>`;
            return;
        }

        tbody.innerHTML = data.map(p => {
            const today    = new Date(); today.setHours(0,0,0,0);
            const dl       = p.tanggal_harus_kembali ? new Date(p.tanggal_harus_kembali) : null;
            const isLate   = dl && dl < today && p.status !== 'dikembalikan';

            const sudahKembali    = p.status === 'dikembalikan';
            const menungguCek     = sudahKembali &&
                                    p.pengembalian?.status_pengecekan === 'menunggu_pengecekan';
            const sudahVerifikasi = sudahKembali &&
                                    p.pengembalian?.status_pengecekan === 'selesai';

            /* ── Kolom Kondisi & Denda ── */
            let kondisiHtml = `<span style="color:var(--gray-400);font-size:13px;">-</span>`;
            if (menungguCek) {
                kondisiHtml = `
                    <span class="badge" style="background:rgba(99,102,241,0.1);color:var(--primary);">
                        <i class="fas fa-hourglass-half"></i> Menunggu Cek
                    </span>`;
            } else if (sudahVerifikasi) {
                const kond = p.pengembalian.kondisi_alat;
                kondisiHtml = kond === 'rusak'
                    ? `<div>
                           <span class="badge badge-danger" style="margin-bottom:4px;">
                               <i class="fas fa-times-circle"></i> Rusak
                           </span>
                           ${p.pengembalian.denda > 0
                               ? `<div style="font-size:13px;font-weight:700;color:var(--danger);margin-top:2px;">
                                      <i class="fas fa-coins"></i>
                                      Rp ${Number(p.pengembalian.denda).toLocaleString('id-ID')}
                                  </div>`
                               : `<div style="font-size:12px;color:var(--warning);margin-top:2px;">
                                      <i class="fas fa-clock"></i> Denda belum ditetapkan
                                  </div>`}
                       </div>`
                    : `<span class="badge badge-success">
                           <i class="fas fa-check-circle"></i> Baik
                       </span>`;
            }

            /* ── Kolom Aksi ── */
            let aksiHtml = '';
            if (!sudahKembali) {
                aksiHtml = `<span style="color:var(--gray-400);font-size:12px;">
                                <i class="fas fa-hourglass-half"></i> Menunggu Peminjam
                            </span>`;
            } else if (menungguCek) {
                // ★ Tombol utama: Periksa Kondisi
                aksiHtml = `
                    <button onclick="bukaVerifikasi(${p.id})"
                            style="white-space:nowrap;padding:8px 16px;
                                   background:linear-gradient(135deg,var(--primary),var(--secondary));
                                   color:white;border:none;border-radius:8px;
                                   cursor:pointer;font-weight:600;font-size:13px;
                                   box-shadow:0 2px 8px rgba(99,102,241,0.3);">
                        <i class="fas fa-clipboard-check"></i> Periksa Kondisi
                    </button>`;
            } else if (sudahVerifikasi) {
                // Sudah selesai — tampilkan tanda selesai
                aksiHtml = `<span style="color:var(--success);font-size:13px;font-weight:600;">
                                <i class="fas fa-check-circle"></i> Selesai
                            </span>`;
            }

            return `<tr>
                <td><strong>#${p.id}</strong></td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        ${buildAvatar(p.user)}
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
                    ${(p.detail_pinjam?.length || 0) > 1
                        ? `<div style="font-size:12px;color:var(--gray-500);">
                               ${p.detail_pinjam.length} item
                           </div>` : ''}
                </td>
                <td style="white-space:nowrap;">${p.tanggal_pinjam || '-'}</td>
                <td style="white-space:nowrap;">
                    ${p.pengembalian?.tanggal_kembali
                        ? `<span style="color:var(--gray-900);font-weight:600;">
                               ${p.pengembalian.tanggal_kembali}
                           </span>`
                        : dl
                        ? `<span style="color:${isLate ? 'var(--danger)' : 'var(--gray-700)'};">
                               ${p.tanggal_harus_kembali}
                               ${isLate ? `<div style="font-size:11px;color:var(--danger);">
                                   <i class="fas fa-exclamation-triangle"></i> Terlambat
                               </div>` : ''}
                           </span>`
                        : '-'}
                </td>
                <td>${statusBadge(p)}</td>
                <td>${kondisiHtml}</td>
                <td>${aksiHtml}</td>
            </tr>`;
        }).join('');
    }

    function showError(msg) {
        document.getElementById('pengembalianTable').innerHTML =
            `<tr><td colspan="8" class="empty-state">
                <i class="fas fa-exclamation-circle"></i><h3>${msg}</h3>
            </td></tr>`;
    }

    /* ─── Filter events ─────────────────────────────────────────────────── */
    document.getElementById('searchInput').addEventListener('input', applyFilter);
    document.getElementById('filterMode').addEventListener('change', function() {
        const labels = {
            menunggu_cek : '<i class="fas fa-search"></i> Menunggu Pengecekan Petugas',
            aktif        : '<i class="fas fa-clock"></i> Belum Dikembalikan',
            semua        : '<i class="fas fa-list"></i> Semua Data',
        };
        document.getElementById('tableTitle').innerHTML =
            labels[this.value] || labels.semua;
        applyFilter();
    });

    /* ─── Modal Verifikasi ───────────────────────────────────────────────── */
    window.bukaVerifikasi = function(peminjamanId) {
        const p = allPeminjaman.find(x => x.id === peminjamanId);
        if (!p || !p.pengembalian) return;

        document.getElementById('verifikasiPengembalianId').value = p.pengembalian.id;

        // Ringkasan
        const rows = (p.detail_pinjam || []).map(d => `
            <div style="display:flex;justify-content:space-between;
                        padding:4px 0;border-bottom:1px solid var(--gray-200);">
                <span style="font-weight:600;">${d.alat?.nama_alat || '-'}</span>
                <span style="color:var(--gray-500);">${d.jumlah_pinjam} unit</span>
            </div>`).join('');

        document.getElementById('verifikasiRingkasan').innerHTML = `
            <div style="display:grid;gap:8px;">
                <div style="display:flex;justify-content:space-between;">
                    <div>
                        <div style="font-size:11px;color:var(--gray-500);">Peminjam</div>
                        <div style="font-weight:700;font-size:15px;">${p.user?.name || '-'}</div>
                        <div style="font-size:12px;color:var(--gray-500);">${p.user?.email || ''}</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:11px;color:var(--gray-500);">Tanggal Kembali</div>
                        <div style="font-weight:700;color:var(--primary);">
                            ${p.pengembalian.tanggal_kembali || '-'}
                        </div>
                    </div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--gray-500);margin-bottom:4px;">
                        Alat yang dikembalikan
                    </div>
                    ${rows || '-'}
                </div>
                ${p.pengembalian.keterangan ? `
                <div>
                    <div style="font-size:11px;color:var(--gray-500);">
                        Catatan dari peminjam
                    </div>
                    <div style="font-size:13px;color:var(--gray-700);font-style:italic;">
                        "${p.pengembalian.keterangan}"
                    </div>
                </div>` : ''}
            </div>`;

        // Reset form
        document.getElementById('verifikasiForm').reset();
        document.getElementById('verifikasiPengembalianId').value = p.pengembalian.id;
        document.getElementById('verifikasiDendaGroup').style.display = 'none';
        document.getElementById('verifikasiDenda').value = 0;

        document.getElementById('verifikasiModal').classList.add('active');
    };

    window.tutupVerifikasi = function() {
        document.getElementById('verifikasiModal').classList.remove('active');
    };

    window.onKondisiChange = function(val) {
        const rusak = val === 'rusak';
        document.getElementById('verifikasiDendaGroup').style.display =
            rusak ? 'block' : 'none';
        if (!rusak) document.getElementById('verifikasiDenda').value = 0;
    };

    /* ─── Submit Verifikasi ──────────────────────────────────────────────── */
    document.getElementById('verifikasiForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const pengembalianId = document.getElementById('verifikasiPengembalianId').value;
        const kondisi        = document.getElementById('verifikasiKondisi').value;
        const denda          = parseFloat(document.getElementById('verifikasiDenda').value) || 0;
        const keterangan     = document.getElementById('verifikasiKeterangan').value || null;

        if (!kondisi) { alert('Kondisi alat wajib dipilih'); return; }
        if (kondisi === 'rusak' && denda <= 0) {
            if (!confirm('Kondisi rusak tapi denda 0. Lanjutkan?')) return;
        }

        const btn = document.getElementById('btnSubmitVerifikasi');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

        try {
            const res  = await fetch(`${API_URL}/pengembalian/${pengembalianId}`, {
                method: 'PUT',
                headers: {
                    Authorization: `Bearer ${getToken()}`,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ kondisi_alat: kondisi, denda, keterangan }),
            });
            const data = await res.json();

            if (data.success) {
                tutupVerifikasi();
                await loadData();
                alert('Verifikasi berhasil disimpan!');
            } else {
                const msg = data.errors
                    ? Object.values(data.errors).flat().join('\n')
                    : (data.message || 'Gagal menyimpan verifikasi');
                alert(msg);
            }
        } catch (err) {
            console.error('verifikasi:', err);
            alert('Terjadi kesalahan koneksi');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> Simpan Verifikasi';
        }
    });

    /* ─── Init ──────────────────────────────────────────────────────────── */
    loadData();
})();
</script>
@endpush
@endsection
