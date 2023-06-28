<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script>
         try {

                window.Laravel = {!! json_encode([
                    'csrfToken' => csrf_token(),
                    'access_token' => $access_token,
                    'current_user' => Auth::user(),
                ]) !!};

                console.log( window.Laravel.access_token);
                } catch (err) {

                }
    </script>
    @yield('link-header')
</head>
<body>
    <div id="app">
        @include('header')

        <main class="py-4" style="min-height:450px;background-color: #F1F1F1"   >
            @yield('content')
        </main>
        @include('footer')
    </div>  
   
</body>
</html>
