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



/**
 * Check the login data form
 * 1. email format
 * 2. password blank
*/ 
document.getElementById('login-form').addEventListener('submit', function(e) {
    e.preventDefault();

    let email = document.getElementById("loginEmail").value.trim();
    let password = document.getElementById("loginPassword").value.trim();

    if (email === "" || password === "") {
        alert("Please fill in all fields.");
        return;
    }

    window.location.href = "index.html";
});
