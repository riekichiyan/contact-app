@extends('layouts.app')

@section('title', 'Admin')

@section('content')
<div class="admin-wrap">

    <h2 class="page-title">管理者画面</h2>

    <form method="GET" action="{{ route('admin.contacts.index') }}" class="filter-form">
        <div class="filter-row">
            <input class="f-input" type="text" name="keyword"
                placeholder="名前やメールアドレスを入力してください"
                value="{{ request('keyword') }}">

            <select class="f-select" name="gender">
                <option value="">性別</option>
                <option value="1" @selected(request('gender')==='1' )>男性</option>
                <option value="2" @selected(request('gender')==='2' )>女性</option>
                <option value="3" @selected(request('gender')==='3' )>その他</option>
            </select>

            <select class="f-select f-select-wide" name="category_id">
                <option value="">お問い合わせの種類</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected((string)request('category_id')===(string)$cat->id)>
                    {{ $cat->content }}
                </option>
                @endforeach
            </select>

            <input class="f-date" type="date" name="from" value="{{ request('from') }}">
            <span class="f-tilde">〜</span>
            <input class="f-date" type="date" name="to" value="{{ request('to') }}">

            <button type="submit" class="admin-btn btn-search">検索</button>
        </div>
    </form>

    <div class="admin-actions-row">
        <div class="admin-actions-left">
            <a href="{{ route('admin.contacts.index') }}" class="btn btn-reset">リセット</a>
            <a href="{{ route('admin.contacts.export', request()->query()) }}" class="btn btn-export">エクスポート</a>
        </div>

        <div class="admin-pagination-wrapper">
            {{ $contacts->onEachSide(1)->links() }}
        </div>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>お名前</th>
                <th>性別</th>
                <th>メールアドレス</th>
                <th>お問い合わせの種類</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contacts as $c)
            @php
            $genderText = match ((string)$c->gender) {
            '1' => '男性',
            '2' => '女性',
            '3' => 'その他',
            default => '',
            };
            @endphp
            <tr>
                <td>{{ $c->last_name }} {{ $c->first_name }}</td>
                <td class="td-center">{{ $genderText }}</td>
                <td>{{ $c->email }}</td>
                <td>{{ optional($c->category)->content }}</td>
                <td class="td-center">
                    <button type="button" class="js-detail" data-id="{{ $c->id }}">詳細</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{--  モーダル --}}
<div id="detailModal" class="modal-backdrop" style="display:none;">
    <div class="modal-panel">
        <button id="modalClose" type="button" class="modal-close">×</button>
        <h3 class="modal-title">お客様情報</h3>
        <div id="modalBody"></div>

        <form id="deleteForm" method="POST" class="modal-delete">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-delete">消去</button>
        </form>
    </div>
</div>
<form method="POST" action="{{ route('logout') }}" class="logout-fixed">
    @csrf
    <button type="submit">ログアウト</button>
</form>
@endsection