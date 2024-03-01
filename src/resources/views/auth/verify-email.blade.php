@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endsection

@section('content')
<div class="verify-email__content">
    <div class="verify-email__content-title">
        <h2>メールアドレス確認のお願い</h2>
    </div>
    <form class="verify-email__form" action="/email/verification-notification" method="post">
        @csrf
        <p class="verify-email__form-p">
            メールアドレスに送信されたメール確認リンクをクリックしてください
        </p>
            <div class="button-outer">
                <button class="verify-email__button-submit" type="submit">
                    メールを再送信する
                </button>
            </div>
    </form>
</div>
@endsection