@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="auth-main">
    <div class="auth-card">
        <h2>Login</h2>

        @if ($errors->any())
        <div class="alert">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <label>メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}" required>

            <label>パスワード</label>
            <input type="password" name="password" required>

            <button type="submit">ログイン</button>
        </form>

        <div style="text-align:center; margin-top:14px;">
            <a href="{{ route('register') }}">新規登録はこちら</a>
        </div>
    </div>
</div>
@endsection