<header class="header">
    <div class="header-left">
        <button class="icon-btn" onclick="toggleSidebar()" style="display:none; margin-right:12px;" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>
        <h2>@yield('page-title', 'Dashboard')</h2>
    </div>
    <div class="header-right">

        {{-- ── Profil User (klik untuk ke halaman edit profil) ── --}}
        <a href="/profil" class="user-info" style="text-decoration:none; cursor:pointer;" title="Edit Profil">
            <div class="user-avatar" id="headerAvatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="user-details">
                <span class="user-name" id="userName">Loading...</span>
                <span class="user-role" id="userRole">-</span>
            </div>
        </a>

    </div>
</header>
