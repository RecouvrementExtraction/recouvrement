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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="{{ asset('/build/assets/app-041e359a.css') }}">
    <script src="/build/assets/app-70a4cfe7.js"></script>
    <link rel="stylesheet" href="{{ asset('Css/app.css') }}">
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        /* Style général pour les boutons de la navbar */
        .navbar-nav .nav-link {
            padding: 10px 20px;
            transition: background-color 0.3s, color 0.3s;
            border-radius: 5px; /* Ajout de la bordure arrondie */
        }

        /* Style au survol pour les trois premiers boutons */
        .navbar-nav .nav-item:nth-child(-n+3) .nav-link:hover {
            background-color: #171f916c; /* Couleur de fond sur survol */
            color: #020202; /* Couleur de texte sur survol */
            border-radius: 5px; /* Ajout de la bordure arrondie */
        }

        /* Style personnalisé pour le bouton d'accueil */
        .navbar-nav .nav-item:first-child .nav-link:hover {
            background-color: #171f916c;
            color: #050505;
            border-radius: 5px; /* Ajout de la bordure arrondie */
        }
    </style>



</head>
<body>
    <div id="app" class="d-flex flex-column min-vh-100">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm imprimer-bouton fixed-top">
            <div>
                <img src="/images/igf.png" alt="Logo de l'entreprise" height="50" width="100">
            </div>
            <div class="container">
                {{-- <a class="navbar-brand custom-register-link" href="{{ url('/home') }}">
                    <i class="bi bi-house-door"></i>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button> --}}

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Use flexbox to evenly distribute space between items -->
                    <ul class="navbar-nav me-auto d-flex justify-content-between w-100">
                        <!-- Left Side Of Navbar -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/home') }}">
                                <i class="bi bi-house-door"></i> Accueil
                            </a>
                        </li>

                        @auth
                            <!-- New buttons visible only to authenticated users -->
                            <li class="nav-item">
                                <a class="nav-link" href="/client_recouvre/{{ auth()->user()->id }}">
                                    <i class="bi-cash-stack"></i>factures
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/client_rappel/{{ auth()->user()->id }}">
                                    <i class="bi-chat-dots"></i> rappels
                                </a>
                            </li>
                        @endauth

                        {{-- @guest
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a href="{{ route('register') }}" class="nav-link custom-register-link">
                                        {{ __("S'enregistrer") }}
                                    </a>
                                </li>
                            @endif
                        @endguest --}}

                        @auth
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="bi-person"></i>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    @can('manage-users')
                                        <a href="{{route('admin.users.index')}}" class="dropdown-item">Liste des utilisateurs</a>
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
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
        <main class="flex-grow-1 mt-5"  style="padding-top: 70px;">
            @yield('content')
        </main>


            <div class="container text-center">
                <span class="text-muted">© {{ date('Y') }} <a href="https://igf-sn.com/" target="_blank">IGF-sn</a>. Tous droits réservés.</span>
            </div>
    </div>

    <link rel="stylesheet" href="{{ asset('Css/monStyle.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

</body>
</html>
