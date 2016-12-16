<!DOCTYPE html>
<html>
    <head>
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="Cache-Control" content="no-cache">
        <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=2.0">
        <meta name="keywords" content="{{ $keywords or $site['keywords'] }}">
        <meta name="description" content="{{ $description or $site['description'] }}">
        <title>{{ $title or $site['name'] }}</title>
        <script type="text/javascript" src="/lib/jquery/jquery-1.11.1.min.js" charset="utf-8"></script>
        <link href="/css/phone.css" rel="stylesheet">
        @yield('user_css')
    </head>
    <body>
        @yield('header')

        @yield('content')

        @include('parts.footer')

        <a class="go_top" href="javascript:;" onclick="javascript:$('html,body').animate({scrollTop:0},400);return false;"></a>
    </body>

    <!-- Scripts -->
    <script src="/js/app.js"></script>
    @yield('user_js')
</html>
