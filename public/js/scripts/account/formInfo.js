function showSaveBtn() {
    document.getElementById("edit-btn").setAttribute("hidden", true);
    document.getElementById("changePass-btn").setAttribute("hidden", true);
    document.getElementById("save-btn").removeAttribute("hidden");
}

function saveBtn() {
    document.getElementById("edit-btn").removeAttribute("hidden");
    document.getElementById("changePass-btn").removeAttribute("hidden");
    document.getElementById("save-btn").setAttribute("hidden", true);

    alert("Save Button clicked");
}

function changePassBtn() {
    alert("ChangePass Button clicked");
}

document.addEventListener("DOMContentLoaded", () => {
    const editBtn = document.getElementById("edit-btn");
    const saveBtn = document.getElementById("save-btn");

    const editEmail = document.getElementById("edit-email");
    const editPhone = document.getElementById("edit-phone");

    const saveEmail = document.getElementById("save-email");
    const savePhone = document.getElementById("save-phone");

    var full_name = document.getElementById("full_name");
    var email = document.getElementById("email");
    var phone_number = document.getElementById("phone_number");
    var gender = document.getElementById("gender");

    var hiddenEmail = email.value.replace(
        /^(.{2})(.*)(@.*)$/,
        function (match, p1, p2, p3) {
            var asterisks = "*".repeat(p2.length);
            return p1 + asterisks + p3;
        }
    );

    var hiddenPhone = phone_number.value.replace(
        /^(.{3})(.*)(.{2})$/,
        function (match, p1, p2, p3) {
            var asterisks = "*".repeat(p2.length);
            return p1 + asterisks + p3;
        }
    );

    editBtn.addEventListener("click", () => {
        full_name.classList.remove("form-input");
        full_name.disabled = false;
        email.classList.remove("form-input");
        email.disabled = false;
        phone_number.classList.remove("form-input");
        phone_number.disabled = false;
        gender.classList.remove("form-input");
        gender.disabled = false;

        email.value = emailValue;
        phone_number.value = phoneValue;
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

        email.value = hiddenEmail;
        phone_number.value = hiddenPhone;
    });

    // ? click edit email và lưu
    editEmail.addEventListener("click", () => {
        email.classList.remove("form-input");
        email.value = emailValue;
        editEmail.hidden = true;
        saveEmail.hidden = false;
        email.disabled = false;
    });

    saveEmail.addEventListener("click", () => {
        email.classList.add("form-input");
        editEmail.hidden = false;
        saveEmail.hidden = true;
        email.value = hiddenEmail;
        email.disabled = true;
    });

    // ? click edit phone và lưu
    editPhone.addEventListener("click", () => {
        phone_number.classList.remove("form-input");
        phone_number.value = phoneValue;
        editPhone.hidden = true;
        savePhone.hidden = false;
        phone_number.disabled = false;
    });

    savePhone.addEventListener("click", () => {
        phone_number.classList.add("form-input");
        editPhone.hidden = false;
        savePhone.hidden = true;
        phone_number.value = hiddenPhone;
        phone_number.disabled = true;
    });
});
