<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Könyvtár</title>
 
    <!-- Scripts -->
    <!--<script src="{{ asset('js/app.js') }}" defer></script>-->
   
    <!--<script src="{{ asset('js/jquery-3.7.1.js') }}"></script>-->
   
    <!-- Styles -->
    <!--<link rel="stylesheet" href="{{ asset('css/app.css') }}">-->
    <!--<link rel="stylesheet" type="text/css" href="{{ asset('fontawesome/css/all.css') }}" >-->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
 
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}" type="image/x-icon">
</head>
 
<body>
    <header>
        <div class="row">
            
            <nav>
                    <button><a href="{{ route('authors.index') }}">Szerzők</a></button>
                    <button><a href="{{ route('books.index') }}">Könyvek</a></button>
                    <button><a href="{{ route('categories.index') }}">Kategóriák</a></button>
                    @if(session('api_token'))
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" style="cursor: pointer;">Kijelentkezés</button>
                        </form>
                    @else
                        <button><a href="{{ route('login') }}">Bejelentkezés</a></button>
                    @endif
            </nav>
        </div>
    </header>
    <main>
        @if(session('success'))
            <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border: 1px solid #c3e6cb; border-radius: 4px;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 4px;">
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>
 
    <footer>
        <p>&copy; Király Gábor - Praszna Koppány - Nagy Gergely - 2025</p>
    </footer>
 
</body>
 
</html>