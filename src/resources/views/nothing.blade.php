@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/nothing.css') }}">
@endsection

@section('header-nav')
<div class="header-nav__group">
    <ul class="header-nav">
        @if (Auth::check())
        <li>
            <a href="/">ホーム</a>
        </li>
        <li>
            <form class="attendance-form" action="{{ route('form.send-day' )}}" method="get">
                <button class="attendance-button">日付一覧</button>
                <input type="hidden" name="send_day" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
            </form>
        </li>
        <li>
            <a href="/userlist">会員一覧</a>
        </li>
        <li>
            <form class="logout-form" action="/logout" method="post">
                @csrf
                <button class="logout-button">
                    ログアウト
                </button>
            </form>
        </li>
        @endif
    </ul>
</div>
@endsection

@section('content')
<div class="nothing-content">
    <div class="nothing-message">
        <p class="nothing-p">
            該当の勤務情報はありませんでした
        </p>
    </div>
</div>
@endsection