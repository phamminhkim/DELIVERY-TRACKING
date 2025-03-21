<template>
    <div>
        <p class="font-weight-bold d-flex">
            <img src="https://delivery.thienlong.vn/icon_tl.png" width="5%" style="margin-right: 10px;"> ĐƠN HÀNG TẠI HỆ
            THỐNG DELIVERY TRACKING
        </p>
        <div>
            <p class="text-primary custom-bd-bt">THÔNG TIN ĐƠN HÀNG #... <small class="text-black-50">({{ current_time }})</small></p>
        </div>
        <div class="form-group d-flex pl-2">
            <div class="flex-1">
                <label class="font-weight-bold">Thông tin người gửi</label></br>
                <span class="text-gray">{{ current_user.name }}</span></br>
                <span class="text-primary"><u>{{ current_user.email }}</u></span></br>
                <span class="text-gray">{{ current_user.phone_number }}</span></br>

            </div>
            <div class="flex-fill ml-2">
                <label class="font-weight-bold">Nội dung</label></br>
                <span class="text-gray"><b>Tiêu đề: </b>{{ data.title }}</span></br>
                <span class="text-gray"><b>Trung tâm: </b>{{ data.central_branch }}</span></br>
                <span class="text-gray"><b>Ghi chú: </b>{{ data.description }}</span></br>
                <span class="text-xs">Có gửi File đính kèm</span>
            </div>
        </div>
        <div style="margin-top: 20px; border-top: 1px solid #ddd; padding-top: 10px;">
            <p style="font-size: 14px; color: #555;">
                Đây là email tự động, vui lòng không trả lời.
            </p>

        </div>
    </div>
</template>
<script>
import { type } from 'jquery';

export default {
    props: {
        data: { type: Object, default: () => { return {} } },
        api_handler: { type: Object, default: () => { return {} } },
        current_time: { type: String, default: '' },
    },
    data() {
        return {
            current_user: window.Laravel.current_user,
        }
    },
    methods: {

    }
}
</script>
<style lang="scss" scoped>
.custom-bd-bt {
    border-bottom: 2px solid #007bff;
}
</style>
