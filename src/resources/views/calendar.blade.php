@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/calendar.css') }}">
@endsection

@section('header-nav')
<div class="header-nav__group">
    <ul class="header-nav">
        <li>
            <a href="/">ホーム</a>
        </li>
        <li>
            <a href="/calendar">日付選択</a>
        </li>
        @if (Auth::check())
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
<div class="calendar-content">
    <div class="calendar-content__title">
        <form action="{{ route('form.send-day' )}}" method="get">
        @csrf
            <h2>
                日にちを選択してください
            </h2>
            <input class="date" type="date" name="send_day" >
            <button class="calendar-button__submit" type="submit">
                勤務記録を見る
            </button>
        </form>
    </div>
</div>
@endsection