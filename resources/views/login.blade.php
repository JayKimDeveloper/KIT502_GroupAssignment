<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
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
          <img src="{{ asset('images/login_bg1.jpg') }}" alt="Login background">
        </div>

        <div class="login-form-box">

          <h2>Login</h2>

          {{-- Show validation errors from AuthController --}}
          @if ($errors->any())
            <div class="error-box">
              @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
              @endforeach
            </div>
          @endif

          {{--
            FIXES:
            - method="POST"    so it hits AuthController@login
            - action="/login"  so it goes to the right endpoint
            - @csrf            Laravel CSRF token (required)
            - name="..."       on each input so the form data actually sends
            - Removed the 'role' select — login doesn't need it. Role is set
              at registration and stored against the user record. Asking for
              role at login lets someone elevate by picking the wrong option.
          --}}
          <form id="login-form" method="POST" action="{{ url('/login') }}">
            @csrf

            <div class="form-group">
                <label for="loginEmail">Email</label>
                <input type="email" id="loginEmail" name="email"
                       value="{{ old('email') }}"
                       placeholder="Email" required>
            </div>

            <div class="form-group">
                <label for="loginPassword">Password</label>
                <input type="password" id="loginPassword" name="password"
                       placeholder="Password" required>
            </div>

            {{-- Remember Me (Tutorial 5 requirement) --}}
            <div class="form-group">
                <label>
                    <input type="checkbox" name="remember" value="1"> Remember Me
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            <p>Don't have an account? <a href="{{ url('/register') }}">Sign Up</a></p>
          </form>
        </div>

      </div>

    </div>
  </div>

<script src="{{ asset('js/login_script.js') }}"></script>
</body>
</html>