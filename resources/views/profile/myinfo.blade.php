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
            <div class="right col-lg-2 col-sm-12 container-sm">
                <div class="right-slidebar row">
                    <span class="mb-3">Xin chào, Nguyễn Ngọc Hùng</span>
                    <a class="text-decoration-none mb-3" href="myinfo">Quản lý tài khoản</a>
                    <a class="text-decoration-none mb-3" href="myorder">Đơn hàng của tôi</a>
                    <a class="text-decoration-none mb-3" href="order-waiting">
                        Đơn hàng chờ giao
                        <span class="badge badge-info text-dark">0</span>
                    </a>
                    <a class="text-decoration-none mb-3" href="myorder">
                        Đơn hàng đang giao
                        <span class="badge badge-info text-dark">1</span>
                    </a>
                    <a class="text-decoration-none mb-3" href="myorder">Đơn hàng đã giao</a>
                    <a class="text-decoration-none mb-3" href="myorder">Nhà vận chuyển</a>
                    <a class="text-decoration-none mb-3" href="myorder">Nhân viên giao hàng</a>
                </div>
                <div class="dropdown sm-show mb-3">
                    <button class="btn dropdown-toggle p-0" type="button" id="dropdownMenu" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <span>Xin chào, Nguyễn Ngọc Hùng</span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenu">
                        <li>
                            <a class="dropdown-item" href="myinfo">Quản lý tài khoản</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="myorder">Đơn hàng của tôi</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="order-waiting">
                                Đơn hàng chờ giao
                                <span class="badge badge-info text-dark">0</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                Đơn hàng đang giao
                                <span class="badge badge-info text-dark">1</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">Đơn hàng đã giao</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">Nhà vận chuyển</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">Nhân viên giao hàng</a>
                        </li>
                    </div>
                </div>
            </div>
            <div class="left col-lg-9 col-sm-12 container-sm">
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
