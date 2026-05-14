document.getElementById('register-form').addEventListener('submit', function (e) {
    const role     = document.getElementById('role').value;
    const name     = document.getElementById('name').value.trim();
    const email    = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirm  = document.getElementById('confirmPassword').value;
 
    // All fields required.
    if (!role || !name || !email || !password || !confirm) {
        e.preventDefault();
        alert('Please fill in all fields.');
        return;
    }
 
    // Email format.
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        e.preventDefault();
        alert('Please enter a valid email address.');
        return;
    }
 
    // Password and confirmation match.
    if (password !== confirm) {
        e.preventDefault();
        alert('Password and Confirm Password do not match.');
        return;
    }
 
    // Password policy: >=6, upper, lower, special.
    if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{6,}/.test(password)) {
        e.preventDefault();
        alert('Password must be at least 6 characters and include uppercase, lowercase, and a special character.');
        return;
    }
 
});