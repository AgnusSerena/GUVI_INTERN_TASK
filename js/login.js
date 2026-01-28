$(document).ready(function () {
  // ===============================
  // AUTO-FILL RECENTLY USED EMAIL
  // ===============================
  const lastEmail = localStorage.getItem("email");
  if (lastEmail) {
    $("#email").val(lastEmail);
  }

  // ===============================
  // LOGIN BUTTON CLICK
  // ===============================
  $("#loginBtn").click(function () {
    let email = $("#email").val().trim();
    let password = $("#password").val().trim();

    // Clear old errors
    $("#emailError").text("");
    $("#passwordError").text("");
    $("#email").removeClass("is-invalid");
    $("#password").removeClass("is-invalid");

    let hasError = false;

    // Frontend validations
    if (email === "") {
      $("#emailError").text("Email is required");
      $("#email").addClass("is-invalid");
      hasError = true;
    }

    if (password === "") {
      $("#passwordError").text("Password is required");
      $("#password").addClass("is-invalid");
      hasError = true;
    }

    if (hasError) return;

    // ===============================
    // AJAX LOGIN REQUEST
    // ===============================
    $.ajax({
      url: "http://localhost:8000/php/login.php",
      type: "POST",
      dataType: "json",
      data: { input1: email, input2: password },

      success: function (response) {
        // LOGIN FAILED → show inline error
        if (!response.status) {
          if (response.message === "No user found") {
            $("#emailError").text("Email not registered");
            $("#email").addClass("is-invalid");
          }

          if (response.message === "Incorrect password") {
            $("#passwordError").text("Incorrect password");
            $("#password").addClass("is-invalid");
          }

          return;
        }

        // ===============================
        // LOGIN SUCCESS
        // ===============================

        // Save session + last used email
        localStorage.setItem("session_id", response.session_id);
        localStorage.setItem("email", response.data.emailid);
        localStorage.setItem("mongoDbId", response.data.mongoDbId);

        // Redirect to profile page
        window.location.href = "/html/profile.html";
      },

      error: function () {
        $("#passwordError").text("Server error! Please try again.");
        $("#password").addClass("is-invalid");
      },
    });
  });
});
