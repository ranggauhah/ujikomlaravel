@extends('layouts.app')

@section('title', 'Edit Profil - Sistem Peminjaman Alat')

@section('content')
<div class="dashboard-page">
    @include('layouts.sidebar')
    <div class="main-content">
        @include('layouts.topbar')
        <div class="content">
            @section('page-title', 'Edit Profil')

            {{-- Alert container --}}
            <div id="alertContainer"></div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; max-width:900px;">

                {{-- ── Kartu Edit Profil ── --}}
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-user-edit"></i> Informasi Profil</h3>
                    </div>
                    <div class="card-body">

                        {{-- Avatar + Upload Foto --}}
                        <div style="display:flex; align-items:center; gap:16px; margin-bottom:28px;">

                            {{-- Lingkaran avatar yang bisa diklik --}}
                            <div style="position:relative; flex-shrink:0;">
                                <div id="profilAvatar" style="
                                    width:80px; height:80px; border-radius:50%;
                                    background:linear-gradient(135deg,var(--primary),var(--secondary));
                                    display:flex; align-items:center; justify-content:center;
                                    color:#fff; font-size:28px; font-weight:700;
                                    overflow:hidden; cursor:pointer;
                                    border:3px solid rgba(99,102,241,0.25);
                                    transition:opacity .2s;"
                                    onclick="document.getElementById('inputFoto').click()"
                                    title="Klik untuk ganti foto">
                                    <i class="fas fa-user" id="avatarIcon"></i>
                                    <img id="avatarImg" src="" alt="Foto Profil"
                                        style="display:none; width:100%; height:100%; object-fit:cover;">
                                </div>
                                {{-- overlay kamera --}}
                                <div onclick="document.getElementById('inputFoto').click()" style="
                                    position:absolute; bottom:0; right:0;
                                    width:26px; height:26px; border-radius:50%;
                                    background:var(--primary); border:2px solid #fff;
                                    display:flex; align-items:center; justify-content:center;
                                    cursor:pointer; color:#fff; font-size:11px;"
                                    title="Ganti foto">
                                    <i class="fas fa-camera"></i>
                                </div>
                                {{-- input file tersembunyi --}}
                                <input type="file" id="inputFoto" accept="image/jpeg,image/png,image/jpg,image/webp"
                                    style="display:none;" onchange="previewDanUploadFoto(event)">
                            </div>

                            <div style="flex:1; min-width:0;">
                                <div id="profilNamaHeader" style="font-weight:700;font-size:18px;color:var(--gray-900);">Loading...</div>
                                <div id="profilRoleHeader" style="font-size:13px;color:var(--gray-500);text-transform:capitalize;margin-top:2px;"></div>
                                <div id="profilEmailHeader" style="font-size:13px;color:var(--gray-400);margin-top:2px;"></div>
                                <div style="margin-top:8px; display:flex; gap:8px; flex-wrap:wrap;">
                                    <button type="button" onclick="document.getElementById('inputFoto').click()"
                                        style="font-size:11px; padding:4px 10px; border-radius:20px;
                                               background:rgba(99,102,241,0.1); color:var(--primary);
                                               border:1px solid rgba(99,102,241,0.3); cursor:pointer; font-weight:600;">
                                        <i class="fas fa-camera"></i> Ganti Foto
                                    </button>
                                    <button type="button" id="btnHapusFoto" onclick="hapusFotoProfil()"
                                        style="display:none; font-size:11px; padding:4px 10px; border-radius:20px;
                                               background:rgba(239,68,68,0.08); color:#ef4444;
                                               border:1px solid rgba(239,68,68,0.25); cursor:pointer; font-weight:600;">
                                        <i class="fas fa-trash"></i> Hapus Foto
                                    </button>
                                </div>
                            </div>
                        </div>

                        <form id="formProfil">
                            <div class="form-group">
                                <label><i class="fas fa-user"></i> Nama Lengkap</label>
                                <input type="text" id="inputNama" placeholder="Masukkan nama lengkap" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" id="inputEmail" placeholder="Masukkan email" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-shield-alt"></i> Role</label>
                                <input type="text" id="inputRole" disabled style="background:var(--gray-100);cursor:not-allowed;">
                            </div>
                            <button type="submit" class="btn btn-primary" id="btnSimpanProfil">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </form>

                    </div>
                </div>

                {{-- ── Kartu Ganti Password ── --}}
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-lock"></i> Ganti Password</h3>
                    </div>
                    <div class="card-body">

                        <div style="
                            background:rgba(99,102,241,0.05);
                            border:1px solid rgba(99,102,241,0.15);
                            border-radius:12px; padding:14px 16px;
                            margin-bottom:24px; font-size:13px; color:var(--gray-600);">
                            <i class="fas fa-info-circle" style="color:var(--primary);margin-right:6px;"></i>
                            Password minimal 8 karakter. Gunakan kombinasi huruf dan angka.
                        </div>

                        <form id="formPassword">
                            <div class="form-group">
                                <label><i class="fas fa-key"></i> Password Lama</label>
                                <div style="position:relative;">
                                    <input type="password" id="inputPasswordLama" placeholder="Masukkan password lama" required>
                                    <button type="button" onclick="togglePass('inputPasswordLama', this)"
                                        style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--gray-400);">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-lock"></i> Password Baru</label>
                                <div style="position:relative;">
                                    <input type="password" id="inputPasswordBaru" placeholder="Minimal 8 karakter" required>
                                    <button type="button" onclick="togglePass('inputPasswordBaru', this)"
                                        style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--gray-400);">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-lock"></i> Konfirmasi Password Baru</label>
                                <div style="position:relative;">
                                    <input type="password" id="inputPasswordKonfirmasi" placeholder="Ulangi password baru" required>
                                    <button type="button" onclick="togglePass('inputPasswordKonfirmasi', this)"
                                        style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--gray-400);">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" id="btnGantiPassword">
                                <i class="fas fa-key"></i> Ganti Password
                            </button>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Load data profil saat halaman dibuka ──────────────────────────
async function loadProfil() {
    const token = getToken();
    if (!token) { window.location.href = '/'; return; }

    try {
        const res  = await fetch(`${API_URL}/auth/me`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        const json = await res.json();
        if (!json.success) { window.location.href = '/'; return; }

        const u = json.data;

        // Isi form
        document.getElementById('inputNama').value  = u.name  || '';
        document.getElementById('inputEmail').value = u.email || '';
        document.getElementById('inputRole').value  = u.role  || '';

        // Header avatar
        document.getElementById('profilNamaHeader').textContent  = u.name  || '-';
        document.getElementById('profilRoleHeader').textContent  = u.role  || '-';
        document.getElementById('profilEmailHeader').textContent = u.email || '-';

        // Foto atau inisial di avatar
        setAvatar(u.foto, u.name);
    } catch (e) {
        showAlert('Gagal memuat data profil', 'danger');
    }
}

// ── Submit edit profil ────────────────────────────────────────────
document.getElementById('formProfil').addEventListener('submit', async function (e) {
    e.preventDefault();
    const btn = document.getElementById('btnSimpanProfil');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    try {
        const res  = await fetch(`${API_URL}/auth/profile`, {
            method:  'PUT',
            headers: {
                'Content-Type': 'application/json',
                Authorization:  `Bearer ${getToken()}`,
            },
            body: JSON.stringify({
                name:  document.getElementById('inputNama').value.trim(),
                email: document.getElementById('inputEmail').value.trim(),
            }),
        });
        const json = await res.json();

        if (json.success) {
            // Update localStorage agar nama di sidebar/topbar ikut berubah
            const stored = localStorage.getItem('user');
            if (stored) {
                const u = JSON.parse(stored);
                u.name  = json.data.name;
                u.email = json.data.email;
                localStorage.setItem('user', JSON.stringify(u));
            }
            if (typeof refreshUserUI === 'function') refreshUserUI();
            showAlert('Profil berhasil diperbarui!', 'success');
            loadProfil();
        } else {
            const msg = json.errors
                ? Object.values(json.errors).flat().join(', ')
                : json.message || 'Gagal memperbarui profil';
            showAlert(msg, 'danger');
        }
    } catch (e) {
        showAlert('Terjadi kesalahan. Coba lagi.', 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan Perubahan';
    }
});

// ── Submit ganti password ─────────────────────────────────────────
document.getElementById('formPassword').addEventListener('submit', async function (e) {
    e.preventDefault();

    const passwordBaru        = document.getElementById('inputPasswordBaru').value;
    const passwordKonfirmasi  = document.getElementById('inputPasswordKonfirmasi').value;

    if (passwordBaru !== passwordKonfirmasi) {
        showAlert('Konfirmasi password tidak cocok!', 'danger');
        return;
    }
    if (passwordBaru.length < 8) {
        showAlert('Password baru minimal 8 karakter!', 'danger');
        return;
    }

    const btn = document.getElementById('btnGantiPassword');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengubah...';

    try {
        const res  = await fetch(`${API_URL}/auth/change-password`, {
            method:  'PUT',
            headers: {
                'Content-Type': 'application/json',
                Authorization:  `Bearer ${getToken()}`,
            },
            body: JSON.stringify({
                password_lama:              document.getElementById('inputPasswordLama').value,
                password_baru:              passwordBaru,
                password_baru_confirmation: passwordKonfirmasi,
            }),
        });
        const json = await res.json();

        if (json.success) {
            showAlert('Password berhasil diubah!', 'success');
            document.getElementById('formPassword').reset();
        } else {
            showAlert(json.message || 'Gagal mengubah password', 'danger');
        }
    } catch (e) {
        showAlert('Terjadi kesalahan. Coba lagi.', 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-key"></i> Ganti Password';
    }
});

// ── Toggle show/hide password ─────────────────────────────────────
function togglePass(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type   = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type   = 'password';
        icon.className = 'fas fa-eye';
    }
}

// ── Helper: set avatar (foto atau inisial) ────────────────────────
function setAvatar(fotoUrl, nama) {
    const icon = document.getElementById('avatarIcon');
    const img  = document.getElementById('avatarImg');
    const btn  = document.getElementById('btnHapusFoto');

    if (fotoUrl) {
        img.src          = fotoUrl;
        img.style.display = 'block';
        icon.style.display = 'none';
        btn.style.display  = 'inline-flex';
    } else {
        img.style.display  = 'none';
        icon.style.display = '';
        // tampilkan inisial
        const avatar = document.getElementById('profilAvatar');
        if (nama) {
            icon.className = '';
            icon.textContent = nama.charAt(0).toUpperCase();
        } else {
            icon.className = 'fas fa-user';
            icon.textContent = '';
        }
        btn.style.display = 'none';
    }
}

// ── Preview & upload foto langsung setelah dipilih ────────────────
async function previewDanUploadFoto(event) {
    const file = event.target.files[0];
    if (!file) return;

    // Validasi ukuran di sisi klien (maks 2 MB)
    if (file.size > 2 * 1024 * 1024) {
        showAlert('Ukuran foto maksimal 2 MB', 'danger');
        event.target.value = '';
        return;
    }

    // Preview langsung via FileReader
    const reader = new FileReader();
    reader.onload = (e) => setAvatar(e.target.result, null);
    reader.readAsDataURL(file);

    // Upload ke server
    const formData = new FormData();
    formData.append('foto', file);

    try {
        const res  = await fetch(`${API_URL}/auth/profile/photo`, {
            method:  'POST',
            headers: { Authorization: `Bearer ${getToken()}` },
            body:    formData,
        });
        const json = await res.json();

        if (json.success) {
            // Update avatar dengan URL dari server
            setAvatar(json.data.foto, json.data.name);
            // Sync localStorage
            const stored = localStorage.getItem('user');
            if (stored) {
                const u = JSON.parse(stored);
                u.foto  = json.data.foto;
                localStorage.setItem('user', JSON.stringify(u));
            }
            if (typeof refreshUserUI === 'function') refreshUserUI();
            showAlert('Foto profil berhasil diperbarui!', 'success');
        } else {
            showAlert(json.message || 'Gagal mengunggah foto', 'danger');
            loadProfil(); // kembalikan avatar semula
        }
    } catch (err) {
        showAlert('Terjadi kesalahan saat mengunggah foto', 'danger');
        loadProfil();
    } finally {
        event.target.value = ''; // reset input agar file yang sama bisa dipilih ulang
    }
}

// ── Hapus foto profil ─────────────────────────────────────────────
async function hapusFotoProfil() {
    if (!confirm('Hapus foto profil?')) return;

    try {
        const res  = await fetch(`${API_URL}/auth/profile/photo`, {
            method:  'DELETE',
            headers: { Authorization: `Bearer ${getToken()}` },
        });
        const json = await res.json();

        if (json.success) {
            setAvatar(null, document.getElementById('profilNamaHeader').textContent);
            const stored = localStorage.getItem('user');
            if (stored) {
                const u = JSON.parse(stored);
                u.foto  = null;
                localStorage.setItem('user', JSON.stringify(u));
            }
            if (typeof refreshUserUI === 'function') refreshUserUI();
            showAlert('Foto profil berhasil dihapus', 'success');
        } else {
            showAlert(json.message || 'Gagal menghapus foto', 'danger');
        }
    } catch (err) {
        showAlert('Terjadi kesalahan saat menghapus foto', 'danger');
    }
}

// ── Helper tampilkan alert ────────────────────────────────────────
function showAlert(message, type) {
    const container = document.getElementById('alertContainer');
    const icons = { success: 'fa-check-circle', danger: 'fa-exclamation-circle', warning: 'fa-exclamation-triangle' };
    container.innerHTML = `
        <div class="alert alert-${type}" style="max-width:900px;margin-bottom:20px;">
            <i class="fas ${icons[type] || 'fa-info-circle'}"></i>
            ${message}
        </div>`;
    // Auto hilang setelah 4 detik
    setTimeout(() => { container.innerHTML = ''; }, 4000);
}

// Jalankan saat halaman dibuka
loadProfil();
</script>
@endpush
