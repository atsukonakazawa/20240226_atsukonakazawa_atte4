<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atte</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header>
        <div class="header-inner">
            <a class="header-logo" href="/">Atte</a>
            <div class="header-nav__group">
                @yield('header-nav')
                </div>
        </div>
    </header>
    <main>
        @yield('content')
        <div class="copyright">
            <small>&copy; Atte, inc.</small>
        </div>
    </main>
</body>
</html>