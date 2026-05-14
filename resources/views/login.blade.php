<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <title>Login | TechEvents UTAS</title>
  <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
  <link rel="stylesheet" href="{{ asset('css/login_style.css') }}">
</head>
<body>

@include('partials.navbar')

    
  <div class="page">

    <div class="container">

      <div class="login-box">

        <div class="login-form-image">
          <img src="images/login_bg1.jpg">
        </div>

        <div class="login-form-box">

          <h2>Login</h2>


          <form id="login-form">

            <label for="role">Role</label>
            <select id="role" name="role">
                <option value="">Select your role</option>
                <option value="organiser">Organiser</option>
                <option value="attendee">Attendee</option>
            </select>


            <div class="form-group">
                <label for="loginEmail">Email</label>
                <input type="email" id="loginEmail" placeholder="Email" required>
            </div>

            <div class="form-group">
                <label for="loginPassword">Password</label>
                <input type="password" id="loginPassword" placeholder="Password" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            <p>Don't have an account? <a href="register">Sign Up</a></p>
          </form>
      </div>


      </div>

    </div>
  </div>

<script src="js/login_script.js"></script>
</body>
</html>
