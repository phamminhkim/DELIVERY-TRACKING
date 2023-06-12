@extends('layouts.app')

@section('content')
    <div class="container login-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <p><strong>ĐĂNG NHẬP TÀI KHOẢN</strong></p>
                        <p>Bạn chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký tại đây</a></p>
                        @if (session('warning'))
                            <div class="alert alert-warning" role="alert">
                                <strong>{{ session('warning') }}</strong>
                            </div>
                        @endif
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">Tài khoản đăng
                                    nhập</label>

                                <div class="col-md-6">
                                    <input id="email" type="text" placeholder="Mã nhân viên hoặc e-mail"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" placeholder="Mật khẩu" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="current-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row offset-md-4">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                            {{ old('remember') ? 'checked' : '' }}>

                                        <label class="form-check-label" for="remember">
                                            Nhớ đăng nhập
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    @if (Route::has('password.request'))
                                        <a class="btn-link forgot-pass" href="{{ route('password.request') }}">
                                            Quên mật khẩu ?
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="col btn form-btn" style="background-color: #EC9D27">
                                        Đăng nhập
                                    </button>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4 text-center">
                                    <a class="col-5 btn form-btn" href=" {{ url('login/google') }} "
                                        style="background-color: #E54040">
                                        {{-- <i class="fa-brands fa-google"></i> --}}
                                        Google
                                    </a>
                                    <a class="col-5 btn form-btn" href=" {{ url('login/facebook') }} "
                                        style="background-color: #6683D9">
                                        {{-- <i class="fa-brands fa-facebook"></i> --}}
                                        Facebook
                                    </a>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
