<header class="main-header d-flex align-items-center">
    {{-- LOGIC: Tombol Burger hanya untuk yang sudah login (Punya Sidebar) --}}
    @auth
    <i id="universal-toggler" class="fas fa-bars cursor-pointer"></i>
    @endauth

    {{-- Judul tetap muncul --}}
    <h1 class="ms-3 fs-4 mb-0">{{ $header ?? $title ?? '' }}</h1>
</header>