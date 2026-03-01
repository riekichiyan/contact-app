<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

    <!-- ===== ヘッダー（中央Miracle） ===== -->
    <header class="guest-header">
        <div class="guest-header__inner">
            <img class="guest-header__logo" src="{{ asset('miracle.svg') }}" alt="Miracle">
        </div>
    </header>

    <!-- ===== メイン ===== -->
    <main class="auth-main">
        <div class="auth-card">
            @yield('content')
        </div>
    </main>

</body>

</html>