@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Vui lòng xác minh địa chỉ email.</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            Một liên kết xác minh mới đã được gửi đến địa chỉ email của bạn.
                        </div>
                    @endif

                    Trước khi tiếp tục, vui lòng kiểm tra email của bạn để biết liên kết xác minh. 
                    Nếu bạn không nhận được email,
                    
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">Bấm vào đây để tạo yêu cầu mới</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
