@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Đăng nhập lỗi</div>

                <div class="card-body">
                   
                   {{$message}}
                    
                   <a type="button" href="{{route('login')}}" class="btn btn-link p-0 m-0 align-baseline">Quay lại trang đăng nhập</a>.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


