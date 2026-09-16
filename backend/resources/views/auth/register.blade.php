@extends('layouts.app')

@section('title', 'Register - Sistem Peminjaman Alat')

@section('body-class', 'auth-page')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <div class="logo">
            <i class="fas fa-rocket"></i>
        </div>
        <h1>Daftar Akun Baru</h1>
        <p>Buat akun untuk mulai meminjam alat</p>
    </div>

    <div id="alertBox" class="alert" style="display: none;"></div>

    <form id="registerForm">
        <div class="form-group">
            <label for="name">
                <i class="fas fa-user"></i> Nama Lengkap
            </label>
            <input type="text" id="name" name="name" placeholder="Masukkan nama lengkap" required>
        </div>

        <div class="form-group">
            <label for="email">
                <i class="fas fa-envelope"></i> Alamat Email
            </label>
            <input type="email" id="email" name="email" placeholder="email@example.com" required>
        </div>

        <div class="form-group">
            <label for="password">
                <i class="fas fa-lock"></i> Password
            </label>
            <input type="password" id="password" name="password" placeholder="Minimal 8 karakter" required minlength="8">
        </div>

        <div class="form-group">
            <label for="password_confirmation">
                <i class="fas fa-check-circle"></i> Konfirmasi Password
            </label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password" required>
        </div>

        <button type="submit" class="btn btn-primary" id="registerBtn">
            <i class="fas fa-rocket"></i> 
            <span>Daftar Sekarang</span>
        </button>

        <div class="divider">
            <span>sudah punya akun?</span>
        </div>

        <a href="{{ route('login') }}" class="btn btn-secondary">
            <i class="fas fa-sign-in-alt"></i> 
            <span>Masuk ke Akun</span>
        </a>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('registerForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const btn = document.getElementById('registerBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        
        const formData = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            password: document.getElementById('password').value,
            password_confirmation: document.getElementById('password_confirmation').value
        };
        
        try {
            const response = await fetch(`${API_URL}/auth/register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (data.success) {
                setToken(data.data.token);
                setUser(data.data.user);
                showAlert('Registrasi berhasil! Mengarahkan...', 'success');
                
                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 1000);
            } else {
                const errorMessage = data.errors 
                    ? Object.values(data.errors).flat().join(', ')
                    : (data.message || 'Registrasi gagal');
                showAlert(errorMessage, 'danger');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-user-plus"></i> Daftar Sekarang';
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan. Pastikan server berjalan!', 'danger');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-user-plus"></i> Daftar Sekarang';
        }
    });
</script>
@endpush
@endsection
