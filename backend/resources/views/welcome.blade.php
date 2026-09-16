@extends('layouts.app')

@section('title', 'Welcome - Sistem Peminjaman Alat')

@section('body-class', 'auth-page')

@section('content')
<div style="max-width: 1200px; width: 100%; position: relative; z-index: 1;">
    <!-- Hero Section -->
    <div style="text-align: center; margin-bottom: 60px; animation: fadeIn 0.8s ease;">
        <div style="width: 120px; height: 120px; margin: 0 auto 32px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 30px; display: flex; align-items: center; justify-content: center; box-shadow: 0 20px 60px rgba(99, 102, 241, 0.3);">
            <i class="fas fa-cube" style="font-size: 56px; color: white;"></i>
        </div>
        
        <h1 style="font-size: 48px; font-weight: 800; color: white; margin-bottom: 16px; letter-spacing: -1px;">
            Sistem Peminjaman Alat
        </h1>
        
        <p style="font-size: 20px; color: rgba(255, 255, 255, 0.9); margin-bottom: 40px; max-width: 600px; margin-left: auto; margin-right: auto; line-height: 1.6;">
            Kelola peminjaman alat dengan mudah, cepat, dan modern. Solusi digital untuk manajemen inventaris yang efisien.
        </p>
        
        <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('login') }}" class="btn btn-primary" style="width: auto; padding: 16px 40px; font-size: 18px; box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);">
                <i class="fas fa-sign-in-alt"></i>
                <span>Masuk ke Sistem</span>
            </a>
            <a href="{{ route('register') }}" class="btn btn-secondary" style="width: auto; padding: 16px 40px; font-size: 18px; background: rgba(255, 255, 255, 0.95); color: var(--primary);">
                <i class="fas fa-user-plus"></i>
                <span>Daftar Sekarang</span>
            </a>
        </div>
    </div>

    <!-- Features -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 60px;">
        <div style="background: rgba(255, 255, 255, 0.98); padding: 32px; border-radius: 20px; text-align: center; backdrop-filter: blur(10px); box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1); transition: transform 0.3s; animation: slideUp 0.6s ease;">
            <div style="width: 64px; height: 64px; margin: 0 auto 20px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 16px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-boxes" style="font-size: 28px; color: white;"></i>
            </div>
            <h3 style="font-size: 20px; font-weight: 700; color: var(--gray-900); margin-bottom: 12px;">Kelola Alat</h3>
            <p style="color: var(--gray-600); font-size: 15px; line-height: 1.6;">Manajemen inventaris alat dengan sistem kategori yang terorganisir</p>
        </div>

        <div style="background: rgba(255, 255, 255, 0.98); padding: 32px; border-radius: 20px; text-align: center; backdrop-filter: blur(10px); box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1); transition: transform 0.3s; animation: slideUp 0.7s ease;">
            <div style="width: 64px; height: 64px; margin: 0 auto 20px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 16px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-hand-holding" style="font-size: 28px; color: white;"></i>
            </div>
            <h3 style="font-size: 20px; font-weight: 700; color: var(--gray-900); margin-bottom: 12px;">Peminjaman Mudah</h3>
            <p style="color: var(--gray-600); font-size: 15px; line-height: 1.6;">Proses peminjaman yang cepat dengan tracking waktu otomatis</p>
        </div>

        <div style="background: rgba(255, 255, 255, 0.98); padding: 32px; border-radius: 20px; text-align: center; backdrop-filter: blur(10px); box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1); transition: transform 0.3s; animation: slideUp 0.8s ease;">
            <div style="width: 64px; height: 64px; margin: 0 auto 20px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 16px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-chart-bar" style="font-size: 28px; color: white;"></i>
            </div>
            <h3 style="font-size: 20px; font-weight: 700; color: var(--gray-900); margin-bottom: 12px;">Laporan Lengkap</h3>
            <p style="color: var(--gray-600); font-size: 15px; line-height: 1.6;">Dashboard dan laporan real-time untuk monitoring aktivitas</p>
        </div>
    </div>

    <!-- Info Section -->
    <div style="background: rgba(255, 255, 255, 0.98); padding: 48px; border-radius: 24px; text-align: center; backdrop-filter: blur(10px); box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1); animation: slideUp 0.9s ease;">
        <h2 style="font-size: 32px; font-weight: 700; color: var(--gray-900); margin-bottom: 16px;">Siap Memulai?</h2>
        <p style="font-size: 16px; color: var(--gray-600); margin-bottom: 32px; max-width: 600px; margin-left: auto; margin-right: auto;">
            Daftar sekarang dan rasakan kemudahan dalam mengelola peminjaman alat. Sistem yang aman, cepat, dan mudah digunakan.
        </p>
        
        <div style="display: flex; gap: 12px; justify-content: center; align-items: center; flex-wrap: wrap; padding: 24px; background: var(--gray-50); border-radius: 16px; margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 8px; padding: 12px 20px; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);">
                <i class="fas fa-shield-alt" style="color: var(--success);"></i>
                <span style="font-weight: 600; color: var(--gray-700);">Aman & Terpercaya</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px; padding: 12px 20px; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);">
                <i class="fas fa-bolt" style="color: var(--warning);"></i>
                <span style="font-weight: 600; color: var(--gray-700);">Cepat & Responsif</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px; padding: 12px 20px; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);">
                <i class="fas fa-mobile-alt" style="color: var(--primary);"></i>
                <span style="font-weight: 600; color: var(--gray-700);">Mobile Friendly</span>
            </div>
        </div>

        <a href="{{ route('register') }}" class="btn btn-primary" style="width: auto; padding: 16px 48px; font-size: 18px;">
            <i class="fas fa-rocket"></i>
            <span>Mulai Sekarang - Gratis</span>
        </a>
    </div>

    <!-- Footer -->
    <div style="text-align: center; margin-top: 60px; padding-top: 32px; border-top: 1px solid rgba(255, 255, 255, 0.2);">
        <p style="color: rgba(255, 255, 255, 0.8); font-size: 14px;">
            © 2024 Sistem Peminjaman Alat. All rights reserved.
        </p>
    </div>
</div>

@push('styles')
<style>
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .btn:hover {
        transform: translateY(-3px) !important;
    }

    div[style*="transition: transform"]:hover {
        transform: translateY(-8px) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // Redirect if already logged in
    if (getToken()) {
        window.location.href = '/dashboard';
    }
</script>
@endpush
@endsection
