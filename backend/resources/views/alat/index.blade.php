@extends('layouts.app')

@section('title', 'Data Alat - Sistem Peminjaman Alat')

@section('body-class', 'dashboard-page')

@section('content')
@include('layouts.sidebar')

<!-- Main Content -->
<main class="main-content">
    @section('page-title', 'Data Alat')
    @include('layouts.topbar')

    <div class="content">

        <!-- Stats -->
        <div class="stats-grid" style="margin-bottom:32px;">
            <div class="stat-card gradient-primary">
                <div class="stat-icon"><i class="fas fa-boxes"></i></div>
                <div class="stat-content">
                    <div class="stat-value" id="totalAlat">0</div>
                    <div class="stat-label">Total Alat</div>
                </div>
            </div>
            <div class="stat-card gradient-success">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-content">
                    <div class="stat-value" id="alatBaik">0</div>
                    <div class="stat-label">Kondisi Baik</div>
                </div>
            </div>
            <div class="stat-card gradient-warning">
                <div class="stat-icon"><i class="fas fa-tools"></i></div>
                <div class="stat-content">
                    <div class="stat-value" id="alatPerbaikan">0</div>
                    <div class="stat-label">Dalam Perbaikan</div>
                </div>
            </div>
            <div class="stat-card gradient-danger">
                <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="stat-content">
                    <div class="stat-value" id="alatRusak">0</div>
                    <div class="stat-label">Rusak</div>
                </div>
            </div>
        </div>

        <!-- Search & Actions -->
        <div class="card">
            <div class="card-body">
                <div class="filters">
                    <div class="search-box" style="flex:1;">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Cari nama alat atau merk...">
                    </div>
                    <select class="filter-select" id="filterKategori">
                        <option value="">Semua Kategori</option>
                    </select>
                    <select class="filter-select" id="filterKondisi">
                        <option value="">Semua Kondisi</option>
                        <option value="baik">Baik</option>
                        <option value="rusak">Rusak</option>
                        <option value="dalam_perbaikan">Dalam Perbaikan</option>
                    </select>
                    <button class="btn btn-primary" onclick="showAddModal()"
                            style="width:auto; padding:12px 24px;" id="btnTambah">
                        <i class="fas fa-plus"></i>
                        <span>Tambah Alat</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> Daftar Alat</h3>
            </div>
            <div class="card-body no-padding">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Alat</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Kondisi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="alatTable">
                            <tr>
                                <td colspan="6" style="text-align:center; padding:40px; color:var(--gray-500);">
                                    <i class="fas fa-spinner fa-spin" style="font-size:24px;"></i>
                                    <div style="margin-top:8px;">Memuat data...</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- Modal Tambah / Edit -->
<div class="modal" id="alatModal">
    <div class="modal-content" style="max-width:540px;">
        <div class="modal-header">
            <h3 id="modalTitle">Tambah Alat</h3>
            <button class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="alatForm" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="hidden" id="alatId">

                <div class="form-group">
                    <label><i class="fas fa-box"></i> Nama Alat</label>
                    <input type="text" id="nama_alat" placeholder="Contoh: Laptop Dell Inspiron" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-trademark"></i> Merk / Model</label>
                    <input type="text" id="merk" placeholder="Contoh: Dell, HP, Bosch">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Kategori</label>
                    <select id="kategori_id" required>
                        <option value="">Pilih Kategori</option>
                    </select>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-cubes"></i> Jumlah / Stok</label>
                    <input type="number" id="jumlah" min="0" placeholder="0" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-clipboard-check"></i> Kondisi</label>
                    <select id="kondisi" required>
                        <option value="">Pilih Kondisi</option>
                        <option value="baik">Baik</option>
                        <option value="rusak">Rusak</option>
                        <option value="dalam_perbaikan">Dalam Perbaikan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Deskripsi</label>
                    <textarea id="deskripsi" rows="3" placeholder="Deskripsi singkat alat"
                              style="resize:vertical;"></textarea>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-image"></i> Foto Alat
                        <span style="font-weight:400; color:var(--gray-500);"> (opsional, maks 2MB)</span>
                    </label>
                    <input type="file" id="foto" accept="image/jpeg,image/png,image/jpg"
                           style="padding:10px;" onchange="previewFoto(this)">
                    <!-- Preview foto baru yang dipilih -->
                    <div id="fotoNewPreviewWrap" style="display:none; margin-top:8px;">
                        <div style="font-size:13px; color:var(--primary); font-weight:600; margin-bottom:4px;">
                            <i class="fas fa-check-circle"></i> Foto baru dipilih:
                        </div>
                        <img id="fotoNewPreview" src="" alt="Preview baru"
                             style="max-height:140px; border-radius:8px; border:2px solid var(--primary); object-fit:cover;">
                    </div>
                    <!-- preview foto lama saat edit -->
                    <div id="fotoPreviewWrap" style="display:none; margin-top:8px;">
                        <div style="font-size:13px; color:var(--gray-500); margin-bottom:4px;">
                            <i class="fas fa-image"></i> Foto saat ini (kosongkan jika tidak ingin mengubah):
                        </div>
                        <img id="fotoPreview" src="" alt="Foto saat ini"
                             style="max-height:120px; border-radius:8px; border:1px solid var(--gray-200); object-fit:cover;">
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()"
                        style="width:auto; padding:12px 24px;">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="submit" class="btn btn-primary" id="submitBtn"
                        style="width:auto; padding:12px 24px;">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function() {
    if (!getToken()) { window.location.href = '/'; return; }

    const user = initUserUI();
    if (!user) return;

    if (user.role === 'peminjam') document.getElementById('btnTambah').style.display = 'none';
    // Petugas hanya bisa lihat alat, tidak bisa tambah/edit/hapus
    if (user.role === 'petugas') document.getElementById('btnTambah').style.display = 'none';

    let allAlat   = [];
    let categories = [];

    // ─── Helper badge kondisi ─────────────────────────────────────────────────
    function getKondisiBadge(kondisi) {
        const map = {
            baik:             { color: 'success', icon: 'check-circle',   label: 'Baik' },
            rusak:            { color: 'danger',  icon: 'times-circle',   label: 'Rusak' },
            dalam_perbaikan:  { color: 'warning', icon: 'tools',          label: 'Dalam Perbaikan' },
        };
        const k = map[kondisi] || { color: 'primary', icon: 'question', label: kondisi || '-' };
        return `<span class="badge badge-${k.color}"><i class="fas fa-${k.icon}"></i> ${k.label}</span>`;
    }

    // ─── Load kategori ────────────────────────────────────────────────────────
    async function loadKategori() {
        try {
            const res  = await fetch(`${API_URL}/kategori-alat`, {
                headers: { 'Authorization': `Bearer ${getToken()}` }
            });
            const data = await res.json();
            if (data.success) {
                categories = data.data;
                const filterSel = document.getElementById('filterKategori');
                const modalSel  = document.getElementById('kategori_id');
                categories.forEach(cat => {
                    filterSel.innerHTML += `<option value="${cat.id}">${cat.nama_kategori}</option>`;
                    modalSel.innerHTML  += `<option value="${cat.id}">${cat.nama_kategori}</option>`;
                });
            }
        } catch (err) { console.error('Error loading kategori:', err); }
    }

    // ─── Load alat ────────────────────────────────────────────────────────────
    async function loadAlat() {
        try {
            const res  = await fetch(`${API_URL}/alat`, {
                headers: { 'Authorization': `Bearer ${getToken()}` }
            });
            const data = await res.json();
            if (data.success) {
                allAlat = data.data;
                updateStats();
                renderTable(allAlat);
            }
        } catch (err) {
            console.error('Error loading alat:', err);
            document.getElementById('alatTable').innerHTML = `
                <tr><td colspan="6" class="empty-state">
                    <i class="fas fa-exclamation-circle"></i>
                    <h3>Gagal Memuat Data</h3>
                </td></tr>`;
        }
    }

    function updateStats() {
        document.getElementById('totalAlat').textContent    = allAlat.length;
        // Gunakan field 'kondisi' yang sesuai DB
        document.getElementById('alatBaik').textContent     = allAlat.filter(a => a.kondisi === 'baik').length;
        document.getElementById('alatPerbaikan').textContent = allAlat.filter(a => a.kondisi === 'dalam_perbaikan').length;
        document.getElementById('alatRusak').textContent    = allAlat.filter(a => a.kondisi === 'rusak').length;
    }

    // ─── Gambar otomatis berdasarkan nama/kategori alat ──────────────────────
    // Prioritas: 1) foto upload dari DB, 2) gambar relevan berdasarkan keyword nama alat
    function getAlatImage(alat) {
        if (alat.foto) return alat.foto;

        // Cari keyword yang cocok dari nama alat dan kategori
        const nama = (alat.nama_alat + ' ' + (alat.kategori?.nama_kategori || '') + ' ' + (alat.merk || '')).toLowerCase();

        // Mapping keyword → Unsplash topic ID (konsisten, tidak deprecated)
        const keywordMap = [
            // Elektronik & komputer
            { keys: ['laptop','notebook','macbook'],   q: 'laptop'         },
            { keys: ['komputer','pc','desktop'],        q: 'computer'       },
            { keys: ['printer','cetak'],                q: 'printer'        },
            { keys: ['proyektor','projector','infocus'],q: 'projector'      },
            { keys: ['kamera','camera','foto'],         q: 'camera'         },
            { keys: ['tablet','ipad'],                  q: 'tablet'         },
            { keys: ['monitor','layar','screen'],       q: 'monitor'        },
            { keys: ['keyboard','mouse'],               q: 'keyboard'       },
            { keys: ['speaker','audio','sound'],        q: 'speaker'        },
            { keys: ['router','wifi','jaringan'],       q: 'router'         },
            { keys: ['handphone','hp','smartphone'],    q: 'smartphone'     },
            // Alat tulis & kantor
            { keys: ['meja','desk','table'],            q: 'desk'           },
            { keys: ['kursi','chair'],                  q: 'chair'          },
            { keys: ['whiteboard','papan tulis'],       q: 'whiteboard'     },
            { keys: ['scanner'],                        q: 'scanner'        },
            // Alat pertukangan & mesin
            { keys: ['bor','drill'],                    q: 'drill'          },
            { keys: ['gergaji','saw'],                  q: 'saw'            },
            { keys: ['palu','hammer'],                  q: 'hammer'         },
            { keys: ['kunci','wrench','spanner'],       q: 'wrench'         },
            { keys: ['obeng','screwdriver'],            q: 'screwdriver'    },
            { keys: ['gerinda','grinder'],              q: 'angle+grinder'  },
            { keys: ['las','welding'],                  q: 'welding'        },
            { keys: ['mesin','machine'],                q: 'machine'        },
            // Olahraga
            { keys: ['bola','ball','sepak'],            q: 'football'       },
            { keys: ['raket','badminton','tenis'],      q: 'badminton'      },
            { keys: ['basket','basketball'],            q: 'basketball'     },
            { keys: ['voli','volleyball'],              q: 'volleyball'     },
            { keys: ['renang','kolam','swim'],          q: 'swimming'       },
            { keys: ['sepeda','bike','bicycle'],        q: 'bicycle'        },
            { keys: ['treadmill','gym','fitness'],      q: 'gym'            },
            // Medis & lab
            { keys: ['stetoskop','stethoscope'],        q: 'stethoscope'    },
            { keys: ['mikroskop','microscope'],         q: 'microscope'     },
            { keys: ['termometer','thermometer'],       q: 'thermometer'    },
            { keys: ['lab','laboratorium'],             q: 'laboratory'     },
            // Musik
            { keys: ['gitar','guitar'],                 q: 'guitar'         },
            { keys: ['piano','keyboard+musik'],         q: 'piano'          },
            { keys: ['drum','musik','music'],           q: 'drum'           },
            // Lainnya
            { keys: ['tangga','ladder'],                q: 'ladder'         },
            { keys: ['tenda','tent'],                   q: 'tent'           },
        ];

        // Cari match pertama
        let keyword = null;
        for (const { keys, q } of keywordMap) {
            if (keys.some(k => nama.includes(k))) {
                keyword = q;
                break;
            }
        }

        // Jika tidak ada match, gunakan nama alat itu sendiri
        if (!keyword) {
            keyword = encodeURIComponent(alat.nama_alat.split(' ')[0]);
        }

        // Picsum tidak bisa keyword — gunakan Unsplash API public (tidak butuh key)
        // format: https://source.unsplash.com/featured/200x200/?keyword
        // Tapi sudah deprecated, jadi pakai loremflickr yang masih aktif
        return `https://loremflickr.com/200/200/${keyword}?lock=${alat.id}`;
    }

    // Fallback SVG gradient jika internet tidak ada
    function getFallbackImage(id) {
        const gradients = [
            ['667eea','764ba2'], ['f093fb','f5576c'], ['4facfe','00f2fe'],
            ['43e97b','38f9d7'], ['fa709a','fee140'], ['a18cd1','fbc2eb'],
            ['ffecd2','fcb69f'], ['a1c4fd','c2e9fb'], ['d4fc79','96e6a1'],
        ];
        const [c1, c2] = gradients[id % gradients.length];
        const svg = `<svg xmlns='http://www.w3.org/2000/svg' width='200' height='200'>
            <defs><linearGradient id='g${id}' x1='0%' y1='0%' x2='100%' y2='100%'>
                <stop offset='0%' style='stop-color:%23${c1}'/>
                <stop offset='100%' style='stop-color:%23${c2}'/>
            </linearGradient></defs>
            <rect width='200' height='200' fill='url(%23g${id})' rx='12'/>
            <text x='50%' y='54%' dominant-baseline='middle' text-anchor='middle'
                  font-family='sans-serif' font-size='72' opacity='0.85'>🔧</text>
        </svg>`;
        return 'data:image/svg+xml,' + encodeURIComponent(svg);
    }

    // ─── Render tabel ─────────────────────────────────────────────────────────
    function renderTable(data) {
        const tbody = document.getElementById('alatTable');

        if (data.length === 0) {
            tbody.innerHTML = `
                <tr><td colspan="6" class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Belum Ada Data Alat</h3>
                    <p>Klik tombol "Tambah Alat" untuk menambah data</p>
                </td></tr>`;
            return;
        }

        tbody.innerHTML = data.map(alat => `
            <tr>
                <td><strong>#${alat.id}</strong></td>
                <td>
                    <div style="display:flex; align-items:center; gap:12px;">
                        <img src="${getAlatImage(alat)}"
                             alt="${alat.nama_alat}"
                             style="width:52px;height:52px;border-radius:10px;object-fit:cover;border:1px solid var(--gray-200);background:var(--gray-100);"
                             onerror="this.src=getFallbackImage(${alat.id})">
                        <div>
                            <div style="font-weight:600; color:var(--gray-900);">${alat.nama_alat}</div>
                            <div style="font-size:13px; color:var(--gray-500);">
                                ${alat.merk ? `<i class="fas fa-trademark" style="font-size:11px;"></i> ${alat.merk}` : ''}
                                ${alat.deskripsi ? (alat.merk ? ' &bull; ' : '') + alat.deskripsi.substring(0, 50) + (alat.deskripsi.length > 50 ? '…' : '') : ''}
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge badge-primary">
                        <i class="fas fa-tag"></i>
                        ${alat.kategori?.nama_kategori || '-'}
                    </span>
                </td>
                <td><strong>${alat.jumlah ?? '-'}</strong></td>
                <td>${getKondisiBadge(alat.kondisi)}</td>
                <td>
                    <div class="action-buttons">
                        ${user?.role === 'admin' ? `
                            <button class="btn-icon btn-edit" onclick="editAlat(${alat.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-icon btn-delete" onclick="deleteAlat(${alat.id})" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>` : '<span style="color:var(--gray-400);font-size:13px;">-</span>'}
                    </div>
                </td>
            </tr>
        `).join('');
    }

    // ─── Filter ───────────────────────────────────────────────────────────────
    document.getElementById('searchInput').addEventListener('input', filterData);
    document.getElementById('filterKategori').addEventListener('change', filterData);
    document.getElementById('filterKondisi').addEventListener('change', filterData);

    function filterData() {
        const search  = document.getElementById('searchInput').value.toLowerCase();
        const kategori = document.getElementById('filterKategori').value;
        const kondisi  = document.getElementById('filterKondisi').value;

        const filtered = allAlat.filter(alat => {
            const matchSearch  = alat.nama_alat.toLowerCase().includes(search) ||
                                 (alat.merk && alat.merk.toLowerCase().includes(search)) ||
                                 (alat.deskripsi && alat.deskripsi.toLowerCase().includes(search));
            const matchKategori = !kategori || String(alat.kategori_id) === String(kategori);
            const matchKondisi  = !kondisi  || alat.kondisi === kondisi;
            return matchSearch && matchKategori && matchKondisi;
        });

        renderTable(filtered);
    }

    // ─── Preview foto sebelum upload ─────────────────────────────────────────
    function previewFoto(input) {
        const wrap = document.getElementById('fotoNewPreviewWrap');
        const img  = document.getElementById('fotoNewPreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                img.src = e.target.result;
                wrap.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            wrap.style.display = 'none';
            img.src = '';
        }
    }

    // ─── Modal ────────────────────────────────────────────────────────────────
    function showAddModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Alat';
        document.getElementById('alatForm').reset();
        document.getElementById('alatId').value = '';
        document.getElementById('fotoPreviewWrap').style.display    = 'none';
        document.getElementById('fotoNewPreviewWrap').style.display = 'none';
        document.getElementById('alatModal').classList.add('active');
    }

    function editAlat(id) {
        const alat = allAlat.find(a => a.id === id);
        if (!alat) return;

        document.getElementById('modalTitle').textContent   = 'Edit Alat';
        document.getElementById('alatId').value             = alat.id;
        document.getElementById('nama_alat').value          = alat.nama_alat;
        document.getElementById('merk').value               = alat.merk || '';
        document.getElementById('kategori_id').value        = alat.kategori_id;
        document.getElementById('jumlah').value             = alat.jumlah;
        document.getElementById('kondisi').value            = alat.kondisi;
        document.getElementById('deskripsi').value          = alat.deskripsi || '';

        // Reset preview foto baru
        document.getElementById('fotoNewPreviewWrap').style.display = 'none';
        document.getElementById('fotoNewPreview').src = '';

        // Tampilkan preview foto — jika ada upload pakai itu, kalau tidak pakai Unsplash
        const fotoSrc = alat.foto || getAlatImage(alat);
        document.getElementById('fotoPreview').src               = fotoSrc;
        document.getElementById('fotoPreviewWrap').style.display = 'block';
        document.getElementById('fotoPreviewWrap').querySelector('div').textContent =
            alat.foto ? 'Foto saat ini (kosongkan jika tidak ingin mengubah):'
                      : 'Gambar otomatis dari nama alat (upload foto untuk menggantinya):';

        document.getElementById('alatModal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('alatModal').classList.remove('active');
        // Reset preview
        document.getElementById('fotoNewPreviewWrap').style.display = 'none';
        document.getElementById('fotoNewPreview').src = '';
    }

    // ─── Submit (pakai FormData karena ada upload file) ───────────────────────
    document.getElementById('alatForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const id = document.getElementById('alatId').value;

        const formData = new FormData();
        formData.append('nama_alat',   document.getElementById('nama_alat').value);
        formData.append('merk',        document.getElementById('merk').value);
        formData.append('kategori_id', document.getElementById('kategori_id').value);
        formData.append('jumlah',      document.getElementById('jumlah').value);
        formData.append('kondisi',     document.getElementById('kondisi').value);
        formData.append('deskripsi',   document.getElementById('deskripsi').value);

        const fotoFile = document.getElementById('foto').files[0];
        if (fotoFile) formData.append('foto', fotoFile);

        // Laravel butuh _method=PUT untuk update via FormData
        if (id) formData.append('_method', 'PUT');

        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

        try {
            const url    = id ? `${API_URL}/alat/${id}` : `${API_URL}/alat`;
            const method = 'POST'; // selalu POST karena FormData; PUT via _method

            const res  = await fetch(url, {
                method,
                headers: { 'Authorization': `Bearer ${getToken()}` },
                // JANGAN set Content-Type — biarkan browser isi boundary FormData
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                closeModal();
                await loadAlat();
                alert(id ? 'Data alat berhasil diperbarui!' : 'Alat berhasil ditambahkan!');
            } else {
                const errors = data.errors
                    ? Object.values(data.errors).flat().join('\n')
                    : (data.message || 'Gagal menyimpan alat');
                alert(errors);
            }
        } catch (err) {
            console.error('Error:', err);
            alert('Terjadi kesalahan koneksi');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
        }
    });

    // ─── Hapus ────────────────────────────────────────────────────────────────
    async function deleteAlat(id) {
        if (!confirm('Yakin ingin menghapus alat ini? File foto juga akan dihapus.')) return;

        try {
            const res  = await fetch(`${API_URL}/alat/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${getToken()}` }
            });
            const data = await res.json();

            if (data.success) {
                await loadAlat();
                alert('Alat berhasil dihapus!');
            } else {
                alert(data.message || 'Gagal menghapus alat');
            }
        } catch (err) {
            console.error('Error:', err);
            alert('Terjadi kesalahan koneksi');
        }
    }

    // ─── Init ─────────────────────────────────────────────────────────────────
    // Expose ke window — dibutuhkan karena onclick di template string JS
    // tidak bisa akses fungsi dalam IIFE secara langsung
    window.showAddModal    = showAddModal;
    window.editAlat        = editAlat;
    window.deleteAlat      = deleteAlat;
    window.closeModal      = closeModal;
    window.previewFoto     = previewFoto;
    window.getFallbackImage = getFallbackImage;

    loadKategori();
    loadAlat();
})();
</script>
@endpush
@endsection
