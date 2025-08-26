<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('backend-assets/css/app.css') }}">

    <!-- External CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    {{-- font awesom icon --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    {{-- for selec2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css">

    <!-- Include Date Range Picker -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


    {{-- jQuery & DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.3/css/dataTables.dataTables.css">
    {{-- <link rel="stylesheet" href="https://cdn.datatables    .net/1.13.6/css/jquery.dataTables.min.css"> --}}

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

    <!-- Template Main JS File -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- use for drag and droptable --}}
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>

    {{-- js for sweet alert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- for select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>


    {{-- jQuery & DataTables --}}
    <script src="https://cdn.datatables.net/2.3.3/js/dataTables.js"></script>

    {{-- for select2 --}}
    <script>
        $(document).ready(function() {
            $('select[data-control="select2"]').select2({
                placeholder: "Search or select an option",
                allowClear: true // Optional: adds a clear (×) button
            });
        });
    </script>


    <script>
        $(document).ready(function() {

            $('#example').DataTable({
                colReorder: true,
                scrollX: true, // Enable horizontal scroll
                scrollY: '400px',
                scrollCollapse: true,
                paging: true, // or false if you don't want pagination
                fixedHeader: true
            });
            $('.example').DataTable({
                colReorder: true,
                scrollX: true, // Enable horizontal scroll
                scrollY: '400px',
                scrollCollapse: true,
                paging: true, // or false if you don't want pagination
                fixedHeader: true
            });

        });
    </script>

    @stack('scripts')
</body>

</html>
