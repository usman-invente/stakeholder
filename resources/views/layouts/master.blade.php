<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Responsive styles */
        body, html {
            overflow-x: hidden;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        @media (max-width: 767.98px) {
            #sidebar-container {
                display: none !important;
            }
            
            .card-body {
                padding: 1rem;
            }
        }
        
        /* Dropdown styles for all screen sizes */
        .dropdown-menu {
            position: absolute !important;
            right: 0 !important;
            left: auto !important;
            max-width: 90vw;
            border: 1px solid rgba(0,0,0,.15);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
            background-color: #fff;
            z-index: 1030;
        }
        
        /* Ensure dropdown is visible when toggled */
        .dropdown-menu.show {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Ensure dropdown items are properly clickable */
        .dropdown-item {
            padding: 0.5rem 1rem;
            clear: both;
            font-weight: 400;
            text-align: inherit;
            white-space: normal; /* Allow text to wrap */
            border: 0;
        }
        
        /* Better dropdown positioning on tablets */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .dropdown-menu {
                max-width: 300px;
            }
            
            .navbar-nav .dropdown-menu {
                right: 0 !important;
                left: auto !important;
            }
            
            /* Ensure the navbar is fully expanded on tablets */
            .navbar-expand-md .navbar-collapse {
                display: flex !important;
                flex-basis: auto;
            }
            
            .navbar-expand-md .navbar-toggler {
                display: none;
            }
        }
        
        @media (min-width: 768px) {
            .navbar-nav.d-md-none,
            .navbar-toggler.d-md-none {
                display: none !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="h-100">
    <div class="d-flex flex-column flex-md-row h-100">
        <!-- Sidebar - visible on medium screens and up only -->
        <div id="sidebar-container" class="d-md-block">
            @include('layouts.partials.sidebar')
        </div>

        <div class="flex-grow-1 overflow-auto">
            <!-- Top Bar -->
            @include('layouts.partials.topbar')

            <!-- Main Content -->
            <main class="p-3 p-md-4">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all dropdowns with appropriate configuration
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl, {
                    boundary: document.querySelector('body'), // Use body as boundary
                    popperConfig: {
                        // Make sure Popper positions the dropdown correctly
                        modifiers: [{
                            name: 'preventOverflow',
                            options: {
                                boundary: 'viewport',
                                padding: 10
                            }
                        }]
                    }
                });
            });
            
            // Fix for dropdown on all screen sizes
            const userDropdown = document.getElementById('navbarDropdown');
            if (userDropdown) {
                // Ensure dropdown starts closed
                const dropdownMenu = userDropdown.nextElementSibling;
                if (dropdownMenu) {
                    dropdownMenu.classList.remove('show');
                }
                
                userDropdown.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevent default action
                    e.stopPropagation(); // Stop event bubbling
                    
                    // Toggle the dropdown menu
                    const dropdownMenu = this.nextElementSibling;
                    if (dropdownMenu) {
                        dropdownMenu.classList.toggle('show');
                        
                        // Set maximum height and allow scrolling for long menus
                        dropdownMenu.style.maxHeight = `${window.innerHeight - 100}px`;
                        dropdownMenu.style.overflowY = 'auto';
                        
                        // Make sure dropdown is visible in the viewport
                        const rect = dropdownMenu.getBoundingClientRect();
                        const viewportWidth = window.innerWidth;
                        
                        if (rect.right > viewportWidth) {
                            // If the dropdown extends beyond the right edge, adjust it
                            dropdownMenu.style.right = '0';
                            dropdownMenu.style.left = 'auto';
                        }
                        
                        // Add specific styling for tablets
                        if (window.innerWidth >= 768 && window.innerWidth < 992) {
                            dropdownMenu.style.minWidth = '250px';
                            dropdownMenu.style.fontSize = '14px';
                        }
                    }
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!userDropdown.contains(e.target)) {
                        const dropdownMenu = userDropdown.nextElementSibling;
                        if (dropdownMenu && dropdownMenu.classList.contains('show')) {
                            dropdownMenu.classList.remove('show');
                        }
                    }
                });
            }
            
            // Fix for navbar toggler - let Bootstrap handle it with data attributes
            const navbarToggler = document.querySelector('.navbar-toggler');
            const navbarCollapse = document.getElementById('navbarSupportedContent');
            
            if (navbarToggler && navbarCollapse) {
                // Also close the menu when a nav item is clicked
                const navItems = document.querySelectorAll('.navbar-collapse .nav-link:not(.dropdown-toggle)');
                navItems.forEach(item => {
                    item.addEventListener('click', () => {
                        if (navbarCollapse.classList.contains('show') && window.innerWidth < 992) {
                            const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                            if (bsCollapse) {
                                bsCollapse.hide();
                            }
                        }
                    });
                });
            }
            
            // Handle table responsiveness
            const tables = document.querySelectorAll('table');
            tables.forEach(table => {
                if (!table.closest('.table-responsive')) {
                    const wrapper = document.createElement('div');
                    wrapper.classList.add('table-responsive');
                    table.parentNode.insertBefore(wrapper, table);
                    wrapper.appendChild(table);
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
