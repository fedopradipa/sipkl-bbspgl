{{--
    Catatan: Sidebar ini mengimplementasikan Hak Akses (RBAC) 
    berdasarkan RoleMiddleware yang terdaftar di Kernel.php.
--}}

@push('style')
<style>
    /* ... (style asli yang mungkin ada) ... */
</style>
@endpush
@auth
@php
$userRole = Auth::user()->role->nama_role ?? 'guest';
// Variabel untuk mengontrol tampilan Admin/Superadmin
$isAdminOrSuperadmin = in_array($userRole, ['admin', 'superadmin']);
// Variabel untuk mengontrol tampilan Teknisi/Superadmin (Perawatan)
$isTeknisiOrSuperadmin = in_array($userRole, ['teknisi', 'superadmin']);
@endphp

<nav id="sidebar" class="p-3 d-flex flex-column">
    <!-- Brand/Logo -->
    <a href="{{ route('dashboard') }}" class="d-flex flex-column align-items-center text-dark text-decoration-none mb-4">
        <i class="fas fa-boxes fa-3x text-primary mb-2"></i>
        <span class="fw-bold text-center">SIPKL BBSPGL</span>
    </a>

    <div class="sidebar-nav-wrapper">
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="fas fa-home fa-fw me-2"></i>Dashboard
                </a>
            </li>

            <!-- Pemisah Teks untuk Master Data -->
            <li class="nav-heading px-3 mt-3 mb-1 text-muted text-uppercase small">
                Master Data
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{route('lowongan.index')}}">
                    <i class="fas fa-tags fa-fw me-2"></i>lowongan
                </a>
            </li>


            {{-- =============================================== --}}
            {{-- MANAJEMEN USER (HANYA SUPERADMIN)               --}}
            {{-- =============================================== --}}
            @if(auth()->user()->isSuperadmin())
            <!-- Pemisah Teks untuk Manajemen User -->
            <li class="nav-heading px-3 mt-3 mb-1 text-muted text-uppercase small">
                Manajemen User
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('role.*') ? 'active' : '' }}" href="{{ route('role.index') }}">
                    <i class="fas fa-user-shield fa-fw me-2"></i>Role
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('user.*') ? 'active' : '' }}" href="{{ route('user.index') }}">
                    <i class="fas fa-users-cog fa-fw me-2"></i>User
                </a>
            </li>
            @endif
        </ul>
    </div>

    {{-- =============================================== --}}
    {{-- USER PROFILE FOOTER                             --}}
    {{-- =============================================== --}}
    <div class="profile-section mt-auto">
        <div class="d-flex align-items-center">

            <!-- Avatar dinamis berdasarkan foto_profil atau inisial -->
            @if (Auth::user()->foto_profil)
            <img src="{{ asset('storage/foto_profil/' . Auth::user()->foto_profil) }}"
                class="rounded-circle me-3"
                alt="User Avatar"
                style="width: 50px; height: 50px; object-fit: cover;">
            @else
            <img src="https://placehold.co/50x50/7E57C2/white?text={{ strtoupper(substr(Auth::user()->name, 0, 1)) }}"
                class="rounded-circle me-3"
                alt="User Avatar">
            @endif

            <div class="me-auto">
                <h6 class="mb-0 fw-bold">{{ Auth::user()->name }}</h6>
                <small class="text-muted profile-role">
                    {{ Auth::user()->jabatan ?? Auth::user()->role?->nama_role ?? 'User' }}
                </small>
            </div>

            <!-- Link Edit Profil -->
            <a href="{{ route('profile.edit') }}" class="profile-link" title="Edit Profil Saya">
                <i class="fas fa-cog fa-fw fs-5"></i>
            </a>

            <!-- Tombol Logout -->
            <a href="{{ route('logout') }}" class="logout-link" title="Logout"
                onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
                <i class="fas fa-sign-out-alt fa-fw fs-5"></i>
            </a>

            <!-- Form tersembunyi untuk proses logout -->
            <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>

        </div>
    </div>
    @endauth
</nav>