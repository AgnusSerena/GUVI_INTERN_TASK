// ❌ DO NOT REDIRECT ON PAGE LOAD
// We removed the "isLogin" check completely.

const form = document.getElementById("form");

// -----------------------------
// Helper Functions
// -----------------------------
function setError(element, message) {
  const formControl = element.parentElement;
  formControl.className = "form-control error";
  formControl.querySelector("small").innerText = message;
}

function setSuccess(element) {
  const formControl = element.parentElement;
  formControl.className = "form-control success";
}

function ValidateEmail(emailValue) {
  return /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(emailValue);
}

function checkInput() {
  const email = document.getElementById("email");
  const password = document.getElementById("password");
  const cpassword = document.getElementById("cpassword");

  let valid = true;

  // Email
  if (email.value.trim() === "") {
    setError(email, "Email is required");
    valid = false;
  } else if (!ValidateEmail(email.value.trim())) {
    setError(email, "Invalid Email");
    valid = false;
  } else {
    setSuccess(email);
  }

  // Password
  if (password.value.trim() === "") {
    setError(password, "Password is required");
    valid = false;
  } else if (password.value.trim() !== cpassword.value.trim()) {
    setError(password, "Passwords do not match");
    valid = false;
  } else {
    setSuccess(password);
  }

  // Confirm Password
  if (cpassword.value.trim() === "") {
    setError(cpassword, "Re-enter password");
    valid = false;
  } else if (password.value.trim() !== cpassword.value.trim()) {
    setError(cpassword, "Passwords do not match");
    valid = false;
  } else {
    setSuccess(cpassword);
  }

  return valid;
}

// -----------------------------
// SUBMIT FORM
// -----------------------------
form.addEventListener("submit", (e) => {
  e.preventDefault();

  if (!checkInput()) return;

  const email = $("#email").val();
  const password = $("#password").val();

  $.ajax({
    url: "http://localhost:8000/php/signup.php",
    type: "POST",
    data: { input3: email, input2: password },
    success: function (response) {
      console.log(response);

      const { session_id, data } = response;

      // Save session
      localStorage.setItem("isLogin", "true");
      localStorage.setItem("session_id", session_id);
      localStorage.setItem("emailid", data.emailid);

      // Redirect to welcome page
      window.location.href = "welcome.html";
    },
    error: function () {
      setError(document.getElementById("email"), "Email already exists");
    },
  });
});
