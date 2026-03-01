@extends('layouts.app')

@section('title', 'Confirm')

@section('content')
<div class="container">
    <h2 class="page-title">確認画面</h2>

    <div class="confirm">

        <div class="confirm__row">
            <div class="confirm__label">お名前</div>
            <div class="confirm__value">
                {{ $inputs['last_name'] ?? '' }} {{ $inputs['first_name'] ?? '' }}
            </div>
        </div>

        <div class="confirm__row">
            <div class="confirm__label">性別</div>
            <div class="confirm__value">
                @php
                $genderText = ['1'=>'男性','2'=>'女性','3'=>'その他'][$inputs['gender'] ?? ''] ?? '';
                @endphp
                {{ $genderText }}
            </div>
        </div>

        <div class="confirm__row">
            <div class="confirm__label">メール</div>
            {{ $inputs['email'] ?? '' }}
        </div>

        <div class="confirm__row">
            <div class="confirm__label">電話</div>
            <div class="confirm__value">{{ $inputs['tel'] ?? '' }}</div>
        </div>

        <div class="confirm__row">
            <div class="confirm__label">住所</div>
            <div class="confirm__value">{{ $inputs['address'] ?? '' }}</div>
        </div>

        <div class="confirm__row">
            <div class="confirm__label">建物名</div>
            <div class="confirm__value">{{ $inputs['building'] ?? '' }}</div>
        </div>

        <div class="confirm__row">
            <div class="confirm__label">種別</div>
            <div class="confirm__value">
                {{ $categoryLabel ?? '' }}
            </div>
        </div>

        <div class="confirm__row">
            <div class="confirm__label">内容</div>
            <div class="confirm__value">
                {!! nl2br(e($inputs['detail'])) !!}
            </div>
        </div>

    </div>

    <div class="btn-group">
        <form method="POST" action="{{ route('contacts.store') }}">
            @csrf

            @foreach($inputs as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach

            <div class="btn-group">
                <a href="{{ route('contacts.create') }}" class="btn btn-back">修正する</a>
                <button type="submit" class="btn btn-submit">送信する</button>
            </div>

        </form>
    </div>
</div>
@endsection