<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'SIPKL BBSPGL' }}</title>

  <link rel="icon" href="{{ asset('images/logo.png')  }}" type="image/x-icon">


  {{-- Bootstrap & Icons --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  {{-- Custom Fonts (Opsional, agar mirip Astra) --}}
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background-color: #f8f9fa;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .navbar {
      padding-top: 1rem;
      padding-bottom: 1rem;
    }

    .logo-img {
      height: 40px;
      /* Sesuaikan tinggi logo */
      width: auto;
      margin-right: 10px;
    }

    .btn-login {
      background-color: #0d47a1;
      /* Biru BBSPGL */
      border-color: #0d47a1;
      color: white;
      transition: all 0.3s;
    }

    .btn-login:hover {
      background-color: #002171;
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(13, 71, 161, 0.3);
    }

    footer {
      margin-top: auto;
    }
  </style>

  @stack('style')
</head>

<body>

  {{-- NAVBAR --}}
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">

      {{-- LOGO BBSPGL --}}
      <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
        {{-- Pastikan file ada di public/images/logo.png --}}
        <img src="{{ asset('images/logo.png') }}" alt="Logo BBSPGL" class="logo-img">
        <span class="fw-bold text-dark d-inline d-sm-block">SIPKL BBSPGL</span>
      </a>

      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-center">

          {{-- Menu --}}
          <li class="nav-item">
            <a href="{{ route('guest.home') }}" class="nav-link fw-bold text-secondary me-3">Lowongan</a>
          </li>

          {{-- Auth Buttons --}}
          @if (Route::has('login'))
          @auth
          <li class="nav-item">
            <a href="{{ route('peserta.dashboard') }}" class="btn btn-outline-primary fw-bold rounded-pill px-4">
              Kembali Dashboard
            </a>
          </li>
          @else
          <li class="nav-item me-2">
            <a href="{{ route('login') }}" class="nav-link fw-bold text-dark">Masuk</a>
          </li>
          <li class="nav-item">
            <a href="{{ route('register') }}" class="btn btn-login fw-bold rounded-pill px-4 shadow-sm">
              Daftar
            </a>
          </li>
          @endauth
          @endif
        </ul>
      </div>
    </div>
  </nav>

  {{-- KONTEN UTAMA --}}
  <main>
    @yield('content')
  </main>

  {{-- FOOTER --}}
  <footer class="bg-white py-4 border-top">
    <div class="container text-center">
      <img src="{{ asset('images/logo.png') }}" alt="Logo Footer" style="height: 30px; opacity: 0.8;">

      <p class="text-muted small mb-0">
        &copy; {{ date('Y') }} <strong>Balai Besar Survei Pemetaan dan Geologi Kelautan Cirebon</strong>.<br>
      </p>
    </div>
  </footer>

  {{-- SCRIPTS --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  @if($message = Session::get('success'))
  <script>
    Swal.fire({
      title: "{{ $message }}",
      icon: "success",
      showConfirmButton: false,
      timer: 2000
    });
  </script>
  @endif
  @if($message = Session::get('error'))
  <script>
    Swal.fire({
      title: "{{ $message }}",
      icon: "error",
      showConfirmButton: false,
      timer: 2000
    });
  </script>
  @endif

  @stack('scripts')
</body>

</html>