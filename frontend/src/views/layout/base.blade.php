<!doctype html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{config('app.name')}}</title>

    <!-- Fonts -->

    <!-- Styles -->
    <link rel="stylesheet" href="{{asset('vendor/lpuddu/laravel-translations-dashboard/css/app.css', true)}}">
    @yield('page-styles')

    <!-- Scripts -->
    <script type="application/javascript" src="{{asset('vendor/lpuddu/laravel-translations-dashboard/js/manifest.js')}}"></script>
    <script type="application/javascript" src="{{asset('vendor/lpuddu/laravel-translations-dashboard/js/vendor.js')}}"></script>
    <script type="application/javascript" src="{{asset('vendor/lpuddu/laravel-translations-dashboard/js/app.js')}}" async></script>
    @yield('page-head-scripts')
</head>
<body class="sidebar-toggled">
<input id="page" type="hidden" value="@yield('page')">

<div id="sidebar" class="transition">
    @include('laravel-translations-dashboard::layout.sidebar')
</div>

<div id="page-container" class="transition">
    <div id="page-header" class="transition">
        @include('laravel-translations-dashboard::layout.header')
    </div>

    <div id="page-content" class="transition">
        @yield('content')
    </div>
</div>

<div id="overlay"></div>

@yield('page-body-scripts')
</body>
</html>