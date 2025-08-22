<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- External CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('backend-assets/css/app.css') }}">

    @stack('styles')
</head>

<body class="font-sans bg-[#f8f5f0]">
    @include('backend.partials.sidebar')

    <div class="md:ml-64 min-h-screen transition-all duration-300">
        @include('backend.partials.header')

        @yield('content')

        @include('backend.partials.footer')
    </div>

    <!-- Custom JavaScript -->
    <script src="{{ asset('backend-assets/js/app.js') }}"></script>
    @stack('scripts')
</body>

</html>
