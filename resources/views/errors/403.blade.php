@extends('layouts.app')


@section('content')
    <div class="page-content">

        <div class="content ">
            <div class="error-page">
                <h2 class="headline text-warning"> 403</h2>

                <div class="error-content" style="padding-top: 30px;">
                    <h3><i class="fas fa-exclamation-triangle text-warning"></i> Bạn không có quyền thực hiện.</h3>

                    <p>
                        Vui lòng liên hệ admin
                    </p>

                </div>
                <!-- /.error-content -->
            </div>
            <!-- /.error-page -->
        </div>
    </div>
@endsection
