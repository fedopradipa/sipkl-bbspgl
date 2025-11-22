<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top py-3">
    <div class="container">
        {{-- LOGO BRAND --}}
        <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="{{ url('/') }}">
            <div class="bg-primary text-white rounded p-1 me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                <i class="fas fa-globe-asia"></i>
            </div>
            <div>
                <span class="text-dark"> SIPKL</span>BBSPGL
            </div>
        </a>

        {{-- TOGGLE BUTTON (Untuk HP) --}}
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- MENU KANAN --}}
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">

                {{-- Cek apakah User sedang login atau tidak --}}
                @auth
                {{-- JIKA SUDAH LOGIN (Misal Admin/Peserta nyasar ke halaman depan) --}}
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary fw-bold rounded-pill px-4">
                        <i class="fas fa-tachometer-alt me-2"></i> Ke Dashboard
                    </a>
                </li>
                @else
                {{-- JIKA TAMU (GUEST) --}}
                <li class="nav-item">
                    <a href="{{ route('login') }}" class="nav-link fw-bold text-dark me-2">login</a>
                </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>