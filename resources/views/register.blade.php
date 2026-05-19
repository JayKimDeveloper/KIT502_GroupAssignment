<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | TechEvents UTAS</title>
  <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
  <link rel="stylesheet" href="{{ asset('css/login_style.css') }}">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
@include('partials.navbar')

    
    <div class="page">
    <div class="container">
        <div class="register-box">

            <!-- Left side color panel -->
            <div class="register-panel">
                <h2>Welcome Back!</h2>
                <p>Already have an account? Log in to continue your journey with us.</p>
                <a href="{{ url('/login') }}" class="btn btn-panel">Log In</a>
            </div>

            <!-- Register form -->
            <div class="register-form-box">
                <h2>Create Account</h2>

                {{-- Show backend validation errors --}}
                @if ($errors->any())
                    <div class="error-box">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                {{--
                    FIXES (same as login.blade.php):
                    - method="POST"  so it hits AuthController@register
                    - action="/register"
                    - @csrf token
                    - name="..." on every input so values get sent
                    - name="password_confirmation" — this exact name is required
                      by Laravel's 'confirmed' validation rule that matches it
                      against the 'password' field
                --}}
                <form id="register-form" method="POST" action="{{ url('/register') }}">
                    @csrf

                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role" required>
                            <option value="">Select your role</option>
                            <option value="organiser" {{ old('role') === 'organiser' ? 'selected' : '' }}>Organiser</option>
                            <option value="attendee"  {{ old('role') === 'attendee'  ? 'selected' : '' }}>Attendee</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name"
                               value="{{ old('name') }}"
                               placeholder="Full name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                               value="{{ old('email') }}"
                               placeholder="Email address" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password"
                               placeholder="Password" required>
                        <small>
                            Min 6 characters, with at least one uppercase,
                            one lowercase, and one special character.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <input type="password" id="confirmPassword"
                               name="password_confirmation"
                               placeholder="Confirm password" required>
                    </div>

                    <div class="form-actions">
                        <button type="reset"  class="btn btn-outline" style="flex:1;">Reset</button>
                        <button type="submit" class="btn btn-primary" style="flex:1;">Register</button>
                    </div>
                </form>
              </div>

          </div>
      </div>
    </div>
    
    <script>window.APP_URL = "{{ url('/') }}";</script>
    <script src="{{ asset('js/register_script.js') }}"></script>
</body>
</html>