@extends('layouts.app')

@section('title', 'Login - Sistem Peminjaman Alat')

@section('body-class', 'auth-page')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <div class="logo">
            <i class="fas fa-cube"></i>
        </div>
        <h1>Sistem Peminjaman Alat</h1>
        <p>Silakan login untuk melanjutkan</p>
    </div>

    <div id="alertBox" class="alert" style="display: none;"></div>

    <form id="loginForm">
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
            <input type="password" id="password" name="password" placeholder="Masukkan password" required>
        </div>

        <button type="submit" class="btn btn-primary" id="loginBtn">
            <i class="fas fa-sign-in-alt"></i> 
            <span>Masuk ke Dashboard</span>
        </button>

        <div class="divider">
            <span>atau</span>
        </div>

        <a href="{{ route('register') }}" class="btn btn-secondary">
            <i class="fas fa-user-plus"></i> 
            <span>Daftar Akun Baru</span>
        </a>
    </form>

    <div class="demo-accounts">
        <p style="font-weight: 600; margin-bottom: 16px;"><i class="fas fa-rocket" style="color: var(--primary);"></i> Akun Demo Tersedia:</p>
        <div class="demo-grid">
            <button class="demo-btn" onclick="fillDemo('admin@example.com', 'password')" title="Login sebagai Admin">
                <i class="fas fa-user-shield"></i><br>
                <strong>Admin</strong>
            </button>
            <button class="demo-btn" onclick="fillDemo('petugas@example.com', 'password')" title="Login sebagai Petugas">
                <i class="fas fa-user-tie"></i><br>
                <strong>Petugas</strong>
            </button>
            <button class="demo-btn" onclick="fillDemo('peminjam@example.com', 'password')" title="Login sebagai Peminjam">
                <i class="fas fa-user"></i><br>
                <strong>Peminjam</strong>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function fillDemo(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }

    document.getElementById('loginForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const btn = document.getElementById('loginBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        
        const formData = {
            email: document.getElementById('email').value,
            password: document.getElementById('password').value
        };
        
        try {
            const response = await fetch(`${API_URL}/auth/login`, {
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
                showAlert('Login berhasil! Mengarahkan...', 'success');
                
                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 1000);
            } else {
                showAlert(data.message || 'Login gagal', 'danger');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Login';
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan. Pastikan server berjalan!', 'danger');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Login';
        }
    });
</script>
@endpush
@endsection
