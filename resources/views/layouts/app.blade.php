<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    
    <link rel="stylesheet" href="{{asset('css/bootstrap.4.6.2.css')}}" />
    <link rel="stylesheet" href="{{asset('css/main_style.css')}}" />
    <!-- Scripts -->

    <script src="{{asset('js/bootstrap.4.6.2.js')}}"></script>
    <script src="{{asset('jquery_v3.5.1.js')}}"></script>
</head>
<body>
    <div id="app">        
        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
