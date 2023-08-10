{{-- @extends('layouts.app')
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
                            <div class="form-group row mb-2 text-center">
                                <strong class="col-md-6 offset-md-4">hoặc</strong>
                            </div>
                            <div class="form-group row mb-2">
                                <div class="col-md-6 offset-md-4">
                                    <a class="col btn form-btn" href=" {{ url('login/google') }} "
                                        style="background-color: #E54040">
                                        Đăng nhập bằng Google
                                    </a>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <div class="col-md-6 offset-md-4">
                                    <a class="col btn form-btn" href=" {{ url('login/facebook') }} "
                                        style="background-color: #3b5998">
                                        Đăng nhập bằng Facebook
                                    </a>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <div class="col-md-6 offset-md-4">
                                    <a class="col btn form-btn" href=" {{ url('auth/zalo') }} "
                                        style="background-color: #0180C7">
                                        Đăng nhập bằng Zalo
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection --}}


@extends('layouts.auth')
@section('content')
    <div class="login-box">
        <div class="login-logo">
            <b>Đăng nhập</b>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Đăng nhập để bắt đầu</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="input-group mb-3">
                        {{-- <input type="email" class="form-control" placeholder="Email"> --}}
                        <input id="email" type="text" placeholder="Mã nhân viên hoặc e-mail"
                            class="form-control @error('email') is-invalid @enderror" name="email"
                            value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        {{-- <input type="password" class="form-control" placeholder="Password"> --}}
                        <input id="password" placeholder="Mật khẩu" type="password"
                            class="form-control @error('password') is-invalid @enderror" name="password" required
                            autocomplete="current-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-7">
                            <div class="icheck-primary">
                                {{-- <input type="checkbox" id="remember"> --}}
                                <input {{-- class="form-check-input"  --}} type="checkbox" name="remember" id="remember"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-5">
                            {{-- <button type="submit" class="btn btn-primary btn-block">Sign In</button> --}}
                            <button type="submit" class="btn btn-primary btn-block">
                                Đăng nhập
                            </button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

                <div class="social-auth-links text-center mb-3">
                    {{-- <p>- HOẶC -</p> --}}
                    {{-- {{-- <a href=" {{ url('login/facebook') }}" class="btn btn-block"
                        style="background-color: #3b5998; font-weight: 600; color: #fff">
                        Đăng nhập bằng Facebook
                    </a> --}}
                    {{-- <a href=" {{ url('login/google') }}" class="btn btn-block"
                        style="background-color: #E54040; font-weight: 600; color: #fff">
                        Đăng nhập bằng Google
                    </a> --}}
                    {{-- <a href=" {{ url('login/zalo') }}" class="btn btn-block"
                        style="background-color: #0180C7; font-weight: 600; color: #fff">
                        Đăng nhập bằng Zalo
                    </a> --}}
                    {{-- <a href=" {{ url('auth/onetl') }}" class="btn btn-block"
                        style="background-color: #0180C7; font-weight: 600; color: #fff">
                        Đăng nhập OneThienLong
                    </a> --}}
                </div>
                <!-- /.social-auth-links -->

                {{-- <p class="mb-1">
                    <a href="{{ route('password.request') }}">Quên mật khẩu</a>
                </p> --}}
                {{-- <p class="mb-0">
                    <a href="{{ route('register') }}" class="text-center">Đăng ký tài khoản mới</a>
                </p> --}}
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->
@endsection
