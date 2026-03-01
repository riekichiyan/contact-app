<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
</head>

<body class="{{ request()->is('admin/*') ? 'is-admin' : 'is-guest' }}">

    <header class="header">
        <div class="logo">
            <img src="{{ asset('Miracle.svg') }}" alt="Miracle">
        </div>
    </header>

    <main class="main {{ request()->is('admin/*') ? 'main-admin' : '' }}">
        @yield('content')
    </main>

    {{-- （モーダル用）：最下部・</body>の直前 --}}
    <script src="{{ asset('js/admin-modal.js') }}?v={{ time() }}"></script>

</body>

</html>