@extends('errors.layout')

@section('message')

<div class="page-content">

<div class="content ">
      <div class="error-page">
        <h2 class="headline text-warning"> 419</h2>

        <div class="error-content" style="padding-top: 30px;">
          <h3><i class="fas fa-exclamation-triangle text-warning"></i> Page Expired.</h3>

          <p>
           <a href="/login">Đăng nhập</a>
            
          </p>
 
        </div>
        <!-- /.error-content -->
      </div>
      <!-- /.error-page -->
</div>
</div>
@endsection