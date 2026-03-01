@extends('layouts.app')

@section('title', 'お問い合わせ一覧')
@section('heading', 'お問い合わせ一覧')

@section('header_actions')
<th style="padding:10px; text-align:left;">作成日</th>
<a class="btn btn-primary" href="{{ route('contacts.create') }}">新規作成</a>
@endsection

@section('content')
<div class="card">
    <div class="muted" style="margin-bottom:10px;">
        登録件数：{{ $contacts->count() }}件
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>カテゴリ</th>
                    <th>氏名</th>
                    <th>メール</th>
                    <th>電話</th>
                    <th>住所</th>
                    <th>内容</th>
                    <th>作成日</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contacts as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td><span class="badge">{{ $c->category?->name ?? '未設定' }}</span></td>
                    <td>{{ $c->first_name }} {{ $c->last_name }}</td>
                    <td>{{ $c->email }}</td>
                    <td>{{ $c->tel }}</td>
                    <td>
                        {{ $c->address }}
                        @if($c->building)
                        <div class="muted">{{ $c->building }}</div>
                        @endif
                    </td>
                    <td style="max-width:240px; white-space:pre-wrap;">{{ $c->detail }}</td>
                    <td class="muted">{{ $c->created_at?->format('Y-m-d H:i') }}</td>
                    <td style="padding:10px;">
                        {{ optional($c->created_at)->format('Y-m-d H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="muted">まだデータがありません。右上の「新規作成」から登録してね。</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection