{{-- @extends('layouts.app')
@section('link-header')
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script type="text/javascript">
        var onloadCallback = function() {
            //alert("grecaptcha is ready!");
        };
    </script>
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
@endsection --}}
{{-- @section('content')
    <div class="container register-page">

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <p><strong>ĐĂNG KÝ TÀI KHOẢN</strong></p>
                        <p>Bạn đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập tại đây</a></p>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Tên (*)</label>

                                <div class="col-md-6">
                                    <input id="name" type="text"
                                        class="form-control @error('name') is-invalid @enderror" name="name"
                                        value="{{ old('name') }}" autocomplete="name" autofocus>

                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">Địa chỉ E-Mail</label>

                                <div class="col-md-6">
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" required autocomplete="email">

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Mật khẩu</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="new-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Xác nhận mật
                                    khẩu</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control"
                                        name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right"></label>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="col-md-offset-4 col-md-6">

                                            <div class="g-recaptcha" data-sitekey="6LdYYIEmAAAAAGaDLR5uQg_WWQrckA6EW9N3v41D"
                                                data-callback="YourOnSubmitFn"></div>
                                            <div hidden
                                                class="form-control @error('g-recaptcha-response') is-invalid @enderror"
                                                name="g-recaptcha-response"></div>
                                            @error('g-recaptcha-response')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="col btn form-btn" style="background-color: #EC9D27">
                                        Đăng ký
                                    </button>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4 text-center">
                                    <a class="col-5 btn form-btn" href=" {{ url('login/google') }} "
                                        style="background-color: #E54040">
                                        Google
                                    </a>
                                    <a class="col-5 btn form-btn" href=" {{ url('login/facebook') }} "
                                        style="background-color: #6683D9">
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
@endsection --}}

@extends('layouts.auth')
@section('link-header')
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script type="text/javascript">
        var onloadCallback = function() {
            //alert("grecaptcha is ready!");
        };
    </script>
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
@endsection
@section('content')
    <div class="register-box">
        <div class="register-logo">
            <b>Đăng ký tài khoản</b>
        </div>
        <div class="card">
            <div class="card-body register-card-body">
                <p class="login-box-msg">Đăng ký tài khoản mới</p>
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="input-group mb-3">
                        {{-- <input type="text" class="form-control" placeholder="Full name"> --}}
                        <input id="name" type="text" placeholder="Họ và tên"
                            class="form-control @error('name') is-invalid @enderror" name="name"
                            value="{{ old('name') }}" autocomplete="name" autofocus>

                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        {{-- <input type="email" class="form-control" placeholder="Email"> --}}
                        <input id="email" type="email" placeholder="Email"
                            class="form-control @error('email') is-invalid @enderror" name="email"
                            value="{{ old('email') }}" required autocomplete="email">

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
                        <input id="password" type="password" placeholder="Mật khẩu"
                            class="form-control @error('password') is-invalid @enderror" name="password" required
                            autocomplete="new-password">

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
                    <div class="input-group mb-3">
                        {{-- <input type="password" class="form-control" placeholder="Retype password"> --}}
                        <input id="password-confirm" type="password" placeholder="Nhập lại mật khẩu" class="form-control" name="password_confirmation"
                            required autocomplete="new-password">

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="col-md-offset-4 col-md-6">

                                    <div class="g-recaptcha" data-sitekey="6LdYYIEmAAAAAGaDLR5uQg_WWQrckA6EW9N3v41D"
                                        data-callback="YourOnSubmitFn"></div>
                                    <div hidden
                                        class="form-control @error('g-recaptcha-response') is-invalid @enderror"
                                        name="g-recaptcha-response"></div>
                                    @error('g-recaptcha-response')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="agreeTerms" name="terms" value="agree">
                                <label for="agreeTerms">
                                    I agree to the <a href="#">terms</a>
                                </label>
                            </div>
                        </div> --}}

                        <div class="col-5 m-auto">
                            <button type="submit" class="btn btn-primary btn-block">Đăng ký</button>
                        </div>

                    </div>
                </form>
                <div class="social-auth-links text-center">
                    <p>- HOẶC -</p>
                    <a href=" {{ url('login/facebook') }}" class="btn btn-block"
                        style="background-color: #3b5998; font-weight: 600; color: #fff">
                        Đăng nhập bằng Facebook
                    </a>
                    <a href=" {{ url('login/google') }}" class="btn btn-block"
                        style="background-color: #E54040; font-weight: 600; color: #fff">
                        Đăng nhập bằng Google
                    </a>
                </div>
                <a href="{{ route('login') }}" class="text-center">I already have a membership</a>
            </div>
        @endsection
