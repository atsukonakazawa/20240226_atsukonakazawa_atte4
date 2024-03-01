@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('header-nav')
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
@endsection

@section('content')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // フラッシュメッセージdisableWorkInの存在をチェック
        @if(session('disableWorkIn'))
            // workInボタンを無効にする
            document.getElementById('workIn').disabled=true;
            // workOutボタンを有効にする
            document.getElementById('workOut').disabled=false;
            // breakInボタンを有効にする
            document.getElementById('breakIn').disabled=false;
            // breakOutボタンを無効にする
            document.getElementById('breakOut').disabled=true;
        @endif

        //フラッシュメッセージdisableWorkOutの存在をチェック
        @if(session('disableWorkOut'))
            // workInボタンを有効にする
            document.getElementById('workIn').disabled=false;
            // workOutボタンを無効にする
            document.getElementById('workOut').disabled=true;
            // breakInボタンを無効にする
            document.getElementById('breakIn').disabled=true;
            // breakOutボタンを無効にする
            document.getElementById('breakOut').disabled=true;
        @endif

        // フラッシュメッセージdisableBreakInの存在をチェック
        @if(session('disableBreakIn'))
            // workInボタンを無効にする
            document.getElementById('workIn').disabled=true;
            // workOutボタンを無効にする
            document.getElementById('workOut').disabled=true;
            // breakInボタンを無効にする
            document.getElementById('breakIn').disabled=true;
            // breakOutボタンを有効にする
            document.getElementById('breakOut').disabled=false;
        @endif

        //フラッシュメッセージdisableBreakOutの存在をチェック
        @if(session('disableBreakOut'))
            // workInボタンを無効にする
            document.getElementById('workIn').disabled=true;
            // workOutボタンを有効にする
            document.getElementById('workOut').disabled=false;
            // breakInボタンを有効にする
            document.getElementById('breakIn').disabled=false;
            // breakOutボタンを無効にする
            document.getElementById('breakOut').disabled=true;
        @endif
    });
</script>

<div class="index-content">

    <div class="index-content__title">
        <h2>{{ Auth::user()->name }}さん お疲れ様です！</h2>
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

    <div class="button-group">
        <form class="work-in" action="{{ route('form.work-in' )}}" method="get" >
        @csrf
            <button id="workIn" class="work-in__button" type="submit" >
                勤務開始
            </button>
            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
            <input type="hidden" name="workDate" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
            <input type="hidden" name="workIn" value="{{ \Carbon\Carbon::now()->format('H:i:s') }}">
            <input type="hidden" name="yearMonth" value="{{ \Carbon\Carbon::now()->format('Y-m-01') }}">
        </form>

        <form class="work-out" action="{{ route('form.work-out' )}}" method="get">
            @csrf
            <button id="workOut" class="work-out__button" type="submit" >
                勤務終了
            </button>
            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
            <input type="hidden" name="workDate" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
            <input type="hidden" name="workOut" value="{{ \Carbon\Carbon::now()->format('H:i:s') }}">
        </form>

        <form class="break-in" action="{{ route('form.break-in' )}}" method="get">
            @csrf
            <button id="breakIn" name="breakIn" class="break-in__button" type="submit" >
                休憩開始
            </button>
            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
            <input type="hidden" name="breakDate" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
            <input type="hidden" name="breakIn" value="{{ \Carbon\Carbon::now()->format('H:i:s') }}">
        </form>

        <form class="break-out" action="{{ route('form.break-out' )}}" method="get">
            @csrf
            <button id="breakOut" name="breakOut" class="break-out__button" type="submit" >
                休憩終了
            </button>
            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
            <input type="hidden" name="breakDate" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
            <input type="hidden" name="breakOut" value="{{ \Carbon\Carbon::now()->format('H:i:s') }}">
        </form>
    </div>
</div>
@endsection