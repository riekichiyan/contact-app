@extends('layouts.guest')

@section('content')
<div class="thanks-page">
    <div class="thanks-card">
        <div class="thanks-big">THANK YOU</div>

        <h1 class="thanks-title">送信完了</h1>

        <p class="thanks-text">
            お問い合わせありがとうございました。<br>
            内容を確認のうえ、担当よりご連絡いたします。
        </p>

        <a href="{{ route('contacts.create') }}" class="thanks-btn">
            フォームへ戻る
        </a>
    </div>
</div>
@endsection