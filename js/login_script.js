function goToLogin() {
  window.location.href = "login.html";
}

function goToSignup() {
  window.location.href = "signup.html";
}

function signup() {
  let name = document.getElementById("name").value;
  let email = document.getElementById("email").value;
  let password = document.getElementById("password").value;

  if (name === "" || email === "" || password === "") {
    alert("Please fill all fields");
    return;
  }

  localStorage.setItem("email", email);
  localStorage.setItem("password", password);

  alert("Signup successful!");
  window.location.href = "login.html";
}

function login() {
  let email = document.getElementById("loginEmail").value;
  let password = document.getElementById("loginPassword").value;

  let storedEmail = localStorage.getItem("email");
  let storedPassword = localStorage.getItem("password");

  if (email === storedEmail && password === storedPassword) {
    alert("Login successful!");
    window.location.href = "index.html";
  } else {
    alert("Invalid credentials");
  }
}