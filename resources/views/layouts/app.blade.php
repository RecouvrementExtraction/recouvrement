<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', '') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('Css/app.css')}}">
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app" class="d-flex flex-column min-vh-100 mt-1">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm mt-1 imprimer-bouton">
            <div class="mr-auto">
                <img src="/images/log.png" alt="Logo de l'entreprise" height="50" width="100">
            </div>
            <div class="container">
                <a class="navbar-brand custom-register-link" href="{{ url('/home') }}">
                    {{__('Accueil')}}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            {{-- @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif --}}

                            @if (Route::has('register'))
                            <li class="nav-item">
                                <a href="{{ route('register') }}" class="navbar-brand custom-register-link">
                                    {{ __("S'enregistrer") }}
                                </a>
                            </li>
                        @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    @can('manage-users')
                                    <a href="{{route('admin.users.index')}}" class="dropdown-item">List des utilisateurs</a>
                                    @endcan
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __("Déconnexion") }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="flex-grow-1 py-4">
            @yield('content')
        </main>

        <footer class="footer mt-auto py-3 fixed-bottom">
            <div class="container text-center">
                <span class="text-muted">© {{ date('Y') }} <a href="https://igf-sn.com/" target="_blank">IGF-sn</a>. Tous droits réservés.</span>
            </div>
        </footer>
    </div>
    {{--Appel script personnalisé--}}
    {{-- <script src="{{asset('Js/fusion.js')}}"></script> --}}
    <link rel="stylesheet" href="{{asset('Css/monStyle.css')}}">

</body>
</html>
