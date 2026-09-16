@extends('layouts.app')

@section('title', 'Kategori Alat - Sistem Peminjaman Alat')

@section('body-class', 'dashboard-page')

@section('content')
@include('layouts.sidebar')

<!-- Main Content -->
<main class="main-content">
    @section('page-title', 'Kategori Alat')
    @include('layouts.topbar')

    <!-- Content -->
    <div class="content">
        <!-- Search and Actions -->
        <div class="card">
            <div class="card-body">
                <div class="filters">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Cari kategori...">
                    </div>
                    <button class="btn btn-primary" onclick="showAddModal()" style="width: auto; padding: 12px 24px;">
                        <i class="fas fa-plus"></i>
                        <span>Tambah Kategori</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Category Cards -->
        <div id="categoryList" class="loading">
            <i class="fas fa-spinner"></i> Loading...
        </div>
    </div>
</main>

<!-- Modal Add/Edit -->
<div class="modal" id="categoryModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Tambah Kategori</h3>
            <button class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="categoryForm">
            <div class="modal-body">
                <input type="hidden" id="categoryId">
                
                <div class="form-group">
                    <label for="nama_kategori">
                        <i class="fas fa-tag"></i> Nama Kategori
                    </label>
                    <input type="text" id="nama_kategori" placeholder="Masukkan nama kategori" required>
                </div>

                <div class="form-group">
                    <label for="deskripsi">
                        <i class="fas fa-align-left"></i> Deskripsi
                    </label>
                    <textarea id="deskripsi" rows="4" placeholder="Masukkan deskripsi kategori" style="resize: vertical;"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()" style="width: auto; padding: 12px 24px;">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="submit" class="btn btn-primary" id="submitBtn" style="width: auto; padding: 12px 24px;">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 24px;
    }

    .category-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        border: 2px solid var(--gray-200);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .category-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary), var(--secondary));
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s;
    }

    .category-card:hover::before {
        transform: scaleX(1);
    }

    .category-card:hover {
        border-color: var(--primary);
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(99, 102, 241, 0.15);
    }

    .category-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        margin-bottom: 16px;
    }

    .category-name {
        font-size: 20px;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 8px;
    }

    .category-desc {
        font-size: 14px;
        color: var(--gray-600);
        line-height: 1.6;
        margin-bottom: 16px;
    }

    .category-footer {
        display: flex;
        gap: 8px;
        padding-top: 16px;
        border-top: 1px solid var(--gray-200);
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    // Check auth
    if (!getToken()) { window.location.href = '/'; return; }

    const user = initUserUI();
    if (!user) return; // initUserUI sudah redirect ke '/' jika tidak login

    let categories = [];

    // Load categories
    async function loadCategories() {
        try {
            const response = await fetch(`${API_URL}/kategori-alat`, {
                headers: { 'Authorization': `Bearer ${getToken()}` }
            });
            const data = await response.json();
            
            if (data.success) {
                categories = data.data;
                renderCategories(categories);
            }
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('categoryList').innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-exclamation-circle"></i>
                    <h3>Gagal Memuat Data</h3>
                    <p>Terjadi kesalahan saat memuat kategori</p>
                </div>
            `;
        }
    }

    // Render categories
    function renderCategories(data) {
        const container = document.getElementById('categoryList');
        
        if (data.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h3>Belum Ada Kategori</h3>
                    <p>Klik tombol "Tambah Kategori" untuk menambah data</p>
                </div>
            `;
            return;
        }

        const icons = ['fa-tools', 'fa-laptop', 'fa-screwdriver', 'fa-hammer', 'fa-wrench', 'fa-cog'];
        
        container.innerHTML = `
            <div class="category-grid">
                ${data.map((cat, index) => `
                    <div class="category-card">
                        <div class="category-icon">
                            <i class="fas ${icons[index % icons.length]}"></i>
                        </div>
                        <div class="category-name">${cat.nama_kategori}</div>
                        <div class="category-desc">${cat.deskripsi || 'Tidak ada deskripsi'}</div>
                        <div class="category-footer">
                            <button class="btn-icon btn-edit" onclick="editCategory(${cat.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-icon btn-delete" onclick="deleteCategory(${cat.id})" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    }

    // Search
    document.getElementById('searchInput').addEventListener('input', (e) => {
        const search = e.target.value.toLowerCase();
        const filtered = categories.filter(cat => 
            cat.nama_kategori.toLowerCase().includes(search) ||
            (cat.deskripsi && cat.deskripsi.toLowerCase().includes(search))
        );
        renderCategories(filtered);
    });

    // Modal functions
    function showAddModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Kategori';
        document.getElementById('categoryId').value = '';
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryModal').classList.add('active');
    }

    function editCategory(id) {
        const category = categories.find(c => c.id === id);
        if (!category) return;

        document.getElementById('modalTitle').textContent = 'Edit Kategori';
        document.getElementById('categoryId').value = category.id;
        document.getElementById('nama_kategori').value = category.nama_kategori;
        document.getElementById('deskripsi').value = category.deskripsi || '';
        document.getElementById('categoryModal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('categoryModal').classList.remove('active');
    }

    // Submit form
    document.getElementById('categoryForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const id = document.getElementById('categoryId').value;
        const formData = {
            nama_kategori: document.getElementById('nama_kategori').value,
            deskripsi: document.getElementById('deskripsi').value
        };

        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

        try {
            const url = id ? `${API_URL}/kategori-alat/${id}` : `${API_URL}/kategori-alat`;
            const method = id ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method,
                headers: {
                    'Authorization': `Bearer ${getToken()}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (data.success) {
                closeModal();
                loadCategories();
                alert('Kategori berhasil disimpan!');
            } else {
                alert(data.message || 'Gagal menyimpan kategori');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan data');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
        }
    });

    // Delete category
    async function deleteCategory(id) {
        if (!confirm('Yakin ingin menghapus kategori ini?')) return;

        try {
            const response = await fetch(`${API_URL}/kategori-alat/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${getToken()}` }
            });

            const data = await response.json();

            if (data.success) {
                loadCategories();
                alert('Kategori berhasil dihapus!');
            } else {
                alert(data.message || 'Gagal menghapus kategori');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus data');
        }
    }

    // Load on start
    // Expose ke window — dibutuhkan untuk onclick di dalam template string JS
    window.showAddModal    = showAddModal;
    window.editCategory    = editCategory;
    window.deleteCategory  = deleteCategory;
    window.closeModal      = closeModal;

    loadCategories();
})();
</script>
@endpush
@endsection
