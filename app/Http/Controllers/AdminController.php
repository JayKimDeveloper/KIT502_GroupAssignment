<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    // dashboard stats
    public function stats(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $totalUsers = User::count();
        $totalEvents = Event::count();
        $upcomingEvents = Event::where('start_datetime', '>=', now())->count();
        $totalRegistrations = Booking::where('status', 'confirmed')->count();

        return response()->json([
            'data' => [
                'total_users' => $totalUsers,
                'total_events' => $totalEvents,
                'upcoming_events' => $upcomingEvents,
                'total_registrations' => $totalRegistrations,
            ],
        ]);
    }

    // get all users
    public function users(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $users = User::orderBy('created_at', 'desc')->get();

        $result = [];
        foreach ($users as $u) {
            $result[] = [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role,
                'created_at' => $u->created_at ? $u->created_at->toISOString() : null,
            ];
        }

        return response()->json(['data' => $result]);
    }

    // admin create new user
    public function storeUser(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'role' => ['required', Rule::in(['admin', 'organiser', 'attendee'])],
        ]);

        $newUser = User::create($data);

        return response()->json([
            'message' => 'User created.',
            'data' => [
                'id' => $newUser->id,
                'name' => $newUser->name,
                'email' => $newUser->email,
                'role' => $newUser->role,
                'created_at' => $newUser->created_at ? $newUser->created_at->toISOString() : null,
            ],
        ], 201);
    }

    // update user
    public function updateUser(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $targetUser = User::find($id);
        if (!$targetUser) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($targetUser->id)],
            'password' => 'sometimes|nullable|confirmed|min:6',
            'role' => ['sometimes', 'required', Rule::in(['admin', 'organiser', 'attendee'])],
        ]);

        // if password empty, dont update it
        if (array_key_exists('password', $data) && empty($data['password'])) {
            unset($data['password']);
        }

        $targetUser->update($data);

        return response()->json([
            'message' => 'User updated.',
            'data' => [
                'id' => $targetUser->id,
                'name' => $targetUser->name,
                'email' => $targetUser->email,
                'role' => $targetUser->role,
                'created_at' => $targetUser->created_at ? $targetUser->created_at->toISOString() : null,
            ],
        ]);
    }

    // change user role (promote/demote)
    public function updateUserRole(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $targetUser = User::find($id);
        if (!$targetUser) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $data = $request->validate([
            'role' => ['required', Rule::in(['admin', 'organiser', 'attendee'])],
        ]);

        $targetUser->update(['role' => $data['role']]);

        return response()->json([
            'message' => 'Role updated.',
            'data' => [
                'id' => $targetUser->id,
                'name' => $targetUser->name,
                'email' => $targetUser->email,
                'role' => $targetUser->role,
                'created_at' => $targetUser->created_at ? $targetUser->created_at->toISOString() : null,
            ],
        ]);
    }

    public function destroyUser(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // cant delete yourself
        if ($user->id === $id) {
            return response()->json([
                'message' => 'You cant delete your own account.',
            ], 422);
        }

        $targetUser = User::find($id);
        if (!$targetUser) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $targetUser->delete();

        return response()->json(['message' => 'User deleted']);
    }

    // admin see all events including draft
    public function events(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $events = Event::with(['category', 'organiser'])
            ->withCount(['bookings as confirmed_bookings_count' => function ($query) {
                $query->where('status', 'confirmed');
            }])
            ->orderBy('start_datetime', 'desc')
            ->get();

        $result = [];
        foreach ($events as $event) {
            $confirmed = $event->confirmed_bookings_count ?? 0;
            $result[] = [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'start_datetime' => $event->start_datetime ? $event->start_datetime->toISOString() : null,
                'end_datetime' => $event->end_datetime ? $event->end_datetime->toISOString() : null,
                'location' => $event->location,
                'capacity' => $event->capacity,
                'registered_count' => $confirmed,
                'available_seats' => max(0, $event->capacity - $confirmed),
                'price' => (string)$event->price,
                'status' => $event->status,
                'image_url' => $event->image_path ? Storage::url($event->image_path) : null,
                'category' => $event->category ? ['id' => $event->category->id, 'name' => $event->category->name] : null,
                'organiser' => $event->organiser ? ['id' => $event->organiser->id, 'name' => $event->organiser->name] : null,
            ];
        }

        return response()->json(['data' => $result]);
    }
}
