@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="register-content">
    <div class="register-content__title">
        <h2>会員登録</h2>
    </div>
    <form class="register-form" action="/register" method="post">
        @csrf
        <div class="register-form__group">
            <div class="input-outer">
                <input type="text" name="name" value="{{ old('name') }}" placeholder="名前"/>
            </div>
            <div class="error-outer">
                <p class="error-p">
                    @error('name')
                    {{ $message }}
                    @enderror
                </p>
            </div>
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
            <div class="input-outer">
                <input type="password" name="password_confirmation" placeholder="確認用パスワード"/>
            </div>
            <div class="button-outer">
                <button class="form-button__submit" type="submit">
                    会員登録
                </button>
            </div>
        </div>
    </form>
    <div class="login-link__button-outer">
        <p class="login-link__button-message">
            アカウントをお持ちの方はこちらから
        </p>
        <a class="login-link__button-submit" href="/login">
            ログイン
        </a>
    </div>
</div>
@endsection