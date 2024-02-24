@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('header-nav')
<div class="header-nav__group">
    <ul class="header-nav">
        <li>
            <a href="/">ホーム</a>
        </li>
        <li>
            <form action="{{ route('form.send-day' )}}" method="get">
                <a href="/attendance">日付一覧</a>
                <input type="hidden" name="send_day" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
            </form>
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
<div class="attendance-content">
    <div class="attendance-content__title">
        <form class="day-before" action="{{ route('form.dayBefore' )}}" method="get">
            <button class="day-before__button" type="submit">
                <
            </button>
            <input type="hidden" name="date" value="{{ $date }}">
        </form>

        <h2>
            {{ $date }}
        </h2>
        <form class="next-day" action="{{ route('form.nextDay' )}}" method="get">
            <button class="next-day__button" type="submit">
                >
            </button>
            <input type="hidden" name="date" value="{{ $date }}">
        </form>
    </div>
    <div class="attendance-content__table">
        <table class="attendance-content__table-item">
            <tr class="title-row">
                <th>
                    名前
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
                    勤務時間
                </th>
            </tr>
            @foreach($workItems as $workItem)
            <tr class="each-row">
                <td>
                    {{ $workItem->user->name }}
                </td>
                <td>
                    {{ $workItem->workIn }}
                </td>
                <td>
                    {{ $workItem->workOut }}
                </td>
                <td>
                    {{ $workItem->sumBreaktime }}
                </td>
                <td>
                    {{ $workItem->actualWorkTime }}
                </td>
            </tr>
            @endforeach
        </table>
        <div class="pagination">
            {{ $workItems->appends(request()->input())->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection