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
    $("#errorBox").addClass("d-none").text("");
  }

  $("#loginBtn").click(function () {
    clearErrors();

    let email = $("#email").val().trim();
    let password = $("#password").val().trim();

    if (email === "") {
      showFieldError($("#email"), "Email is required");
      return;
    }

    if (password === "") {
      showFieldError($("#password"), "Password is required");
      return;
    }

    $.ajax({
      url: "/php/login.php",
      type: "POST",
      data: {
        input1: email,
        input2: password,
      },

      success: function (response) {
        console.log("Login response:", response);

        if (response.status === false) {
          showFieldError($("#email"), "Invalid email or password");
          showFieldError($("#password"), "Invalid email or password");
          return;
        }

        if (response.status === true) {
          localStorage.setItem("isLogin", true);
          localStorage.setItem("session_id", response.session_id);
          localStorage.setItem("emailid", response.email);

          window.location.href = "/html/welcome.html";
        }
      },

      error: function () {
        $("#errorBox").removeClass("d-none").text("Server error. Try again");
      },
    });
  });
});
