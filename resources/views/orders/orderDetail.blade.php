@extends('profile.myinfo')

@section('title')
    Chi tiết kiện hàng
@endsection

@section('my-info-iframe')
    <article class="orderDetail container p-3">
        {{-- ? header --}}
        <div class="detail-header">
            <div class="header-top row mb-3">
                <span class="col header-title">
                    Thông tin đối tác giao hàng
                </span>
                <span class="col">
                    <span class="header-status float-right px-4">
                        Đã giao hàng
                    </span>
                </span>
            </div>
            <div class="header-body row row-cols-1">
                <span class="col">Đơn vị vận chuyển : LEX VN</span>
                <span class="col">Đối tác giao hàng: Nguyễn Văn Dương</span>
                <span class="col">Mã đơn hàng: 456789876</span>
            </div>
        </div>
        {{-- ? end header --}}

        {{-- ? steps body --}}
        <el-steps :active="1">
            <el-step title="Step 1" icon="el-icon-edit"></el-step>
            <el-step title="Step 2" icon="el-icon-upload"></el-step>
            <el-step title="Step 3" icon="el-icon-picture"></el-step>
        </el-steps>
        {{-- ? end steps body --}}
    </article>
@endsection
