<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="form-page-bg">
    <div class="public-page">
        <div class="public-page-locale">
            <x-locale-theme :show-theme="false" />
        </div>
        @yield('body')
    </div>
</body>
</html>
