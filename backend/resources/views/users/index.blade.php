@extends('layouts.app')

@section('title', 'Manajemen User - Sistem Peminjaman Alat')

@section('body-class', 'dashboard-page')

@section('content')
@include('layouts.sidebar')

<!-- Main Content -->
<main class="main-content">
    @section('page-title', 'Manajemen User')
    @include('layouts.topbar')

    <!-- Content -->
    <div class="content">
        <!-- Stats -->
        <div class="stats-grid" style="margin-bottom: 32px;">
            <div class="stat-card gradient-primary">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="totalUsers">0</div>
                    <div class="stat-label">Total User</div>
                </div>
            </div>
            <div class="stat-card gradient-danger">
                <div class="stat-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="totalAdmin">0</div>
                    <div class="stat-label">Admin</div>
                </div>
            </div>
            <div class="stat-card gradient-warning">
                <div class="stat-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="totalPetugas">0</div>
                    <div class="stat-label">Petugas</div>
                </div>
            </div>
            <div class="stat-card gradient-success">
                <div class="stat-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="totalPeminjam">0</div>
                    <div class="stat-label">Peminjam</div>
                </div>
            </div>
        </div>

        <!-- Search and Actions -->
        <div class="card">
            <div class="card-body">
                <div class="filters">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Cari user...">
                    </div>
                    <select class="filter-select" id="filterRole">
                        <option value="">Semua Role</option>
                        <option value="admin">Admin</option>
                        <option value="petugas">Petugas</option>
                        <option value="peminjam">Peminjam</option>
                    </select>
                    <button class="btn btn-primary" onclick="showAddModal()" style="width: auto; padding: 12px 24px;">
                        <i class="fas fa-user-plus"></i>
                        <span>Tambah User</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> Daftar User</h3>
            </div>
            <div class="card-body no-padding">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="userTable">
                            <tr>
                                <td colspan="5" class="loading">
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

<!-- Modal Add/Edit -->
<div class="modal" id="userModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Tambah User</h3>
            <button class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="userForm">
            <div class="modal-body">
                <input type="hidden" id="userId">
                
                <div class="form-group">
                    <label for="name">
                        <i class="fas fa-user"></i> Nama Lengkap
                    </label>
                    <input type="text" id="name" placeholder="Masukkan nama lengkap" required>
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" id="email" placeholder="email@example.com" required>
                </div>

                <div class="form-group">
                    <label for="role">
                        <i class="fas fa-user-tag"></i> Role
                    </label>
                    <select id="role" required>
                        <option value="">Pilih Role</option>
                        <option value="admin">Admin</option>
                        <option value="petugas">Petugas</option>
                        <option value="peminjam">Peminjam</option>
                    </select>
                </div>

                <div class="form-group" id="passwordGroup">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="password" id="password" placeholder="Minimal 8 karakter" minlength="8">
                    <div style="font-size: 13px; color: var(--gray-500); margin-top: 4px;">
                        <i class="fas fa-info-circle"></i> Kosongkan jika tidak ingin mengubah password
                    </div>
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

@push('scripts')
<script>
(function() {
    if (!getToken()) { window.location.href = '/'; return; }

    const user = initUserUI();
    if (!user) return; // initUserUI sudah redirect ke '/' jika tidak login
    if (user.role !== 'admin') { window.location.href = '/dashboard'; return; }

    let allUsers = [];

    async function loadUsers() {
        try {
            const response = await fetch(`${API_URL}/users`, {
                headers: { 'Authorization': `Bearer ${getToken()}` }
            });
            const data = await response.json();
            
            if (data.success) {
                allUsers = data.data;
                updateStats();
                renderTable(allUsers);
            }
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('userTable').innerHTML = `
                <tr><td colspan="5" class="empty-state">
                    <i class="fas fa-exclamation-circle"></i>
                    <h3>Gagal Memuat Data</h3>
                </td></tr>
            `;
        }
    }

    function updateStats() {
        const total = allUsers.length;
        const admin = allUsers.filter(u => u.role === 'admin').length;
        const petugas = allUsers.filter(u => u.role === 'petugas').length;
        const peminjam = allUsers.filter(u => u.role === 'peminjam').length;
        
        document.getElementById('totalUsers').textContent = total;
        document.getElementById('totalAdmin').textContent = admin;
        document.getElementById('totalPetugas').textContent = petugas;
        document.getElementById('totalPeminjam').textContent = peminjam;
    }

    function getRoleBadge(role) {
        const badges = {
            admin: { color: 'danger', icon: 'user-shield' },
            petugas: { color: 'warning', icon: 'user-tie' },
            peminjam: { color: 'success', icon: 'user' }
        };
        const badge = badges[role] || { color: 'primary', icon: 'user' };
        return `<span class="badge badge-${badge.color}"><i class="fas fa-${badge.icon}"></i> ${role}</span>`;
    }

    function renderTable(data) {
        const tbody = document.getElementById('userTable');
        
        if (data.length === 0) {
            tbody.innerHTML = `
                <tr><td colspan="5" class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Belum Ada Data User</h3>
                </td></tr>
            `;
            return;
        }

        tbody.innerHTML = data.map(u => `
            <tr>
                <td><strong>#${u.id}</strong></td>
                <td>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 16px;">
                            ${u.name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2)}
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 15px;">${u.name}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div style="color: var(--gray-600);">
                        <i class="fas fa-envelope" style="color: var(--gray-400);"></i>
                        ${u.email}
                    </div>
                </td>
                <td>${getRoleBadge(u.role)}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-icon btn-edit" onclick="editUser(${u.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        ${u.id !== user.id ? `
                            <button class="btn-icon btn-delete" onclick="deleteUser(${u.id})" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `).join('');
    }

    // Filters
    document.getElementById('searchInput').addEventListener('input', filterData);
    document.getElementById('filterRole').addEventListener('change', filterData);

    function filterData() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const role = document.getElementById('filterRole').value;

        const filtered = allUsers.filter(u => {
            const matchSearch = u.name.toLowerCase().includes(search) || u.email.toLowerCase().includes(search);
            const matchRole = !role || u.role === role;
            
            return matchSearch && matchRole;
        });

        renderTable(filtered);
    }

    function showAddModal() {
        document.getElementById('modalTitle').textContent = 'Tambah User';
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('password').required = true;
        document.getElementById('passwordGroup').querySelector('div').style.display = 'none';
        document.getElementById('userModal').classList.add('active');
    }

    function editUser(id) {
        const u = allUsers.find(item => item.id === id);
        if (!u) return;

        document.getElementById('modalTitle').textContent = 'Edit User';
        document.getElementById('userId').value = u.id;
        document.getElementById('name').value = u.name;
        document.getElementById('email').value = u.email;
        document.getElementById('role').value = u.role;
        document.getElementById('password').value = '';
        document.getElementById('password').required = false;
        document.getElementById('passwordGroup').querySelector('div').style.display = 'block';
        document.getElementById('userModal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('userModal').classList.remove('active');
    }

    document.getElementById('userForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const id = document.getElementById('userId').value;
        const formData = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            role: document.getElementById('role').value
        };

        const password = document.getElementById('password').value;
        if (password) {
            formData.password = password;
            formData.password_confirmation = password;
        }

        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

        try {
            const url = id ? `${API_URL}/users/${id}` : `${API_URL}/users`;
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
                loadUsers();
                alert('User berhasil disimpan!');
            } else {
                const errorMessage = data.errors 
                    ? Object.values(data.errors).flat().join(', ')
                    : (data.message || 'Gagal menyimpan user');
                alert(errorMessage);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
        }
    });

    async function deleteUser(id) {
        if (!confirm('Yakin ingin menghapus user ini?')) return;

        try {
            const response = await fetch(`${API_URL}/users/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${getToken()}` }
            });

            const data = await response.json();

            if (data.success) {
                loadUsers();
                alert('User berhasil dihapus!');
            } else {
                alert(data.message || 'Gagal menghapus user');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        }
    }

    // Expose ke window — dibutuhkan untuk onclick di dalam template string JS
    window.showAddModal = showAddModal;
    window.editUser     = editUser;
    window.deleteUser   = deleteUser;
    window.closeModal   = closeModal;

    loadUsers();
})();
</script>
@endpush
@endsection
