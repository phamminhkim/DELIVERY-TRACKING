@extends('layouts.app')

@section('content')
    <div class="my-info container-fluid">
        {{-- <div class=" d-flex row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">PRofile</div>
            </div>
        </div>
    </div> --}}
        <div class="info-body row">
            <div class="right col-md-2 container-sm">
                <div class="right-slidebar d-flex flex-column">
                    <span class="mb-3">Xin chào, Nguyễn Ngọc Hùng</span>
                    <a class="text-decoration-none mb-3" href="myinfo">Quản lý tài khoản</a>
                    <a class="text-decoration-none mb-3" href="myorder">Đơn hàng của tôi</a>
                </div>
            </div>
            <div class="left col-md-9 container-sm">
                <div class="left-header mb-3">
                    <span>@yield('title')</span>
                </div>
                <div class="left-iframe">
                  @yield('my-info-iframe')
                </div>
            </div>
        </div>
    </div>
@endsection
