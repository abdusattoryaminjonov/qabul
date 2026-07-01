<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>@yield('title', __('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/public-form-page.js'])
    @stack('head')
</head>
<body class="form-page-bg form-page-light">
    <header class="public-topbar">
        <div class="public-topbar-inner">
            <x-brand-logo variant="public" />
            <div class="public-page-locale">
                <x-locale-theme :show-theme="false" />
            </div>
        </div>
    </header>
    <div class="public-page">
        @yield('body')
    </div>
</body>
</html>
