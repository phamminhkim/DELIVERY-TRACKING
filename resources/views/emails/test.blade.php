<!DOCTYPE html>
<html>

<head>
    <!-- <title>{{ $title }}</title> -->
</head>

<body>
    <div>
        <h4 style="display: flex;" >
            <!-- Đơn hàng -->
            <img src="https://delivery.thienlong.vn/icon_tl.png" width="5%" style="margin-right: 10px;" > ĐƠN HÀNG TẠI HỆ THỐNG DELIVERY TRACKING
        </h4>
        <div>
            <p class="text-blue">THÔNG TIN ĐƠN HÀNG #{{ $data['code'] }} <small class="text-black-50">({{ $data['created_at']}})</small></p>
        </div>
        <div class="container">
            <div class="left">
                <label class="font-weight-bold">Thông tin người gửi</label></br>
                <span class="text-light-gray">{{ $data['user']['name'] }}</span></br>
                <span class="text-link-blue"><u>{{ $data['user']['email'] }}</u></span></br>
                <span class="text-light-gray">{{ $data['user']['phone_number'] }}</span></br>

            </div>
            <div class="right">
                <label class="font-weight-bold">Nội dung</label></br>
                <span class="text-light-gray"><b>Tiêu đề: </b>{{ $data['title'] }}</span></br>
                <span class="text-light-gray"><b>Trung tâm: </b>{{ $data['central_branch'] }}</span></br>
                <span class="text-light-gray"><b>Ghi chú: </b>{{ $data['description'] }}</span></br>
                <span>Có gửi File đính kèm</span>
            </div>
        </div>
        <div style="margin-top: 20px; border-top: 1px solid #ddd; padding-top: 10px;">
            <p style="font-size: 14px; color: #555;">
                Đây là email tự động, vui lòng không trả lời.
            </p>
            <!-- <img src="{{ asset('img/logo.png') }}" width="17%"> -->
            <!-- <img src="https://www.deliverytracking.vn/img/logo.png" width="17%"> -->
            <!-- // lấy img từ asset public/img/logo.png -->
             <!-- <img src="https://delivery.thienlong.vn/icon_tl.png" width="17%"> -->
             <!-- <img src="{{ asset('/mail_fotter.jpg') }}" width="100%"> -->
        </div>
        <!-- <div>
            <p class="text-blue">THÔNG TIN CHI TIẾT ĐƠN HÀNG</p>
        </div>
        <div>
            <p><strong>Tiêu đề:</strong> {{ $title }}</p>
        </div> -->

    </div>
</body>
<style>
    .text-blue {
        color: #007bff;
        width: 100%;
        font-weight: bold;
        border-bottom: 2px solid #007bff;
    }

    .container {
        display: flex;
        width: 100%;
        padding-left: 20px;
    }

    .left {
        flex: 1;
        /* background-color: lightblue; */
    }

    .right {
        flex: 1;
        /* background-color: lightpink; */
    }

    .font-weight-bold {
        font-weight: bold;
    }

    .text-light-gray {
        color: lightslategrey;
    }

    .text-link-blue {
        color: #007bff;
    }
    .d-flex{
        display: flex;
        justify-content: flex-start;
    }
</style>

</html>
