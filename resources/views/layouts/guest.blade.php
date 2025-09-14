<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Visitor Registration</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Custom styles -->
        <style>
            body {
                background-color: #f8f9fa;
                font-family: 'figtree', sans-serif;
            }
            .navbar-brand img {
                height: 40px;
                margin-right: 10px;
            }
            .container {
                margin-top: 2rem;
            }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="/">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'Laravel') }} Logo">
                    {{ config('app.name', 'Laravel') }}
                </a>
            </div>
        </nav>

        <main>
            @yield('content')
        </main>
        
        <footer class="footer mt-5 py-3 bg-light">
            <div class="container text-center">
                <span class="text-muted">© {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</span>
            </div>
        </footer>
        
        <!-- Bootstrap JS Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        
        @yield('scripts')
    </body>
</html>
</html>
