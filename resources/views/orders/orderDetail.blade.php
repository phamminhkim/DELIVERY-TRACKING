@extends('profile.myinfo')

@section('title')
    Chi tiết kiện hàng
@endsection

@section('my-info-iframe')
    <article class="orderDetail p-3">
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
        <div class="steps row justify-content-center align-items-center">
            <div class="col-md-8">
                <el-steps :active="1" finish-status="success">
                    <el-step title="Đang xử lý" icon="el-icon-document"></el-step>
                    <el-step title="Đóng gói" icon="el-icon-present"></el-step>
                    <el-step title="Đang vận chuyển" icon="el-icon-truck"></el-step>
                    <el-step title="Đã giao hàng" icon="el-icon-receiving"></el-step>
                </el-steps>
            </div>
        </div>
        {{-- ? end steps body --}}

        {{-- ? timeline body --}}
        <div class="timeline">
            @foreach ($fakeTimeline as $item)
                <el-timeline>
                    <el-timeline-item key="index" timestamp="{{ $item['timeline'] }}" color="{{ $item['active'] === 1 ? '#0D8F41' : '#D9D9D9' }}">
                        {{ $item['title'] }}
                    </el-timeline-item>
                </el-timeline>
            @endforeach
        </div>
        {{-- ? end timeline --}}
    </article>
@endsection
