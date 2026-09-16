@extends('layouts.app')

@section('title', 'Peminjaman - Sistem Peminjaman Alat')
@section('body-class', 'dashboard-page')

@section('content')
@include('layouts.sidebar')

<main class="main-content">
    @section('page-title', 'Peminjaman Alat')
    @include('layouts.topbar')

    <div class="content">

        <!-- Stats -->
        <div class="stats-grid" style="margin-bottom:32px;">
            <div class="stat-card gradient-primary">
                <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
                <div class="stat-content">
                    <div class="stat-value" id="statTotal">0</div>
                    <div class="stat-label">Total Peminjaman</div>
                </div>
            </div>
            <div class="stat-card gradient-warning">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-content">
                    <div class="stat-value" id="statDipinjam">0</div>
                    <div class="stat-label">Sedang Dipinjam</div>
                </div>
            </div>
            <div class="stat-card gradient-success">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-content">
                    <div class="stat-value" id="statKembali">0</div>
                    <div class="stat-label">Sudah Kembali</div>
                </div>
            </div>
            <div class="stat-card gradient-danger">
                <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="stat-content">
                    <div class="stat-value" id="statTerlambat">0</div>
                    <div class="stat-label">Terlambat</div>
                </div>
            </div>
        </div>

        <!-- Filter & Aksi -->
        <div class="card">
            <div class="card-body">
                <div class="filters">
                    <div class="search-box" style="flex:1;">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Cari nama peminjam atau alat...">
                    </div>
                    <select class="filter-select" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="menunggu">Menunggu Persetujuan</option>
                        <option value="dipinjam">Dipinjam</option>
                        <option value="terlambat">Terlambat</option>
                        <option value="dikembalikan">Dikembalikan</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                    <button class="btn btn-primary" onclick="showAddModal()"
                            style="width:auto; padding:12px 24px;">
                        <i class="fas fa-plus"></i>
                        <span>Pinjam Alat</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabel -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> Daftar Peminjaman</h3>
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
                                <th>Keperluan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="peminjamanTable">
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

<!-- ═══ Modal Tambah Peminjaman ═══ -->
<div class="modal" id="peminjamanModal">
    <div class="modal-content" style="max-width:560px;">
        <div class="modal-header">
            <h3>Pinjam Alat</h3>
            <button class="modal-close" onclick="closeModal('peminjamanModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="peminjamanForm">
            <div class="modal-body">

                <!-- Pilih Alat — Shopee-style card grid -->
                <div class="form-group" style="margin-bottom:0;">
                    <label><i class="fas fa-box"></i> Pilih Alat</label>

                    <!-- Search bar -->
                    <div style="position:relative;margin-bottom:12px;">
                        <i class="fas fa-search"
                           style="position:absolute;left:12px;top:50%;transform:translateY(-50%);
                                  color:var(--gray-400);font-size:14px;"></i>
                        <input type="text" id="alatSearch"
                               placeholder="Cari nama alat, merk, atau kategori..."
                               autocomplete="off"
                               oninput="filterDropdownAlat()"
                               style="width:100%;padding:11px 14px 11px 38px;
                                      border:2px solid var(--gray-200);border-radius:12px;
                                      font-size:14px;outline:none;box-sizing:border-box;"
                               onfocus="this.style.borderColor='var(--primary)'"
                               onblur="this.style.borderColor='var(--gray-200)'">
                    </div>

                    <!-- Grid kartu alat (Shopee style) -->
                    <div id="alatGrid"
                         style="display:grid;grid-template-columns:repeat(3,1fr);
                                gap:10px;max-height:300px;overflow-y:auto;
                                padding:2px 2px 4px;margin-bottom:12px;">
                        <!-- diisi JS -->
                    </div>

                    <!-- Alat terpilih + jumlah + tambah -->
                    <div id="selectedAlatWrap" style="display:none;">
                        <div style="display:flex;align-items:center;gap:8px;
                                    padding:10px 14px;background:var(--gray-50);
                                    border:2px solid var(--primary);border-radius:12px;">
                            <img id="selectedAlatFoto" src="" alt=""
                                 style="width:40px;height:40px;border-radius:8px;
                                        object-fit:cover;flex-shrink:0;">
                            <div style="flex:1;min-width:0;">
                                <div id="selectedAlatNama"
                                     style="font-weight:600;font-size:14px;
                                            white-space:nowrap;overflow:hidden;
                                            text-overflow:ellipsis;"></div>
                                <div id="selectedAlatMeta"
                                     style="font-size:12px;color:var(--gray-500);"></div>
                            </div>
                            <button type="button" onclick="resetAlatSearch()"
                                    style="background:none;border:none;cursor:pointer;
                                           color:var(--gray-400);padding:4px;font-size:16px;"
                                    title="Ganti alat">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <!-- Jumlah + Tambah -->
                        <div style="display:flex;gap:8px;align-items:center;margin-top:10px;">
                            <label style="font-size:13px;color:var(--gray-600);
                                          white-space:nowrap;font-weight:500;">
                                Jumlah:
                            </label>
                            <!-- Tombol −/+ -->
                            <div style="display:flex;align-items:center;gap:0;
                                        border:2px solid var(--gray-200);border-radius:10px;
                                        overflow:hidden;">
                                <button type="button" onclick="ubahJumlah(-1)"
                                        style="background:var(--gray-100);border:none;
                                               padding:8px 14px;cursor:pointer;font-size:16px;
                                               font-weight:700;color:var(--gray-700);">−</button>
                                <input type="number" id="jumlah_pinjam" min="1" value="1"
                                       style="width:54px;border:none;text-align:center;
                                              font-size:15px;font-weight:600;
                                              padding:8px 0;outline:none;">
                                <button type="button" onclick="ubahJumlah(1)"
                                        style="background:var(--gray-100);border:none;
                                               padding:8px 14px;cursor:pointer;font-size:16px;
                                               font-weight:700;color:var(--gray-700);">+</button>
                            </div>
                            <span id="stokInfo"
                                  style="font-size:12px;color:var(--gray-500);"></span>
                            <button type="button" class="btn btn-primary"
                                    onclick="tambahItem()"
                                    style="width:auto;padding:10px 20px;margin-left:auto;">
                                <i class="fas fa-cart-plus"></i> Tambah
                            </button>
                        </div>
                    </div>

                    <!-- Hidden id terpilih -->
                    <input type="hidden" id="alat_id">
                </div>

                <!-- Keranjang -->
                <div id="cartWrap" style="display:none;margin-bottom:16px;">
                    <label style="font-size:13px;font-weight:600;color:var(--gray-700);
                                  margin-bottom:8px;display:block;">
                        <i class="fas fa-shopping-cart"></i> Alat yang akan dipinjam:
                    </label>
                    <div id="cartBody"></div>
                </div>

                <!-- Tanggal Pinjam -->
                <div class="form-group">
                    <label for="tanggal_pinjam">
                        <i class="fas fa-calendar-alt"></i> Tanggal Pinjam
                    </label>
                    <input type="date" id="tanggal_pinjam" required>
                </div>

                <!-- Keperluan -->
                <div class="form-group">
                    <label for="keperluan">
                        <i class="fas fa-align-left"></i> Keperluan
                        <span style="font-weight:400;color:var(--gray-500);"> (opsional)</span>
                    </label>
                    <textarea id="keperluan" rows="3"
                              placeholder="Untuk keperluan apa?"
                              style="resize:vertical;"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        onclick="closeModal('peminjamanModal')"
                        style="width:auto;padding:12px 24px;">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="submit" class="btn btn-primary" id="submitPeminjamanBtn"
                        style="width:auto;padding:12px 24px;">
                    <i class="fas fa-check"></i> Ajukan Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ═══ Modal Detail Peminjaman ═══ -->
<div class="modal" id="detailModal">
    <div class="modal-content" style="max-width:540px;">
        <div class="modal-header">
            <h3>Detail Peminjaman</h3>
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

<!-- ═══ Modal Kembalikan Alat (untuk Peminjam) ═══ -->
<div class="modal" id="kembalikanPeminjamModal">
    <div class="modal-content" style="max-width:480px;">
        <div class="modal-header">
            <h3><i class="fas fa-undo-alt"></i> Kembalikan Alat</h3>
            <button class="modal-close" onclick="closeModal('kembalikanPeminjamModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="kembalikanPeminjamForm">
            <div class="modal-body">
                <input type="hidden" id="kembalikanPeminjamanId">

                <!-- Ringkasan -->
                <div id="kembalikanRingkasan"
                     style="padding:14px;background:var(--gray-50);border-radius:10px;
                            margin-bottom:18px;font-size:14px;">
                </div>

                <!-- Tanggal kembali -->
                <div class="form-group">
                    <label for="kembalikanTanggal">
                        <i class="fas fa-calendar-check"></i> Tanggal Dikembalikan
                    </label>
                    <input type="date" id="kembalikanTanggal" required>
                </div>

                <!-- Kondisi alat -->
                <div class="form-group">
                    <label for="kembalikanKondisi">
                        <i class="fas fa-clipboard-check"></i> Kondisi Alat Saat Dikembalikan
                    </label>
                    <select id="kembalikanKondisi" required>
                        <option value="">-- Pilih Kondisi --</option>
                        <option value="baik">Baik — alat dalam kondisi normal</option>
                        <option value="rusak">Rusak — alat mengalami kerusakan</option>
                    </select>
                </div>

                <!-- Keterangan -->
                <div class="form-group">
                    <label for="kembalikanKeterangan">
                        <i class="fas fa-sticky-note"></i> Keterangan
                        <span style="font-weight:400;color:var(--gray-500);"> (opsional)</span>
                    </label>
                    <textarea id="kembalikanKeterangan" rows="2"
                              placeholder="Catatan kondisi alat jika ada..."
                              style="resize:vertical;"></textarea>
                </div>

                <div style="padding:12px;background:#fef3c7;border:1px solid #fcd34d;
                             border-radius:10px;font-size:13px;color:#92400e;">
                    <i class="fas fa-info-circle"></i>
                    Setelah mengajukan pengembalian, petugas akan memverifikasi kondisi alat.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        onclick="closeModal('kembalikanPeminjamModal')"
                        style="width:auto;padding:12px 24px;">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="submit" class="btn btn-success" id="submitKembalikanPeminjamBtn"
                        style="width:auto;padding:12px 24px;">
                    <i class="fas fa-undo-alt"></i> Ajukan Pengembalian
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
/* ── Keranjang ─────────────────────────────────────────────── */
.cart-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    margin-bottom: 6px;
    font-size: 14px;
}
.cart-item .cart-name { font-weight: 600; color: var(--gray-800); }
.cart-item .cart-qty  { color: var(--gray-500); font-size: 13px; margin-left: 6px; }
.cart-item .btn-remove {
    background: none; border: none; cursor: pointer;
    color: var(--danger); padding: 4px 6px; border-radius: 4px;
    transition: background 0.15s;
}
.cart-item .btn-remove:hover { background: #fee2e2; }

/* ── Shopee-style alat card ────────────────────────────────── */
.alat-card {
    border: 2px solid var(--gray-200);
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4,0,0.2,1);
    background: white;
    position: relative;
}
.alat-card:hover {
    border-color: var(--primary);
    box-shadow: 0 4px 16px rgba(99,102,241,0.18);
    transform: translateY(-2px);
}
.alat-card.selected {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99,102,241,0.2);
}
.alat-card.selected::after {
    content: '\f058';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    position: absolute;
    top: 6px; right: 8px;
    color: var(--primary);
    font-size: 16px;
    background: white;
    border-radius: 50%;
    line-height: 1;
}
.alat-card img {
    width: 100%;
    aspect-ratio: 1/1;
    object-fit: cover;
    display: block;
    background: var(--gray-100);
}
.alat-card .card-info {
    padding: 8px 8px 10px;
}
.alat-card .card-name {
    font-size: 12px;
    font-weight: 600;
    color: var(--gray-900);
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 4px;
}
.alat-card .card-stok {
    font-size: 11px;
    color: var(--gray-500);
    display: flex;
    align-items: center;
    gap: 3px;
}
.alat-card .card-stok.habis {
    color: var(--danger);
}
.alat-card.habis {
    opacity: 0.55;
    cursor: not-allowed;
}
.alat-card .badge-kat {
    font-size: 10px;
    background: var(--gray-100);
    color: var(--gray-600);
    padding: 2px 6px;
    border-radius: 20px;
    margin-bottom: 4px;
    display: inline-block;
}
/* Scrollbar tipis untuk grid */
#alatGrid::-webkit-scrollbar { width: 4px; }
#alatGrid::-webkit-scrollbar-track { background: transparent; }
#alatGrid::-webkit-scrollbar-thumb { background: var(--gray-300); border-radius: 4px; }
</style>
@endpush

@push('scripts')
<script>
(function () {
    if (!getToken()) { window.location.href = '/'; return; }
    const user = initUserUI();
    if (!user) return;

    /* ─── State ────────────────────────────────────────────────────────── */
    let allPeminjaman = [];
    let availableAlat = [];
    let cart = [];              // [{ alat_id, nama_alat, jumlah_pinjam, max }]

    /* ─── Helpers ─────────────────────────────────────────────────────── */
    function namaAlat(p) {
        if (!p.detail_pinjam || !p.detail_pinjam.length) return '-';
        return p.detail_pinjam.map(d => d.alat?.nama_alat || '-').join(', ');
    }

    function statusBadge(status) {
        const map = {
            menunggu:     ['primary', 'hourglass-half',    'Menunggu'],
            dipinjam:     ['warning', 'clock',             'Dipinjam'],
            dikembalikan: ['success', 'check-circle',      'Dikembalikan'],
            terlambat:    ['danger',  'exclamation-circle','Terlambat'],
            ditolak:      ['danger',  'times-circle',      'Ditolak'],
        };
        const [c, ic, label] = map[status] || ['primary', 'question', status];
        return `<span class="badge badge-${c}"><i class="fas fa-${ic}"></i> ${label}</span>`;
    }

    /* ─── Load alat tersedia ───────────────────────────────────────────── */
    async function loadAlat() {
        try {
            const res  = await fetch(`${API_URL}/alat`, {
                headers: { Authorization: `Bearer ${getToken()}` }
            });
            const data = await res.json();
            if (data.success) {
                // Hanya alat kondisi 'baik' dan stok > 0
                availableAlat = data.data.filter(a => a.kondisi === 'baik' && a.jumlah > 0);
                rebuildAlatSelect();
            }
        } catch (e) { console.error('loadAlat:', e); }
    }

    function rebuildAlatSelect() {
        const inCart  = cart.map(i => i.alat_id);
        window._alatList = availableAlat.filter(a => !inCart.includes(a.id));
        renderAlatGrid(window._alatList);
    }

    /* ─── Shopee-style grid ─────────────────────────────────────────────── */
    let selectedAlatId   = null;
    let selectedAlatData = null;

    function filterDropdownAlat() {
        const q = document.getElementById('alatSearch').value.toLowerCase();
        const inCart = cart.map(i => i.alat_id);
        const list = availableAlat
            .filter(a => !inCart.includes(a.id))
            .filter(a =>
                a.nama_alat.toLowerCase().includes(q) ||
                (a.merk || '').toLowerCase().includes(q) ||
                (a.kategori?.nama_kategori || '').toLowerCase().includes(q)
            );
        renderAlatGrid(list);
    }

    function renderAlatGrid(list) {
        const grid = document.getElementById('alatGrid');
        if (!list.length) {
            grid.innerHTML = `
                <div style="grid-column:1/-1;text-align:center;padding:32px 0;color:var(--gray-500);">
                    <i class="fas fa-search" style="font-size:28px;margin-bottom:8px;display:block;opacity:0.4;"></i>
                    Tidak ada alat yang cocok
                </div>`;
            return;
        }
        grid.innerHTML = list.map(a => {
            const imgSrc     = getAlatImageUrl(a);
            const kat        = a.kategori?.nama_kategori || '';
            const isSelected = a.id === selectedAlatId;
            const habis      = a.jumlah <= 0;
            return `
            <div class="alat-card${isSelected ? ' selected' : ''}${habis ? ' habis' : ''}"
                 onclick="${habis ? 'void(0)' : `pilihAlat(${a.id})`}"
                 title="${a.nama_alat}${a.merk ? ' — ' + a.merk : ''}">
                <img src="${imgSrc}" alt="${a.nama_alat}"
                     onerror="this.onerror=null;this.src='${getGradientFallback(a.id)}'">
                <div class="card-info">
                    ${kat ? `<span class="badge-kat">${kat}</span>` : ''}
                    <div class="card-name">${a.nama_alat}${a.merk ? `<span style="font-weight:400;"> ${a.merk}</span>` : ''}</div>
                    <div class="card-stok${habis ? ' habis' : ''}">
                        <i class="fas fa-cubes" style="font-size:9px;"></i>
                        ${habis ? 'Stok habis' : `Stok: ${a.jumlah}`}
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    function getAlatImageUrl(a) {
        if (a.foto) return a.foto;
        const nama = (a.nama_alat + ' ' + (a.kategori?.nama_kategori || '')).toLowerCase();
        const km = [
            [['laptop','notebook','macbook'],'laptop'],
            [['komputer','pc','desktop'],'computer'],
            [['printer'],'printer'],
            [['proyektor','projector','infocus'],'projector'],
            [['kamera','camera'],'camera'],
            [['tablet','ipad'],'tablet'],
            [['monitor','layar'],'monitor'],
            [['keyboard'],'keyboard'],
            [['mouse'],'computer+mouse'],
            [['speaker','audio'],'speaker'],
            [['router','wifi','switch','access'],'router'],
            [['handphone','smartphone'],'smartphone'],
            [['bor','drill'],'drill'],
            [['gergaji','saw'],'saw'],
            [['palu','hammer'],'hammer'],
            [['kunci','wrench'],'wrench'],
            [['raket','badminton'],'badminton'],
            [['bola','sepak','futsal'],'football'],
            [['basket','basketball'],'basketball'],
            [['sepeda','bicycle'],'bicycle'],
            [['gitar','guitar'],'guitar'],
            [['hardisk','ssd'],'hard+disk'],
            [['kabel','cable'],'cable'],
        ];
        let kw = encodeURIComponent(a.nama_alat.split(' ')[0]);
        for (const [keys,q] of km) {
            if (keys.some(k => nama.includes(k))) { kw = q; break; }
        }
        return `https://loremflickr.com/300/300/${kw}?lock=${a.id}`;
    }

    function getGradientFallback(id) {
        const cs = ['667eea,764ba2','f093fb,f5576c','4facfe,00f2fe','43e97b,38f9d7','fa709a,fee140'];
        const [c1,c2] = cs[id % cs.length].split(',');
        return `data:image/svg+xml,${encodeURIComponent(
            `<svg xmlns='http://www.w3.org/2000/svg' width='300' height='300'>
                <defs><linearGradient id='g' x1='0%' y1='0%' x2='100%' y2='100%'>
                    <stop offset='0%' style='stop-color:%23${c1}'/>
                    <stop offset='100%' style='stop-color:%23${c2}'/>
                </linearGradient></defs>
                <rect width='300' height='300' fill='url(%23g)'/>
                <text x='50%' y='54%' dominant-baseline='middle' text-anchor='middle' font-size='90' opacity='0.7'>🔧</text>
            </svg>`
        )}`;
    }

    function pilihAlat(id) {
        const alat = (window._alatList || availableAlat).find(a => a.id === id);
        if (!alat || alat.jumlah <= 0) return;
        selectedAlatId   = id;
        selectedAlatData = alat;
        document.getElementById('alat_id').value = id;

        // Tampilkan panel terpilih
        const foto = getAlatImageUrl(alat);
        const imgEl = document.getElementById('selectedAlatFoto');
        imgEl.src   = foto;
        imgEl.onerror = () => { imgEl.onerror = null; imgEl.src = getGradientFallback(alat.id); };
        document.getElementById('selectedAlatNama').textContent =
            alat.nama_alat + (alat.merk ? ` — ${alat.merk}` : '');
        document.getElementById('selectedAlatMeta').textContent =
            `${alat.kategori?.nama_kategori || ''} · Stok: ${alat.jumlah}`;
        document.getElementById('stokInfo').textContent = `maks ${alat.jumlah}`;
        document.getElementById('jumlah_pinjam').max   = alat.jumlah;
        document.getElementById('jumlah_pinjam').value = 1;
        document.getElementById('selectedAlatWrap').style.display = 'block';

        // Re-render grid untuk highlight card terpilih
        renderAlatGrid(window._alatList || []);
    }

    function ubahJumlah(delta) {
        const inp = document.getElementById('jumlah_pinjam');
        const max = parseInt(inp.max) || (selectedAlatData?.jumlah || 99);
        let val   = (parseInt(inp.value) || 1) + delta;
        inp.value = Math.max(1, Math.min(max, val));
    }

    function resetAlatSearch() {
        selectedAlatId   = null;
        selectedAlatData = null;
        document.getElementById('alat_id').value    = '';
        document.getElementById('alatSearch').value = '';
        document.getElementById('jumlah_pinjam').value = 1;
        document.getElementById('selectedAlatWrap').style.display = 'none';
        rebuildAlatSelect();
    }

    // Dummy agar tidak error jika ada sisa referensi lama
    function bukaDropdownAlat() {}
    function tutupDropdownAlat() {}

    /* ─── Keranjang ────────────────────────────────────────────────────── */
    function tambahItem() {
        const alatId = parseInt(document.getElementById('alat_id').value);
        const jumlah = parseInt(document.getElementById('jumlah_pinjam').value) || 1;

        if (!alatId) { alert('Pilih alat terlebih dahulu'); return; }

        const alat = availableAlat.find(a => a.id === alatId);
        if (!alat) return;

        if (jumlah < 1 || jumlah > alat.jumlah) {
            alert(`Jumlah tidak valid. Stok tersedia: ${alat.jumlah}`);
            return;
        }

        if (cart.find(i => i.alat_id === alatId)) {
            alert('Alat ini sudah ditambahkan. Hapus dulu untuk mengubah jumlah.');
            return;
        }

        cart.push({ alat_id: alatId, nama_alat: alat.nama_alat,
                    jumlah_pinjam: jumlah, max: alat.jumlah });
        resetAlatSearch();
        rebuildAlatSelect();
        renderCart();
    }

    function hapusItem(alatId) {
        cart = cart.filter(i => i.alat_id !== alatId);
        rebuildAlatSelect();
        renderCart();
    }

    function renderCart() {
        const wrap = document.getElementById('cartWrap');
        const body = document.getElementById('cartBody');
        if (!cart.length) { wrap.style.display = 'none'; body.innerHTML = ''; return; }

        wrap.style.display = 'block';
        body.innerHTML = cart.map(item => `
            <div class="cart-item">
                <div>
                    <span class="cart-name">${item.nama_alat}</span>
                    <span class="cart-qty">× ${item.jumlah_pinjam}</span>
                </div>
                <button type="button" class="btn-remove"
                        onclick="hapusItem(${item.alat_id})" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </div>`).join('');
    }

    /* ─── Load peminjaman ──────────────────────────────────────────────── */
    async function loadPeminjaman() {
        try {
            const res  = await fetch(`${API_URL}/peminjaman`, {
                headers: { Authorization: `Bearer ${getToken()}` }
            });
            const data = await res.json();
            if (data.success) {
                allPeminjaman = data.data;
                updateStats();
                renderTable(allPeminjaman);
            } else {
                showTableError('Gagal memuat data peminjaman');
            }
        } catch (e) {
            console.error('loadPeminjaman:', e);
            showTableError('Tidak dapat terhubung ke server');
        }
    }

    function updateStats() {
        document.getElementById('statTotal').textContent    = allPeminjaman.length;
        document.getElementById('statDipinjam').textContent = allPeminjaman.filter(p => p.status === 'dipinjam').length;
        document.getElementById('statKembali').textContent  = allPeminjaman.filter(p => p.status === 'dikembalikan').length;
        document.getElementById('statTerlambat').textContent = allPeminjaman.filter(p => p.status === 'terlambat').length;
    }

    function renderTable(data) {
        const tbody = document.getElementById('peminjamanTable');
        if (!data.length) {
            tbody.innerHTML = `<tr><td colspan="7" class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Belum Ada Peminjaman</h3>
                <p>Klik tombol "Pinjam Alat" untuk memulai</p>
            </td></tr>`;
            return;
        }

        tbody.innerHTML = data.map(p => {
            const sudahKembali = !!p.pengembalian || p.status === 'dikembalikan';
            // Admin bisa batalkan (hapus) peminjaman aktif
            const bolehBatalkan = !sudahKembali
                && (p.status === 'dipinjam' || p.status === 'menunggu')
                && user.role === 'admin';
            // Petugas & Admin bisa approve/tolak peminjaman yang masih menunggu
            const bolehApprove = p.status === 'menunggu'
                && (user.role === 'admin' || user.role === 'petugas');
            // Peminjam bisa ajukan pengembalian jika statusnya dipinjam
            const bolehKembalikan = !sudahKembali
                && p.status === 'dipinjam'
                && user.role === 'peminjam';

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
                            <div style="font-size:12px;color:var(--gray-500);">${p.user?.email || ''}</div>
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
                <td style="max-width:160px;font-size:13px;color:var(--gray-600);white-space:normal;">
                    ${p.keperluan || '-'}
                </td>
                <td>${statusBadge(p.status)}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-icon btn-view"
                                onclick="showDetail(${p.id})" title="Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${bolehApprove ? `
                        <button class="btn-icon" onclick="approvePeminjaman(${p.id})"
                                title="Setujui"
                                style="background:#d1fae5;color:#065f46;border:1px solid #6ee7b7;">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn-icon btn-delete" onclick="tolakPeminjaman(${p.id})"
                                title="Tolak">
                            <i class="fas fa-times"></i>
                        </button>` : ''}
                        ${bolehBatalkan ? `
                        <button class="btn-icon btn-delete"
                                onclick="batalkanPeminjaman(${p.id})"
                                title="Batalkan">
                            <i class="fas fa-ban"></i>
                        </button>` : ''}
                        ${bolehKembalikan ? `
                        <button class="btn btn-success btn-sm"
                                onclick="showModalKembalikan(${p.id})"
                                style="padding:6px 12px;font-size:12px;white-space:nowrap;
                                       background:var(--success);color:white;border:none;
                                       border-radius:8px;cursor:pointer;font-weight:600;">
                            <i class="fas fa-undo-alt"></i> Kembalikan
                        </button>` : ''}
                    </div>
                </td>
            </tr>`;
        }).join('');
    }

    function showTableError(msg) {
        document.getElementById('peminjamanTable').innerHTML =
            `<tr><td colspan="7" class="empty-state">
                <i class="fas fa-exclamation-circle"></i><h3>${msg}</h3>
            </td></tr>`;
    }

    /* ─── Filter ───────────────────────────────────────────────────────── */
    document.getElementById('searchInput').addEventListener('input',  applyFilter);
    document.getElementById('filterStatus').addEventListener('change', applyFilter);

    function applyFilter() {
        const q      = document.getElementById('searchInput').value.toLowerCase();
        const status = document.getElementById('filterStatus').value;
        renderTable(allPeminjaman.filter(p => {
            const matchQ = (p.user?.name || '').toLowerCase().includes(q)
                        || namaAlat(p).toLowerCase().includes(q);
            const matchS = !status || p.status === status;
            return matchQ && matchS;
        }));
    }

    /* ─── Modal helpers ────────────────────────────────────────────────── */
    function showAddModal() {
        // Guard: admin dan petugas tidak boleh buat peminjaman
        if (user.role !== 'peminjam') {
            alert('Hanya Peminjam yang dapat mengajukan peminjaman alat.');
            return;
        }
        cart = [];
        document.getElementById('peminjamanForm').reset();
        document.getElementById('tanggal_pinjam').value =
            new Date().toISOString().split('T')[0];
        renderCart();
        rebuildAlatSelect();
        resetAlatSearch();
        document.getElementById('peminjamanModal').classList.add('active');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }

    /* ─── Detail ───────────────────────────────────────────────────────── */
    function showDetail(id) {
        const p = allPeminjaman.find(x => x.id === id);
        if (!p) return;

        const rows = (p.detail_pinjam || []).map(d => `
            <div style="display:flex;justify-content:space-between;
                        padding:6px 0;border-bottom:1px solid var(--gray-200);">
                <span style="font-weight:600;">${d.alat?.nama_alat || '-'}</span>
                <span style="color:var(--gray-500);">${d.jumlah_pinjam} unit</span>
            </div>`).join('');

        document.getElementById('detailContent').innerHTML = `
            <div style="display:grid;gap:14px;">
                <div style="padding:14px;background:var(--gray-50);border-radius:10px;">
                    <div style="font-size:12px;color:var(--gray-500);margin-bottom:4px;">ID Peminjaman</div>
                    <div style="font-weight:700;font-size:18px;">#${p.id}</div>
                </div>
                <div style="padding:14px;background:var(--gray-50);border-radius:10px;">
                    <div style="font-size:12px;color:var(--gray-500);margin-bottom:4px;">Peminjam</div>
                    <div style="font-weight:600;">${p.user?.name || '-'}</div>
                    <div style="font-size:13px;color:var(--gray-500);">${p.user?.email || ''}</div>
                </div>
                <div style="padding:14px;background:var(--gray-50);border-radius:10px;">
                    <div style="font-size:12px;color:var(--gray-500);margin-bottom:8px;">Alat Dipinjam</div>
                    ${rows || '<span style="color:var(--gray-400)">-</span>'}
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div style="padding:14px;background:var(--gray-50);border-radius:10px;">
                        <div style="font-size:12px;color:var(--gray-500);margin-bottom:4px;">Tanggal Pinjam</div>
                        <div style="font-weight:600;">${p.tanggal_pinjam || '-'}</div>
                    </div>
                    <div style="padding:14px;background:var(--gray-50);border-radius:10px;">
                        <div style="font-size:12px;color:var(--gray-500);margin-bottom:4px;">Deadline Kembali</div>
                        <div style="font-weight:600;">${p.tanggal_kembali || '-'}</div>
                    </div>
                </div>
                <div style="padding:14px;background:var(--gray-50);border-radius:10px;">
                    <div style="font-size:12px;color:var(--gray-500);margin-bottom:4px;">Keperluan</div>
                    <div>${p.keperluan || '-'}</div>
                </div>
                <div style="padding:14px;background:var(--gray-50);border-radius:10px;">
                    <div style="font-size:12px;color:var(--gray-500);margin-bottom:8px;">Status</div>
                    ${statusBadge(p.status)}
                </div>
                ${p.pengembalian ? `
                <div style="padding:14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;">
                    <div style="font-size:12px;color:var(--gray-500);margin-bottom:8px;">Info Pengembalian</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:13px;">
                        <div><span style="color:var(--gray-500);">Tgl Kembali:</span>
                             <strong> ${p.pengembalian.tanggal_kembali || '-'}</strong></div>
                        <div><span style="color:var(--gray-500);">Kondisi:</span>
                             <strong> ${p.pengembalian.kondisi_alat || '-'}</strong></div>
                        ${p.pengembalian.denda > 0
                            ? `<div><span style="color:var(--danger);">Denda:</span>
                                    <strong style="color:var(--danger);">
                                        Rp ${Number(p.pengembalian.denda).toLocaleString('id-ID')}
                                    </strong></div>` : ''}
                        ${p.pengembalian.keterangan
                            ? `<div style="grid-column:1/-1;">
                                   <span style="color:var(--gray-500);">Keterangan:</span>
                                   ${p.pengembalian.keterangan}
                               </div>` : ''}
                    </div>
                </div>` : ''}
            </div>`;

        document.getElementById('detailModal').classList.add('active');
    }

    /* ─── Submit peminjaman ────────────────────────────────────────────── */
    document.getElementById('peminjamanForm').addEventListener('submit', async e => {
        e.preventDefault();

        if (!cart.length) {
            alert('Tambahkan minimal satu alat yang ingin dipinjam');
            return;
        }
        const tanggal = document.getElementById('tanggal_pinjam').value;
        if (!tanggal) { alert('Tanggal pinjam wajib diisi'); return; }

        // Payload sesuai StorePeminjamanRequest:
        // tanggal_pinjam, keperluan (nullable), detail[]{alat_id, jumlah_pinjam}
        const payload = {
            tanggal_pinjam: tanggal,
            keperluan: document.getElementById('keperluan').value || null,
            detail: cart.map(i => ({
                alat_id: i.alat_id,
                jumlah_pinjam: i.jumlah_pinjam
            }))
        };

        const btn = document.getElementById('submitPeminjamanBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

        try {
            const res  = await fetch(`${API_URL}/peminjaman`, {
                method: 'POST',
                headers: {
                    Authorization: `Bearer ${getToken()}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            if (data.success) {
                closeModal('peminjamanModal');
                await Promise.all([loadPeminjaman(), loadAlat()]);
                alert('Peminjaman berhasil diajukan!');
            } else {
                const msg = data.errors
                    ? Object.values(data.errors).flat().join('\n')
                    : (data.message || 'Gagal mengajukan peminjaman');
                alert(msg);
            }
        } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan koneksi');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> Ajukan Peminjaman';
        }
    });

    /* ─── Batalkan peminjaman (kembalikan stok) ────────────────────────── */
    async function batalkanPeminjaman(id) {
        const p = allPeminjaman.find(x => x.id === id);
        const namaList = p ? namaAlat(p) : `#${id}`;
        if (!confirm(`Batalkan peminjaman "${namaList}"?\nStok alat akan dikembalikan.`)) return;

        try {
            const res  = await fetch(`${API_URL}/peminjaman/${id}`, {
                method: 'DELETE',
                headers: { Authorization: `Bearer ${getToken()}` }
            });
            const data = await res.json();

            if (data.success) {
                await Promise.all([loadPeminjaman(), loadAlat()]);
                alert('Peminjaman berhasil dibatalkan!');
            } else {
                alert(data.message || 'Gagal membatalkan peminjaman');
            }
        } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan koneksi');
        }
    }

    /* ─── Peminjam: ajukan pengembalian ────────────────────────────────── */
    function showModalKembalikan(id) {
        const p = allPeminjaman.find(x => x.id === id);
        if (!p) return;

        document.getElementById('kembalikanPeminjamanId').value = id;

        // Isi ringkasan
        const rows = (p.detail_pinjam || []).map(d => `
            <div style="display:flex;justify-content:space-between;
                        padding:4px 0;border-bottom:1px solid var(--gray-200);">
                <span style="font-weight:600;">${d.alat?.nama_alat || '-'}</span>
                <span style="color:var(--gray-500);">${d.jumlah_pinjam} unit</span>
            </div>`).join('');

        document.getElementById('kembalikanRingkasan').innerHTML = `
            <div style="display:grid;gap:8px;">
                <div>
                    <div style="font-size:11px;color:var(--gray-500);">ID Peminjaman</div>
                    <div style="font-weight:700;">#${p.id}</div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--gray-500);margin-bottom:4px;">
                        Alat yang dipinjam
                    </div>
                    ${rows || '<span style="color:var(--gray-400)">-</span>'}
                </div>
                <div>
                    <div style="font-size:11px;color:var(--gray-500);">Tanggal Pinjam</div>
                    <div style="font-weight:600;">${p.tanggal_pinjam || '-'}</div>
                </div>
            </div>`;

        // Reset form
        document.getElementById('kembalikanPeminjamForm').reset();
        document.getElementById('kembalikanPeminjamanId').value = id;
        document.getElementById('kembalikanTanggal').value =
            new Date().toISOString().split('T')[0];

        document.getElementById('kembalikanPeminjamModal').classList.add('active');
    }

    document.getElementById('kembalikanPeminjamForm').addEventListener('submit', async e => {
        e.preventDefault();

        const peminjamanId = document.getElementById('kembalikanPeminjamanId').value;
        const tanggal      = document.getElementById('kembalikanTanggal').value;
        const kondisi      = document.getElementById('kembalikanKondisi').value;
        const keterangan   = document.getElementById('kembalikanKeterangan').value || null;

        if (!tanggal) { alert('Tanggal dikembalikan wajib diisi'); return; }
        if (!kondisi) { alert('Kondisi alat wajib dipilih'); return; }

        // Payload sesuai StorePengembalianRequest:
        // peminjaman_id, tanggal_kembali, kondisi_alat (baik|rusak), keterangan, denda
        const payload = {
            peminjaman_id:   parseInt(peminjamanId),
            tanggal_kembali: tanggal,
            kondisi_alat:    kondisi,
            keterangan:      keterangan,
            denda:           0,   // peminjam tidak input denda, petugas yang atur
        };

        const btn = document.getElementById('submitKembalikanPeminjamBtn');
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
                closeModal('kembalikanPeminjamModal');
                await loadPeminjaman();
                alert('Pengembalian berhasil diajukan! Terima kasih.');
            } else {
                const msg = data.errors
                    ? Object.values(data.errors).flat().join('\n')
                    : (data.message || 'Gagal mengajukan pengembalian');
                alert(msg);
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan koneksi');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-undo-alt"></i> Ajukan Pengembalian';
        }
    });

    /* ─── Init ─────────────────────────────────────────────────────────── */
    // Sembunyikan tombol "Pinjam Alat" untuk admin dan petugas
    // Sesuai modul: hanya Peminjam yang boleh mengajukan peminjaman
    if (user.role === 'admin' || user.role === 'petugas') {
        const btnPinjam = document.querySelector('button[onclick="showAddModal()"]');
        if (btnPinjam) btnPinjam.style.display = 'none';
    }

    /* ─── Approve peminjaman (Petugas & Admin) ─────────────────────────── */
    async function approvePeminjaman(id) {
        const p = allPeminjaman.find(x => x.id === id);
        if (!confirm(`Setujui peminjaman #${id} dari ${p?.user?.name || '?'}?\nStok alat akan dikurangi.`)) return;

        try {
            const res  = await fetch(`${API_URL}/peminjaman/${id}/approve`, {
                method: 'PATCH',
                headers: { Authorization: `Bearer ${getToken()}`, 'Content-Type': 'application/json' }
            });
            const data = await res.json();
            if (data.success) {
                await Promise.all([loadPeminjaman(), loadAlat()]);
                alert('Peminjaman berhasil disetujui!');
            } else {
                alert(data.message || 'Gagal menyetujui peminjaman');
            }
        } catch (e) { console.error(e); alert('Terjadi kesalahan koneksi'); }
    }

    /* ─── Tolak peminjaman (Petugas & Admin) ───────────────────────────── */
    async function tolakPeminjaman(id) {
        const p = allPeminjaman.find(x => x.id === id);
        if (!confirm(`Tolak peminjaman #${id} dari ${p?.user?.name || '?'}?`)) return;

        try {
            const res  = await fetch(`${API_URL}/peminjaman/${id}/tolak`, {
                method: 'PATCH',
                headers: { Authorization: `Bearer ${getToken()}`, 'Content-Type': 'application/json' }
            });
            const data = await res.json();
            if (data.success) {
                await Promise.all([loadPeminjaman(), loadAlat()]);
                alert('Peminjaman berhasil ditolak.');
            } else {
                alert(data.message || 'Gagal menolak peminjaman');
            }
        } catch (e) { console.error(e); alert('Terjadi kesalahan koneksi'); }
    }

    // Expose ke window karena dipakai di onclick HTML yang dirender JS
    window.tambahItem         = tambahItem;
    window.hapusItem          = hapusItem;
    window.showAddModal       = showAddModal;
    window.showDetail         = showDetail;
    window.closeModal         = closeModal;
    window.batalkanPeminjaman = batalkanPeminjaman;
    window.approvePeminjaman  = approvePeminjaman;
    window.tolakPeminjaman    = tolakPeminjaman;
    window.pilihAlat          = pilihAlat;
    window.ubahJumlah         = ubahJumlah;
    window.bukaDropdownAlat   = bukaDropdownAlat;
    window.filterDropdownAlat = filterDropdownAlat;
    window.resetAlatSearch    = resetAlatSearch;
    window.showModalKembalikan = showModalKembalikan;

    loadAlat();
    loadPeminjaman();
})();
</script>
@endpush
@endsection
