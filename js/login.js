$(document).ready(function () {
  $("#form").on("submit", function (e) {
    e.preventDefault();

    let email = $("#email").val().trim();
    let password = $("#password").val().trim();

    if (email === "" || password === "") {
      $("#errorBox").text("Email and password required").fadeIn();
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
        if (!response.status) {
          $("#errorBox").text(response.message).fadeIn();
          return;
        }

        localStorage.setItem("isLogin", true);
        localStorage.setItem("emailid", response.data.emailid);
        localStorage.setItem("session_id", response.session_id);

        window.location.href = "/html/welcome.html";
      },
      error: function () {
        $("#errorBox").text("Server error. Try again.").fadeIn();
      },
    });
  });
});
