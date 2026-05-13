<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;



class AuthController extends Controller
{
    public function register(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request([
            'role'     => ['required', Rule::in(['attendee', 'organiser'])],
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],

            'password' => [
                'required',
                'confirmed', // expects 'password_confirmation' field
                'min:6',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[^A-Za-z0-9]/',
            ],
            ], [
                'email.unique' => 'This email is already registered. Please use a different email.',
                'password.regex' => 'Password must include uppercase, lowercase, and a special character.',
            ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'], // auto-hashed by User model cast
            'role'     => $data['role'],
        ]);

        Auth::login($user);
        $request->session()->regenerate(); // prevent session fixation
 
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Registration successful.',
                'user'    => $user->only(['id', 'name', 'email', 'role']),
            ], 201);
        }

        return redirect()->intended('/')->with('status', 'Registration successful.');

    }

    public function login(Request $request): JsonResponse|RedirectResponse
    {   
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
 
        // Distinguish "email not registered" from "incorrect password"
        // because Tutorial 5 / Practice 5 show both messages separately.
        $user = User::where('email', $credentials['email'])->first();
 
        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'Email not registered.',
            ]);
        }
 
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'password' => 'Incorrect password.',
            ]);
        }
 
        $request->session()->regenerate();
 
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Login successful.',
                'user'    => Auth::user()->only(['id', 'name', 'email', 'role']),
            ]);
        }
 
        return redirect()->intended('/');
    }
 
    /* ---------------------------------------------------------------- */
    /* POST /logout                                                      */
    /* ---------------------------------------------------------------- */
    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        Auth::logout();
 
        // Invalidate the session and rotate the CSRF token to prevent reuse.
        $request->session()->invalidate();
        $request->session()->regenerateToken();
 
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Logged out.']);
        }
 
        return redirect('/');
    }
 
    /* ---------------------------------------------------------------- */
    /* GET /me — used by the navigation bar to render "Welcome, {name}". */
    /* ---------------------------------------------------------------- */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user(); // null if not logged in
 
        return response()->json([
            'authenticated' => (bool) $user,
            'user' => $user
                ? $user->only(['id', 'name', 'email', 'role'])
                : null,
        ]);
    }
}
