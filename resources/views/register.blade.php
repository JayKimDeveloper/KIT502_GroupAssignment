<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | TechEvents UTAS</title>
  <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/login_style.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
    
    @include('partials.navbar')

    <div class="page">
    <div class="container">
        <div class="register-box">

            <!-- Left side color panale -->
            <div class="register-panel">
                <h2>Welcome Back!</h2>
                <p>Already have an account? Log in to continue your journey with us.</p>
                <a href="login" class="btn btn-panel">Log In</a>
            </div>

            <!-- Register form -->
            <div class="register-form-box">
                <h2>Create Account</h2>

                <form id="register-form">
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role">
                            <option value="">Select your role</option>
                            <option value="organiser">Organiser</option>
                            <option value="attendee">Attendee</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" placeholder="Full name">
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" placeholder="Email address">
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" placeholder="Password">
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <input type="password" id="confirmPassword" placeholder="Confirm password">
                    </div>

                    <div class="form-actions">
                        <button type="reset" class="btn btn-outline" style="flex:1;">Reset</button>
                        <button type="submit" class="btn btn-primary" style="flex:1;">Register</button>
                    </div>
                </form>
              </div>

          </div>
      </div>
    </div>

    <script src="js/register_script.js"></script>
</body>
</html>