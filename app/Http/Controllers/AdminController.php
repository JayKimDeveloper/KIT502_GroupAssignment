<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * A small helper so every admin endpoint uses the same role check.
     */
    private function ensureAdmin(Request $request): ?JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (! $user->isAdmin()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return null;
    }

    /**
     * GET /api/admin/stats
     * Shows the main numbers used by the admin dashboard cards.
     */
    public function stats(Request $request): JsonResponse
    {
        if ($denial = $this->ensureAdmin($request)) {
            return $denial;
        }

        return response()->json([
            'data' => [
                'total_users' => User::count(),
                'total_events' => Event::count(),
                'upcoming_events' => Event::where('start_datetime', '>=', now())->count(),
                'total_registrations' => Booking::where('status', 'confirmed')->count(),
            ],
        ]);
    }

    /**
     * GET /api/admin/users
     * Lists all users for the admin user table.
     */
    public function users(Request $request): JsonResponse
    {
        if ($denial = $this->ensureAdmin($request)) {
            return $denial;
        }

        $users = User::orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $users->map(fn (User $user) => $this->transformUser($user)),
        ]);
    }

    /**
     * POST /api/admin/users
     * Creates a user. Admin can choose attendee, organiser, or admin.
     */
    public function storeUser(Request $request): JsonResponse
    {
        if ($denial = $this->ensureAdmin($request)) {
            return $denial;
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:6'],
            'role' => ['required', Rule::in(['admin', 'organiser', 'attendee'])],
        ]);

        $user = User::create($data);

        return response()->json([
            'message' => 'User created.',
            'data' => $this->transformUser($user),
        ], 201);
    }

    /**
     * PUT /api/admin/users/{id}
     * Updates a user. Password is optional.
     */
    public function updateUser(Request $request, int $id): JsonResponse
    {
        if ($denial = $this->ensureAdmin($request)) {
            return $denial;
        }

        $user = User::find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['sometimes', 'nullable', 'confirmed', 'min:6'],
            'role' => ['sometimes', 'required', Rule::in(['admin', 'organiser', 'attendee'])],
        ]);

        if (array_key_exists('password', $data) && empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'User updated.',
            'data' => $this->transformUser($user),
        ]);
    }

   
    public function updateUserRole(Request $request, int $id): JsonResponse
    {
        if ($denial = $this->ensureAdmin($request)) {
            return $denial;
        }

        $user = User::find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $data = $request->validate([
            'role' => ['required', Rule::in(['admin', 'organiser', 'attendee'])],
        ]);

        $user->update(['role' => $data['role']]);

        return response()->json([
            'message' => 'Role updated.',
            'data' => $this->transformUser($user),
        ]);
    }

    /**
     * DELETE /api/admin/users/{id}
     * Deletes a user, but blocks deleting your own logged-in admin account.
     */
    public function destroyUser(Request $request, int $id): JsonResponse
    {
        if ($denial = $this->ensureAdmin($request)) {
            return $denial;
        }

        if ($request->user()->id === $id) {
            return response()->json([
                'message' => 'You cannot delete your own admin account.',
            ], 422);
        }

        $user = User::find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted.']);
    }

    /**
     * GET /api/admin/events
     * Admin sees all events, including draft and cancelled events.
     */
    public function events(Request $request): JsonResponse
    {
        if ($denial = $this->ensureAdmin($request)) {
            return $denial;
        }

        $events = Event::query()
            ->with(['category', 'organiser'])
            ->withCount(['bookings as confirmed_bookings_count' => function ($query) {
                $query->where('status', 'confirmed');
            }])
            ->orderBy('start_datetime', 'desc')
            ->get();

        return response()->json([
            'data' => $events->map(fn (Event $event) => $this->transformEvent($event)),
        ]);
    }

    private function transformUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'created_at' => $user->created_at?->toISOString(),
        ];
    }

    private function transformEvent(Event $event): array
    {
        $confirmedBookings = $event->confirmed_bookings_count ?? 0;

        return [
            'id' => $event->id,
            'title' => $event->title,
            'description' => $event->description,
            'start_datetime' => $event->start_datetime?->toISOString(),
            'end_datetime' => $event->end_datetime?->toISOString(),
            'location' => $event->location,
            'capacity' => $event->capacity,
            'registered_count' => $confirmedBookings,
            'available_seats' => max(0, $event->capacity - $confirmedBookings),
            'price' => (string) $event->price,
            'status' => $event->status,
            'image_url' => $event->image_path ? Storage::url($event->image_path) : null,
            'category' => $event->category ? [
                'id' => $event->category->id,
                'name' => $event->category->name,
            ] : null,
            'organiser' => $event->organiser ? [
                'id' => $event->organiser->id,
                'name' => $event->organiser->name,
            ] : null,
        ];
    }
}