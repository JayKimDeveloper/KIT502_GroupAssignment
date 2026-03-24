$(document).ready(function(e){

    $('#register-form').on('submit', function (e) {
        e.preventDefault();

        const role = $('#role').val();
        const name = $('#name').val().trim();
        const email = $('#email').val().trim();
        const password = $('#password').val().trim();
        const confirmPassword = $('#confirmPassword').val().trim();

        // Empty fields check
        if (role === '' || name === '' || email === '' || password === '' || confirmPassword === '') {
            alert('Please fill in all fields.');
            return;
        }

        // email format check
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Please enter a valid email address.');
            return;
        }

        // password format check
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{6,}$/;
        if (!passwordRegex.test(password)) {
            alert('Password must be at least 6 characters and include uppercase, lowercase, and a special character.');
            return;
        }

        // password match check
        if (password !== confirmPassword) {
            alert('Passwords do not match.');
            return;
        }

        // If success, move to index.html
        window.location.href = 'index.html';


    })

})