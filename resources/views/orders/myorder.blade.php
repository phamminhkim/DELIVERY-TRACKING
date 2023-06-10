{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">My order</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}

@extends('profile.myinfo')

@section('title')
    Đơn hàng của tôi
@endsection

@section('my-info-iframe')
    {{-- <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">My order</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        {{ __('You are logged in!') }}
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="formOrder container p-0">
        <div class="form-header row">
            <div class="header-title col-sm-1 col mb-3">
                <span>Hiển thị </span>
            </div>
            <div class="header-option col">
                <select class="form-select" aria-label="Show list">
                    <option selected>5 đơn hàng gần đây</option>
                    <option value="1">10 đơn hàng gần đây</option>
                    <option value="2">15 đơn hàng gần đây</option>
                    <option value="3">20 đơn hàng gần đây</option>
                </select>
            </div>
        </div>
        <div class="form-list">
            {{-- <div class="list-card container-sm mb-3">
                <div class="list-header row">
                    <div class="header-title col-sm-10">
                        <span class="title">
                            <i class="fa-solid fa-truck px-1" style="color: #000000;"></i>
                            Giao hàng nhanh
                        </span>
                    </div>
                    <div class="header-status col-sm-2 text-center">
                        <span> Đã giao hàng</span>
                    </div>
                </div>
                <div class="stoke"></div>
                <div class="list-body row">
                    <div class="left col-sm-10">
                        <div class="left-lable mt-2">
                            Số phiếu:
                            <a href="">400000333</a>
                        </div>
                        <div class="left-lable mt-2">
                            Trọng lượng :
                            <span>130 kg</span>
                        </div>
                        <div class="left-lable mt-2">
                            Số lần giao:
                            <span>1 lần</span>
                        </div>
                        <div class="left-lable mt-2">
                            Địa chỉ giao:
                            <span>123 Trường Chinh, phường Bình Hưng Hòa, Bình Tân, HCM</span>
                        </div>
                    </div>
                    <div class="right col-sm-2 d-flex justify-content-center align-items-center">
                        <div class="detail-btn">
                            <button type="button" class="btn">
                                Chi tiết
                            </button>
                        </div>
                    </div>
                </div>
            </div> --}}

            {{-- ? test fakeData --}}
            @foreach ($fakeData as $item)
                <div class="list-card container-sm mb-3">
                    <div class="list-header row">
                        <div class="header-title col-sm-10 col">
                            <span class="title">
                                <i class="fa-solid fa-truck px-1" style="color: #000000;"></i>
                                Giao hàng nhanh
                            </span>
                        </div>
                        <div class="header-status col col-sm-2 text-center">
                            @if ($item['status'] === 0)
                                <span style="color: #0D8F41;"> Đã giao hàng</span>
                            @else
                                <span style="color: #504949;"> Đang giao</span>
                            @endif
                        </div>
                    </div>
                    <div class="stoke"></div>
                    <div class="list-body row">
                        <div class="left col-sm-10">
                            <div class="left-label mt-2">
                                Số phiếu:
                                <a href="">{{ $item['number_id'] }}</a>
                            </div>
                            <div class="left-label mt-2">
                                Trọng lượng :
                                <span>{{ $item['weight'] }}</span>
                            </div>
                            <div class="left-label mt-2">
                                Số lần giao:
                                <span>{{ $item['quantity'] }}</span>
                            </div>
                            <div class="left-label mt-2">
                                Địa chỉ giao:
                                <span>{{ $item['address'] }}</span>
                            </div>
                        </div>
                        <div class="right col col-sm-2 d-flex justify-content-center align-items-center">
                            <div class="detail-btn">
                                <a role="button" class="btn" id="detailButton" href='myorder-detail'>
                                    Chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="form-footer mt-3">
            <div class="row col-8 d-flex justify-content-between">
                <div class="left col-5">
                    <span class="left-title">Per page:</span>
                    <input class="left-input float-center" type="number" value="10">
                </div>
                <div class="right col-7">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <li class="page-item"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection
