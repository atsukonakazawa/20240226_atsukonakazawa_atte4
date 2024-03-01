@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/userlist.css') }}">
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
<div class="userlist-content">
    <div class="userlist__title">
        <h2>
            会員一覧
        </h2>
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
    <div class="userlist__table">
        <table class="userlist__table-item">
            <tr class="title-row">
                <th class="id-th">
                    会員番号
                </th>
                <th class="name-th">
                    名前
                </th>
                <th class="button-th">
                </th>
            </tr>
            @foreach($users as $user)
            <tr class="each-row">
                <td class="id-td">
                    {{ $user->id }}
                </td>
                <td class="name-td">
                    {{ $user->name }}
                </td>
                <td class="button-td">
                    <form class="eachAttendance-form" action="{{ route('form.send-month' )}}" method="get">
                        <button class="eachAttendance-button">勤怠表</button>
                        <input type="hidden" name="send_month" value="{{ \Carbon\Carbon::now()->format('Y-m-01') }}">
                        <input type="hidden" name="eachUserId" value="{{ $user->id }}">

                    </form>
                </td>
            </tr>
            @endforeach
        </table>
        <div class="pagination">
            {{ $users->appends(request()->input())->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection