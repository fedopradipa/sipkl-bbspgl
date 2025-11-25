<nav id="sidebar" class="p-3 d-flex flex-column bg-white shadow-sm h-100" style="min-width: 260px;">
    <a class="d-flex flex-column align-items-center text-dark text-decoration-none mb-4 pt-2 border-bottom pb-3">
        <i class="fas fa-boxes fa-2x text-primary mb-2"></i>
        <span class="fw-bold fs-5">SIPKL BBSPGL</span>
    </a>

    <div class="sidebar-nav-wrapper flex-grow-1 overflow-auto">

        {{-- Jika Superadmin --}}
        @if(auth()->user()->isSuperadmin())
        @include('partials.sidebar.superadmin')

        {{-- Jika Admin --}}
        @elseif(auth()->user()->isAdmin())
        @include('partials.sidebar.admin')

        {{-- Jika Peserta --}}
        @elseif(auth()->user()->isPeserta())
        @include('partials.sidebar.peserta')

        {{-- Default / Fallback --}}
        @else
        <div class="alert alert-warning m-3 small">
            Menu tidak tersedia untuk role Anda.
        </div>
        @endif

    </div>

    <div class="mt-auto pt-3 border-top">
        <div class="d-flex align-items-center">
            {{-- Avatar Logic --}}
            @if(auth()->user()->foto_profil)
            {{-- Tambahkan 'foto_profil/' setelah 'storage/' --}}
            <img src="{{ asset('storage/foto_profil/'.auth()->user()->foto_profil) }}"
                width="35" height="35"
                class="rounded-circle me-2 object-fit-cover">
            @else
            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random"
                width="35" height="35"
                class="rounded-circle me-2">
            @endif

            <div class="overflow-hidden">
                <h6 class="mb-0 small fw-bold text-truncate" style="max-width: 120px;">{{ auth()->user()->name }}</h6>
                <small class="text-muted" style="font-size: 10px;">{{ strtoupper(auth()->user()->role->nama_role) }}</small>

            </div>

            <form action="{{ route('logout') }}" method="POST" class="d-flex align-items-center gap-2 ms-auto">
                @csrf

                <!-- Tombol Edit Profile -->
                <a href="{{ Auth::user()->edit_profile_route }}"
                    class="btn btn-sm btn-light"
                    title="Edit Profil Saya">
                    <i class="fas fa-cog"></i>
                </a>

                <!-- Tombol Logout -->
                <button type="submit"
                    class="btn btn-sm btn-danger"
                    title="Keluar">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>

        </div>
    </div>
</nav>