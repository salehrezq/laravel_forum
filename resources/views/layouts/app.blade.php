<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{asset('css/mycss.css')}}"/>
    </head>
    <body>
        <div id="app">
            <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
                <div class="container">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle menu-channels-link" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Browse
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="{{ route('threads.index') }}">All threads</a>
                            @if(auth()->check())
                            <a class="dropdown-item" href="{{ route('threads.index', ['user_id' => auth()->user()->id])}}">My threads</a>
                            @endif
                            <a class="dropdown-item" href="{{ route('threads.index', ['popular'])}}">Popular threads</a>
                            <a class="dropdown-item" href="{{ route('threads.index', ['unanswered'])}}">Unanswered threads</a>
                        </div>
                    </div>
                    <a class="navbar-brand" href="{{route('threads.create')}}">Create New Thread</a>
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle menu-channels-link" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Channels
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @foreach ($channels as $channel)
                            <a class="dropdown-item" href="{{route('threads.channel', ['channelSlug' => $channel->slug])}}">{{$channel->slug}}</a>
                            @endforeach
                        </div>
                    </div>
                    @auth
                        @include('notifications.replies_notifications')
                    @endauth
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav mr-auto">

                        </ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ml-auto">
                            <!-- Authentication Links -->
                            @guest
                            <li><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                            <li><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                            @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                               document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>

            <main class="py-4">
                @yield('content')
            </main>
        </div>
        <div class="flashDiv">
            @if(session('flash'))
            <div class="alert alert-success redirect-alert" role="alert">
                {{session('flash')}}
            </div>
            @endif
        </div>

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}"></script>
        <script src="{{asset('js/axios.js')}}"></script>
        <script src="{{asset('js/lodash.js')}}"></script>
        <script src="{{asset('js/moment.js')}}"></script>
        <script src="{{asset('js/myjs.js')}}"></script>
    </body>
</html>
