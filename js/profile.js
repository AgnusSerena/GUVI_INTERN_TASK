$(document).ready(function () {
  // Get session ID
  const session_id = localStorage.getItem("session_id");

  if (!session_id) {
    window.location.href = "/html/login.html";
    return;
  }

  // -------------------------------
  // FETCH USER DATA FROM BACKEND
  // -------------------------------
  $.ajax({
    url: "http://localhost:8000/php/profile.php",
    type: "POST",
    dataType: "json",
    data: {
      action: "fetch",
      redisID: session_id,
    },

    success: function (response) {
      if (response.error) {
        localStorage.clear();
        window.location.href = "/html/login.html";
        return;
      }

      $("#email").val(response.email);
      $("#fullname").val(response.fullname);
      $("#age").val(response.age);
      $("#phone").val(response.phone);
    },
  });

  // -------------------------------
  // ENABLE EDIT MODE
  // -------------------------------
  $("#editBtn").click(function () {
    $("#fullname").prop("disabled", false);
    $("#age").prop("disabled", false);
    $("#phone").prop("disabled", false);

    $("#editBtn").addClass("d-none");
    $("#saveBtn").removeClass("d-none");
  });

  // -------------------------------
  // SAVE UPDATED DATA
  // -------------------------------
  $("#saveBtn").click(function () {
    const profileData = {
      fullname: $("#fullname").val().trim(),
      age: $("#age").val().trim(),
      phone: $("#phone").val().trim(),
    };

    $.ajax({
      url: "http://localhost:8000/php/profile.php",
      type: "POST",
      dataType: "json",
      data: {
        action: "update",
        redisID: session_id,
        emailid: $("#email").val(),
        profiledata: JSON.stringify(profileData),
      },

      success: function (response) {
        alert("Profile updated!");

        // After save → disable inputs
        $("#fullname").prop("disabled", true);
        $("#age").prop("disabled", true);
        $("#phone").prop("disabled", true);

        $("#saveBtn").addClass("d-none");
        $("#editBtn").removeClass("d-none");
      },
    });
  });

  // -------------------------------
  // LOGOUT
  // -------------------------------
  $("#logout").click(function () {
    localStorage.clear();
    window.location.href = "/html/login.html";
  });
});
