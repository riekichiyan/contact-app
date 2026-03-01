@extends('layouts.guest')

@section('title', 'Register')

@section('content')
<div class="auth-main">
    <div class="auth-card">
        <h2>Register</h2>

        @if ($errors->any())
        <div class="alert">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <label>お名前</label>
            <input type="text" name="name" value="{{ old('name') }}" required>

            <label>メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}" required>

            <label>パスワード</label>
            <input type="password" name="password" required>

            <label>パスワード確認</label>
            <input type="password" name="password_confirmation" required>

            <button type="submit">登録する</button>
        </form>

        <div style="text-align:center; margin-top:14px;">
            <a href="{{ route('login') }}">ログインはこちら</a>
        </div>
    </div>
</div>
@endsection