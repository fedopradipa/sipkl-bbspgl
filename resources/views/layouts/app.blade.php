<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? 'Sistem PKL BBSPGL' }}</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  {{-- CSS Tom-select (Jika nanti dibutuhkan di form tamu) --}}
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
  {{-- CSS Aplikasi Anda --}}
  <link rel="stylesheet" href="{{ asset('css/app-style.css') }}">

  <style>
    /* Penyesuaian khusus Guest agar sedikit beda dengan Admin */
    body {
      background-color: #f8f9fa;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .navbar-brand {
      letter-spacing: 1px;
    }

    footer {
      margin-top: auto;
    }
  </style>

  @stack('style')
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
      {{-- Logo / Nama Aplikasi --}}
      <a class="navbar-brand fw-bold text-primary" href="{{ url('/') }}">
        <i class="fas fa-globe-asia me-2"></i>PKL BBSPGL
      </a>

      {{-- Tombol Toggle untuk Mobile --}}
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      {{-- Menu Kanan --}}
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-center">
          @if (Route::has('login'))
          @auth
          {{-- Jika sudah login, arahkan ke Dashboard --}}
          <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm fw-bold">
              <i class="fas fa-tachometer-alt me-1"></i> Kembali ke Dashboard
            </a>
          </li>
          @else
          {{-- Jika belum login --}}
          <li class="nav-item">
            <a href="{{ route('login') }}" class="btn btn-primary btn-sm fw-bold px-4">
              <i class="fas fa-sign-in-alt me-1"></i> Login
            </a>
          </li>
          @endauth
          @endif
        </ul>
      </div>
    </div>
  </nav>

  <main>
    @yield('content')
  </main>

  <footer class="bg-white text-center py-4 mt-auto border-top">
    <div class="container">
      <small class="text-muted">
        &copy; {{ date('Y') }} Balai Besar Survei Pemetaan dan Geologi Kelautan (BBSPGL). <br>
        Sistem Informasi Manajemen PKL.
      </small>
    </div>
  </footer>

  @stack('modals')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

  {{-- LOGIC SWEETALERT DARI TEMPLATE ADMIN ANDA --}}
  @if($message = Session::get('success'))
  <script>
    Swal.fire({
      title: "{{ $message }}",
      icon: "success",
      showConfirmButton: false,
      timer: 2000,
      background: '#ffffff',
    });
  </script>
  @endif
  @if($message = Session::get('error'))
  <script>
    Swal.fire({
      title: "{{ $message }}",
      icon: "error",
      showConfirmButton: false,
      timer: 2000,
      background: '#ffffff',
    });
  </script>
  @endif

  <script src="{{ asset('js/app-script.js') }}"></script>
  @stack('scripts')
</body>

</html>