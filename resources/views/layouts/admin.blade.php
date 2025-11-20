<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <!-- =============================================== -->
    <!-- STYLE/CSS AREA                                  -->
    <!-- =============================================== -->
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    {{-- Menambahkan CSS untuk Tom-select --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app-style.css') }}">
    @stack('style')
</head>
<body>
    <div class="main-layout">
        <!-- =============================================== -->
        <!-- SIDEBAR                                         -->
        <!-- =============================================== -->
        @include('admin.partials._sidebar')


        <!-- =============================================== -->
        <!-- MAIN CONTENT AREA                               -->
        <!-- =============================================== -->
        <div id="main-content">
            @include('admin.partials._header')
            @yield('content')
            @include('admin.partials._footer')
        </div> 
    </div>
        @stack('modals')

    <!-- =============================================== -->
    <!-- JS AREA                                         -->
    <!-- =============================================== -->
    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- Menambahkan JavaScript untuk Tom-select --}}
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
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
