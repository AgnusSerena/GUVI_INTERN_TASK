$(document).ready(function () {
  function showFieldError(input, message) {
    let small = input.next("small");
    small.text(message);
    input.addClass("is-invalid shake");

    setTimeout(() => input.removeClass("shake"), 400);
  }

  function clearErrors() {
    $("small").text("");
    $(".form-control").removeClass("is-invalid");
  }

  $("#submitBtn").click(function () {
    clearErrors();

    let fullname = $("#fullname").val().trim();
    let email = $("#email").val().trim();
    let password = $("#password").val().trim();
    let cpassword = $("#cpassword").val().trim();

    // VALIDATION
    if (fullname === "")
      return showFieldError($("#fullname"), "Full name is required");
    if (email === "") return showFieldError($("#email"), "Email is required");
    if (password === "")
      return showFieldError($("#password"), "Password is required");
    if (cpassword === "")
      return showFieldError($("#cpassword"), "Please confirm password");

    if (password !== cpassword)
      return showFieldError($("#cpassword"), "Passwords do not match");

    // DATA TO SEND (MATCHES NEW signup.php)
    let data = {
      fullname: fullname,
      email: email,
      password: password,
    };

    $.ajax({
      url: "/php/signup.php",
      type: "POST",
      dataType: "json",
      data: data,

      success: function (response) {
        console.log("Signup response:", response);

        // Backend error
        if (!response.status) {
          showFieldError($("#email"), response.message);
          return;
        }

        // SUCCESS LOGIN AFTER SIGNUP
        localStorage.setItem("session_id", response.session_id);
        localStorage.setItem("email", response.data.emailid);
        localStorage.setItem("mongoDbId", response.data.mongoDbId);

        window.location.href = "/html/profile.html";
      },

      error: function (xhr) {
        if (xhr.status === 409) {
          showFieldError($("#email"), "Email already exists");
        } else {
          showFieldError($("#email"), "Server error. Try again.");
        }
      },
    });
  });
});
