@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login-content">
    <div class="login-content__title">
        <h2>ログイン</h2>
    </div>
    <form class="login-form" action="/login" method="post">
        @csrf
        <div class="login-form__group">
            <div class="input-outer">
                <input type="email" name="email" value="{{ old('email') }}" placeholder="メールアドレス"/>
            </div>
            <div class="error-outer">
                <p class="error-p">
                    @error('email')
                    {{ $message }}
                    @enderror
                </p>
            </div>
            <div class="input-outer">
                <input type="password" name="password" placeholder="パスワード"/>
            </div>
            <div class="error-outer">
                <p class="error-p">
                    @error('password')
                    {{ $message }}
                    @enderror
                </p>
            </div>
            <div class="button-outer">
                <button class="form-button__submit" type="submit">
                    ログイン
                </button>
            </div>
        </div>
    </form>
    <div class="register-link__button-outer">
        <p class="register-link__button-message">
            アカウントをお持ちの方はこちらから
        </p>
        <a class="register-link__button-submit" href="/register">
            会員登録
        </a>
    </div>
</div>
@endsection