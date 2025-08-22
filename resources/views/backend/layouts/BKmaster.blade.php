<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        tailwind = window.tailwind || {};
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'ayur-green': {
                            100: '#e9f0ea',
                            200: '#c8dccb',
                            300: '#a7c8ac',
                            400: '#86b48d',
                            500: '#65a06e',
                            600: '#518c59',
                            700: '#3d7844',
                            800: '#29642f',
                            900: '#15501a'
                        },
                        'ayur-yellow': {
                            100: '#fff8e1',
                            200: '#ffecb3',
                            300: '#ffe082',
                            400: '#ffd54f',
                            500: '#ffca28',
                            600: '#ffb300',
                            700: '#ffa000',
                            800: '#ff8f00',
                            900: '#ff6f00'
                        },
                        'ayur-brown': {
                            100: '#efebe9',
                            200: '#d7ccc8',
                            300: '#bcaaa4',
                            400: '#a1887f',
                            500: '#8d6e63',
                            600: '#795548',
                            700: '#6d4c41',
                            800: '#5d4037',
                            900: '#4e342e'
                        },
                        'ayur-offwhite': '#f8f5f0'
                    }
                }
            }
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        window.FontAwesomeConfig = {
            autoReplaceSvg: 'nest'
        };
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
    <style>
        ::-webkit-scrollbar {
            display: none;
        }

        html,
        body {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <script>
        tailwind.config = {
            "theme": {
                "extend": {
                    "colors": {
                        "ayur-green": {
                            "100": "#e9f0ea",
                            "200": "#c8dccb",
                            "300": "#a7c8ac",
                            "400": "#86b48d",
                            "500": "#65a06e",
                            "600": "#518c59",
                            "700": "#3d7844",
                            "800": "#29642f",
                            "900": "#15501a"
                        },
                        "ayur-yellow": {
                            "100": "#fff8e1",
                            "200": "#ffecb3",
                            "300": "#ffe082",
                            "400": "#ffd54f",
                            "500": "#ffca28",
                            "600": "#ffb300",
                            "700": "#ffa000",
                            "800": "#ff8f00",
                            "900": "#ff6f00"
                        },
                        "ayur-brown": {
                            "100": "#efebe9",
                            "200": "#d7ccc8",
                            "300": "#bcaaa4",
                            "400": "#a1887f",
                            "500": "#8d6e63",
                            "600": "#795548",
                            "700": "#6d4c41",
                            "800": "#5d4037",
                            "900": "#4e342e"
                        },
                        "ayur-offwhite": "#f8f5f0"
                    },
                    "fontFamily": {
                        "nunito": [
                            "Nunito",
                            "sans-serif"
                        ],
                        "sans": [
                            "Inter",
                            "sans-serif"
                        ]
                    }
                }
            }
        };
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif !important;
        }

        /* Preserve Font Awesome icons */
        .fa,
        .fas,
        .far,
        .fal,
        .fab {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands" !important;
        }
    </style>
    <style>
        .highlighted-section {
            outline: 2px solid #3F20FB;
            background-color: rgba(63, 32, 251, 0.1);
        }

        .edit-button {
            position: absolute;
            z-index: 1000;
        }

        ::-webkit-scrollbar {
            display: none;
        }

        html,
        body {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    @stack('styles')


</head>

<body class="font-sans bg-[#f8f5f0]">
    @include('backend.partials.sidebar')

    <div class="md:ml-64 min-h-screen transition-all duration-300">
        @include('backend.partials.header')

        @yield('content')

        @include('backend.partials.footer')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('-translate-x-full');
                });
                document.addEventListener('click', function(event) {
                    if (window.innerWidth < 768 && !sidebar.contains(event.target) && !sidebarToggle
                        .contains(event.target) && !sidebar.classList.contains('-translate-x-full')) {
                        sidebar.classList.add('-translate-x-full');
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
