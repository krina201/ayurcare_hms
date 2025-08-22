// Tailwind CSS Configuration
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
            },
            fontFamily: {
                'nunito': ['Nunito', 'sans-serif'],
                'sans': ['Inter', 'sans-serif']
            }
        }
    }
};

// Font Awesome Configuration
window.FontAwesomeConfig = {
    autoReplaceSvg: 'nest'
};

// Sidebar functionality
document.addEventListener('DOMContentLoaded', function () {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');

    if (sidebarToggle && sidebar) {
        // Toggle sidebar on button click
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('-translate-x-full');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (event) {
            if (window.innerWidth < 768 &&
                !sidebar.contains(event.target) &&
                !sidebarToggle.contains(event.target) &&
                !sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.add('-translate-x-full');
            }
        });
    }
});