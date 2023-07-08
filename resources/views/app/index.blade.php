@extends('layouts.appmain')

@section('content')
<div class="col-md-4 mb-3">
    <span class="link-menu-admin text-uppercase ml-3 mt-2">Quản lý danh sách</span>
    <a class="mt-1 p-1 mr-1 ml-3 mr-4 d-flex align-items-center text-uppercase font-weight-bold"
        href="{{ url('/delivery-partner') }}">
        <i class=""></i>Danh sách nhà vận chuyển
    </a>
    <a class="mt-1 p-1 mr-1 ml-3 mr-4 d-flex align-items-center text-uppercase font-weight-bold"
        href="{{ url('/delivery-user') }}">
        <i class=""></i>Danh sách người dùng
    </a>



</div>
@endsection
