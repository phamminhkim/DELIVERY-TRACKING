@extends('profile.myinfo')

@section('title')
    Quản lý tài khoản / xem
@endsection

@section('my-info-iframe')
    <div class="formInfo p-3">
        @foreach ($fakeUser as $item)
            <div class="form-body row row-cols-lg-3 row-cols-sm-1 row-cols-md-2 g-1 g-lg-3">
                <div class="col-sm-4 col-12 col mb-3">
                    <label for="full_name" class="form-label">Họ tên</label>
                    <input type="text" class="form-input form-control" id="full_name" placeholder="Full name"
                        aria-label="Full name" value="{{ $item['name'] }}" disabled>
                </div>
                <div class="col-sm-4 col-12 col">
                    <label for="email" class="form-label">
                        Địa chỉ email |
                        <a class="form-href" id="edit-email">Thay đổi</a>
                        <a class="form-href" id="save-email" hidden>Lưu</a>
                    </label>
                    <input type="email" class="form-input form-control" id="email" placeholder="Email"
                        aria-label="Email"
                        value="{{ substr($item['email'], 0, 2) }}********{{ substr($item['email'], strpos($item['email'], '@')) }}"
                        disabled>
                </div>
                <div class="col-sm-4 col-12 col">
                    <label for="phone_number" class="form-label">
                        Số điện thoại |
                        <a class="form-href" id="edit-phone">Thay đổi</a>
                        <a class="form-href" id="save-phone" hidden>Lưu</a>
                    </label>
                    <input type="text" class="form-input form-control" id="phone_number" placeholder="Phone number"
                        aria-label="Phone number"
                        value="{{ substr($item['phone_number'], 0, 3) }}******{{ substr($item['phone_number'], -2) }}"
                        disabled>
                </div>
                <div class="col-sm-4 col-12 col d-flex flex-column">
                    <label for="gender" class="form-label">Giới tính</label>
                    <select class="form-input form-select" id="gender" aria-label="Gender" disabled>
                        <option value="0" selected>Nam</option>
                        <option value="1">Nữ</option>
                    </select>
                </div>
            </div>
        @endforeach
        <div class="form-button container-sm row  col-md-3 mt-5">
            <button onclick="showSaveBtn()" type="submit" id="edit-btn" class="btn btn-primary col-md-12 mb-3">Sửa thông
                tin </button>
            <button onclick="changePassBtn()" type="button" id="changePass-btn" class="btn btn-primary col-md-12 mb-3">Đổi
                mật
                khẩu</button>
            <button onclick="saveBtn()" type="button" id="save-btn" class="btn col-md-12 mb-3" hidden>Lưu thay đổi</button>
        </div>
    </div>

    {{-- * truyền dữ liệu qua file script --}}
    <script>
        var emailValue = "{{ $item['email'] }}";
        var phoneValue = "{{ $item['phone_number'] }}";
    </script>

    {{-- * gọi file script --}}
    <script src="{{ asset('js/scripts/account/formInfo.js') }}"></script>
@endsection

{{-- <script>
    function showSaveBtn() {
        document.getElementById("edit-btn").setAttribute("hidden", true);
        document.getElementById("changePass-btn").setAttribute("hidden", true);
        document.getElementById("save-btn").removeAttribute("hidden");
    }

    function saveBtn() {
        document.getElementById("edit-btn").removeAttribute("hidden");
        document.getElementById("changePass-btn").removeAttribute("hidden");
        document.getElementById("save-btn").setAttribute("hidden", true);

        alert('Save Button clicked');
    }

    function changePassBtn() {
        alert('ChangePass Button clicked');
    }

    document.addEventListener("DOMContentLoaded", () => {
        const editBtn = document.getElementById("edit-btn");
        const saveBtn = document.getElementById("save-btn");

        const editEmail = document.getElementById("edit-email");
        const editPhone = document.getElementById("edit-phone");

        var full_name = document.getElementById("full_name");
        var email = document.getElementById("email");
        var phone_number = document.getElementById("phone_number");
        var gender = document.getElementById("gender");

        editBtn.addEventListener("click", () => {
            full_name.classList.remove("form-input");
            full_name.disabled = false;
            email.classList.remove("form-input");
            email.disabled = false;
            phone_number.classList.remove("form-input");
            phone_number.disabled = false;
            gender.classList.remove("form-input");
            gender.disabled = false;

            email.value = "{{ $item['email'] }}";
            phone_number.value = "{{ $item['phone_number'] }}";
        });

        saveBtn.addEventListener("click", () => {
            full_name.classList.add("form-input");
            full_name.disabled = true;
            email.classList.add("form-input");
            email.disabled = true;
            phone_number.classList.add("form-input");
            phone_number.disabled = true;
            gender.classList.add("form-input");
            gender.disabled = true;

            var hiddenEmail = email.value.replace(/^(.{2})(.*)(@.*)$/, function(match, p1, p2, p3) {
                var asterisks = "*".repeat(p2.length);
                return p1 + asterisks + p3;
            });

            var hiddenPhone = phone_number.value.replace(/^(.{3})(.*)(.{2})$/, function(match, p1, p2, p3) {
                var asterisks = "*".repeat(p2.length);
                return p1 + asterisks + p3;
            });

            email.value = hiddenEmail;
            phone_number.value = hiddenPhone;
        });

        editEmail.addEventListener("click", () => {
            email.classList.remove("form-input");
            email.disabled = false;
        });

        editPhone.addEventListener("click", () => {
            phone_number.classList.remove("form-input");
            phone_number.disabled = false;
        });
    });
</script> --}}
