@extends('layouts.guest')
@section('title', 'お問い合わせ')

@section('content')
<div class="container">
    <h1 class="page-title">お問い合わせ</h1>

    @if ($errors->any())
    <div style="background:#fee; border:1px solid #f99; padding:12px; border-radius:10px; margin-bottom:14px;">
        <strong>入力に不備があります：</strong>
        <ul style="margin:8px 0 0; padding-left:18px;">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('contacts.confirm') }}">
        @csrf

        <div class="form-row">
            <div class="label">お名前 <span class="req">必須</span></div>
            <div class="grid-2">
                <input class="input" type="text" name="last_name" placeholder="姓" value="{{ old('last_name', $draft['last_name'] ?? '') }}">
                <input class="input" type="text" name="first_name" placeholder="名" value="{{ old('first_name', $draft['first_name'] ?? '') }}">
            </div>
        </div>
        <div class="form-row">
            <div class="label">性別 <span class="req">必須</span></div>

            <div class="radio-group">
                <label class="gender-item">
                    <input type="radio" name="gender" value="1"
                        {{ old('gender', $draft['gender'] ?? '') == '1' ? 'checked' : '' }}>
                    <span class="gender-text">男性</span>
                </label>

                <label class="gender-item">
                    <input type="radio" name="gender" value="2"
                        {{ old('gender', $draft['gender'] ?? '') == '2' ? 'checked' : '' }}>
                    <span class="gender-text">女性</span>
                </label>

                <label class="gender-item">
                    <input type="radio" name="gender" value="3"
                        {{ old('gender', $draft['gender'] ?? '') == '3' ? 'checked' : '' }}>
                    <span class="gender-text">その他</span>
                </label>
            </div>
        </div>

        <div class="form-row">
            <div class="label">メール <span class="req">必須</span></div>
            <input class="input" type="email" name="email" placeholder="test@example.com" value="{{ old('email', $draft['email'] ?? '') }}">
        </div>

        <div class="form-row">
            <div class="label">電話<span class="req">必須</span></div>
            <input class="input" type="text" name="tel" value="{{ old('tel', $draft['tel'] ?? '') }}">
        </div>

        <div class="form-row">
            <div class="label">住所<span class="req">必須</span></div>
            <input class="input" type="text" name="address" value="{{ old('address', $draft['address'] ?? '') }}">
        </div>

        <div class="form-row">
            <div class="label">建物名</div>
            <input class="input" type="text" name="building" value="{{ old('building', $draft['building'] ?? '') }}">
        </div>

        <div class="form-row">
            <div class="label">お問い合わせ種別<span class="req">必須</span></div>
            <select class="select" name="category_id" required>
                <option value="">選択してください</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}"
                    {{ old('category_id', $draft['category_id'] ?? '') == $category->id ? 'selected' : '' }}>
                    {{ $category->content }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="form-row">
            <div class="label" style="font-weight:bold;">内容 <span class="req">必須</span></div>

            <textarea
                class="textarea {{ $errors->has('detail') ? 'is-invalid' : '' }}"
                name="detail"
                rows="5">{{ old('detail', $draft['detail'] ?? '') }}</textarea>
            
        </div>
        <div style="margin-top:18px;">
            <button type="submit" class="btn">確認へ</button>
        </div>

    </form>
</div>
@endsection