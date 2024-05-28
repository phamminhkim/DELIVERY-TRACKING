<template>
    <div class="card">
        <div class="card-body">

            <form>
                <div class="form-group row">
                    <label for="staticEmail" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-6">
                       <span>hung.nn@thienlongvn.com</span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword" class="col-sm-2 col-form-label">Mật khẩu hiện tại</label>
                    <div class="col-sm-6">
                        <input type="password" class="form-control" id="password_current">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword" class="col-sm-2 col-form-label">Mật khẩu mới</label>
                    <div class="col-sm-6">
                        <input type="password" class="form-control" id="password">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword" class="col-sm-2 col-form-label">Nhập lại mật khẩu mới</label>
                    <div class="col-sm-6">
                        <input type="password" class="form-control" id="password_confirm">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword" class="col-sm-2 col-form-label"></label>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                    </div>
                </div>

            </form>
        </div>
    </div>


</template>
<script>
export default {
    // name:'UserProfile'
}
</script>
<style lang="">

</style>