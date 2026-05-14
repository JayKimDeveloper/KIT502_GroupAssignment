function goToLogin() {
    window.location.href = "/login";
}
 
function goToSignup() {
    window.location.href = "/register";
}
 
document.getElementById('login-form').addEventListener('submit', function (e) {
    const email    = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value.trim();
 
    // Block submit only if invalid; otherwise let the browser do its thing.
    if (email === '' || password === '') {
        e.preventDefault();
        alert('Please fill in all fields.');
        return;
    }
 
    // Basic email format check.
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        e.preventDefault();
        alert('Please enter a valid email address.');
        return;
    }
 
});