<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'role' => ['required', Rule::in(['attendee', 'organiser'])],
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                'min:6',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[^A-Za-z0-9]/',
            ],
        ], [
            'email.unique' => 'This email already exist. Please use different email.',
            'password.regex' => 'Password need uppercase, lowercase, and special character.',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Register success.',
                'user' => $user->only(['id', 'name', 'email', 'role']),
            ], 201);
        }

        return redirect()->intended('/')->with('status', 'Register success.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // check if email exist
        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'Email not registered.',
            ]);
        }

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'password' => 'Wrong password.',
            ]);
        }

        $request->session()->regenerate();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Login success.',
                'user' => Auth::user()->only(['id', 'name', 'email', 'role']),
            ]);
        }

        return redirect()->intended('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Logged out']);
        }

        return redirect('/');
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'authenticated' => $user ? true : false,
            'user' => $user ? $user->only(['id', 'name', 'email', 'role']) : null,
        ]);
    }
}
