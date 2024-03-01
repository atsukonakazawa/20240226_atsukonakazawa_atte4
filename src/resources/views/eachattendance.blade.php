@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/eachattendance.css') }}">
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
<div class="eachattendance-content">
    <div class="eachattendance__title">
        @foreach($eachUsers as $eachUser)
        <h2>
            勤怠表
        </h2>
        @endforeach
    </div>
    <div class="monthly-outer">
        <p class="monthly-nameLabel">
            氏名：
        </p>
        <p class="monthly-name">
            {{ $eachUser->name }}
        </p>
        <p class="monthly-timeLabel">
            月間実働時間合計:
        </p>
        <p class="monthly-time">
            {{ $maxRecord->monthly }}
        </p>
    </div>
    <div class="message-outer">
        <p class="message">
        @if(session('message'))
            {{ session('message')}}
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
            {{ $error }}
            @endforeach
        @endif
        </p>
    </div>
    <div class="select-month">
        <form class="month-before" action="{{ route('form.monthBefore' )}}" method="get">
            <button class="month-before__button" type="submit">
                <
            </button>
            <input type="hidden" name="YearMonth" value="{{ $YearMonth }}">
            @foreach($eachUsers as $eachUser)
            <input type="hidden" name="eachUserId" value="{{ $eachUser->id }}">
            @endforeach
        </form>
        <p class="month">
            {{ $YearMonth }}
        </p>
        <form class="next-month" action="{{ route('form.nextMonth' )}}" method="get">
            <button class="next-month__button" type="submit">
                >
            </button>
            <input type="hidden" name="YearMonth" value="{{ $YearMonth }}">
            @foreach($eachUsers as $eachUser)
            <input type="hidden" name="eachUserId" value="{{ $eachUser->id }}">
            @endforeach
        </form>
    </div>
    <div class="eachattendance__table">
        <table class="eachattendance__table-item">
            <tr class="title-row">
                <th>
                    勤務日
                </th>
                <th>
                    勤務開始
                </th>
                <th>
                    勤務終了
                </th>
                <th>
                    休憩時間
                </th>
                <th>
                    実働時間
                </th>
            </tr>
            @foreach($eachUserWorks as $eachUserWork)
            <tr class="each-row">
                <td>
                    {{ $eachUserWork->workDate }}
                </td>
                <td>
                    {{ $eachUserWork->workIn }}
                </td>
                <td>
                    {{ $eachUserWork->workOut }}
                </td>
                <td>
                    {{ $eachUserWork->sumBreaktime }}
                </td>
                <td>
                    {{ $eachUserWork->actualWorkTime }}
                </td>
            </tr>
            @endforeach
        </table>
        <div class="pagination">
            {{ $eachUserWorks->appends(request()->input())->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection