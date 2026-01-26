$(document).ready(function () {
  function showFieldError(input, message) {
    let small = input.next("small");
    small.text(message);

    input.addClass("is-invalid shake");

    setTimeout(() => {
      input.removeClass("shake");
    }, 400);
  }

  function clearErrors() {
    $("small").text("");
    $(".form-control").removeClass("is-invalid");
  }

  $("#submitBtn").click(function () {
    clearErrors();

    let email = $("#email").val().trim();
    let password = $("#password").val().trim();
    let cpassword = $("#cpassword").val().trim();

    if (email === "") {
      showFieldError($("#email"), "Email is required");
      return;
    }

    if (password === "") {
      showFieldError($("#password"), "Password is required");
      return;
    }

    if (cpassword === "") {
      showFieldError($("#cpassword"), "Please confirm password");
      return;
    }

    if (password !== cpassword) {
      showFieldError($("#cpassword"), "Passwords do not match");
      return;
    }

    // DATA TO SEND
    let data = {
      input2: password,
      input3: email,
    };

    $.ajax({
      url: "/php/signup.php",
      type: "POST",
      data: data,

      success: function (response) {
        console.log("Signup response:", response);

        // ❌ BACKEND ERROR
        if (response.status === "error") {
          showFieldError($("#email"), response.message);
          return;
        }

        // ✅ SUCCESS → REDIRECT
        if (response.status === "success") {
          localStorage.setItem("isLogin", true);
          localStorage.setItem("session_id", response.session_id);
          localStorage.setItem("emailid", response.data.emailid);

          // Go to welcome page
          window.location.href = "/html/welcome.html";
        }
      },

      error: function (xhr) {
        console.log("Signup xhr:", xhr);

        if (xhr.status === 409) {
          showFieldError($("#email"), "Email already exists");
        } else {
          showFieldError($("#email"), "Server error. Try again.");
        }
      },
    });
  });
});
