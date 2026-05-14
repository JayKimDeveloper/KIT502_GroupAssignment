<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
 


class EventController extends Controller
{
    //


    /**
     * Author: YoungHyun Kim
     * GET /api/events
     * 
     * Lists event, filter: category_id, location
     * 
     */
    public function index(Request $request): JsonResponse
    {

        $query = Event::query()
            ->published()
            ->with(['category', 'organiser'])
            ->withCount(['bookings as confirmed_bookings_count' => function ($q) {
                $q->where('status', 'confirmed');
            }]);


            if ($request->filled('category_id')) {
                $query->where('category_id', $request->integer('category_id'));
            }
    
            if ($request->filled('date')) {
                // "Events on or after this date" — feels more useful than exact match.
                $query->whereDate('start_datetime', '>=', $request->date('date'));
            }
    
            if ($request->filled('location')) {
                // Substring search, case-insensitive on most DBs.
                $query->where('location', 'like', '%' . $request->string('location') . '%');
            }
    
            $events = $query->orderBy('start_datetime', 'asc')->get();
    
            return response()->json([
                'data' => $events->map(fn ($e) => $this->transform($e)),
            ]);

    }


        /**
     * GET /api/events/recent
     * Landing page: the 2 most recently created published events.
     */
    public function recent(): JsonResponse
    {
        $events = Event::query()
            ->published()
            ->with(['category', 'organiser'])
            ->withCount(['bookings as confirmed_bookings_count' => function ($q) {
                $q->where('status', 'confirmed');
            }])
            ->latest('created_at')
            ->take(2)
            ->get();
 
        return response()->json([
            'data' => $events->map(fn ($e) => $this->transform($e)),
        ]);
    }
 
    /**
     * GET /api/events/{id}
     * Single event detail. 404 if not found OR if it's a draft and the
     * requester isn't the organiser/admin (drafts shouldn't leak publicly).
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $event = Event::with(['category', 'organiser'])
            ->withCount(['bookings as confirmed_bookings_count' => function ($q) {
                $q->where('status', 'confirmed');
            }])
            ->find($id);
 
        if (! $event) {
            return response()->json(['message' => 'Event not found.'], 404);
        }
 
        // Drafts/cancelled hidden unless requester owns it or is admin.
        if ($event->status !== 'published') {
            $user = $request->user();
            $canSee = $user && ($user->isAdmin() || $event->organiser_id === $user->id);
            if (! $canSee) {
                return response()->json(['message' => 'Event not found.'], 404);
            }
        }
 
        return response()->json(['data' => $this->transform($event)]);
    }
 
    /* ================================================================== */
    /* Authenticated endpoints (organiser / admin)                         */
    /* ================================================================== */
 
    /**
     * GET /api/events/mine
     * Events created by the current organiser (Event Management page).
     * Admins get all events here too (so they can manage from one place).
     */
    public function mine(Request $request): JsonResponse
    {
        $user = $request->user();
 
        $query = Event::query()
            ->with(['category', 'organiser'])
            ->withCount(['bookings as confirmed_bookings_count' => function ($q) {
                $q->where('status', 'confirmed');
            }]);
 
        // Admin sees everything; organiser only their own.
        if (! $user->isAdmin()) {
            $query->where('organiser_id', $user->id);
        }
 
        $events = $query->orderBy('start_datetime', 'desc')->get();
 
        return response()->json([
            'data' => $events->map(fn ($e) => $this->transform($e)),
        ]);
    }
 
    /**
     * POST /api/events
     * Create a new event.
     *
     * Accepts multipart/form-data because of the optional image upload.
     * Image is stored in storage/app/public/events/ and served via /storage/.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
 
        // Only organisers and admins can create events.
        if (! ($user->isOrganiser() || $user->isAdmin())) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
 
        $data = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['required', 'string'],
            'start_datetime' => ['required', 'date'],
            'end_datetime'   => ['required', 'date', 'after_or_equal:start_datetime'],
            'location'       => ['required', 'string', 'max:255'],
            'capacity'       => ['required', 'integer', 'min:1'],
            'price'          => ['required', 'numeric', 'min:0'],
            'status'         => ['required', Rule::in(['draft', 'published', 'cancelled'])],
            'category_id'    => ['nullable', 'integer', 'exists:categories,id'],
            'image'          => ['nullable', 'image', 'max:2048'], // 2MB
        ]);
 
        // Always the current user is the organiser — never trust client input.
        $data['organiser_id'] = $user->id;
 
        // Handle image upload if present.
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('events', 'public');
        }
 
        $event = Event::create($data);
        $event->load(['category', 'organiser']);
 
        return response()->json([
            'message' => 'Event created.',
            'data'    => $this->transform($event),
        ], 201);
    }
 
    /**
     * PUT /api/events/{id}
     * Update an event. All fields optional (partial update).
     * Organiser can only update their own; admin can update any.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $event = Event::find($id);
        if (! $event) {
            return response()->json(['message' => 'Event not found.'], 404);
        }
 
        if ($denial = $this->ensureCanManage($request, $event)) {
            return $denial;
        }
 
        $data = $request->validate([
            'title'          => ['sometimes', 'required', 'string', 'max:255'],
            'description'    => ['sometimes', 'required', 'string'],
            'start_datetime' => ['sometimes', 'required', 'date'],
            'end_datetime'   => ['sometimes', 'required', 'date', 'after_or_equal:start_datetime'],
            'location'       => ['sometimes', 'required', 'string', 'max:255'],
            'capacity'       => ['sometimes', 'required', 'integer', 'min:1'],
            'price'          => ['sometimes', 'required', 'numeric', 'min:0'],
            'status'         => ['sometimes', 'required', Rule::in(['draft', 'published', 'cancelled'])],
            'category_id'    => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'image'          => ['sometimes', 'nullable', 'image', 'max:2048'],
        ]);
 
        // Capacity sanity check: don't allow reducing below already-confirmed bookings.
        if (isset($data['capacity'])) {
            $confirmed = $event->confirmedBookings()->count();
            if ($data['capacity'] < $confirmed) {
                return response()->json([
                    'message' => "Capacity cannot be less than the number of confirmed bookings ({$confirmed}).",
                ], 422);
            }
        }
 
        // Replace image: delete the old file to avoid storage clutter.
        if ($request->hasFile('image')) {
            if ($event->image_path) {
                Storage::disk('public')->delete($event->image_path);
            }
            $data['image_path'] = $request->file('image')->store('events', 'public');
        }
 
        $event->update($data);
        $event->load(['category', 'organiser']);
 
        return response()->json([
            'message' => 'Event updated.',
            'data'    => $this->transform($event),
        ]);
    }
 
    /**
     * DELETE /api/events/{id}
     * Delete an event. Same ownership rules as update.
     * Bookings cascade-delete via the FK constraint.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $event = Event::find($id);
        if (! $event) {
            return response()->json(['message' => 'Event not found.'], 404);
        }
 
        if ($denial = $this->ensureCanManage($request, $event)) {
            return $denial;
        }
 
        if ($event->image_path) {
            Storage::disk('public')->delete($event->image_path);
        }
 
        $event->delete();
 
        return response()->json(['message' => 'Event deleted.']);
    }
 
    /* ================================================================== */
    /* Helpers                                                             */
    /* ================================================================== */
 
    /**
     * Verify the request user owns the event, or is an admin.
     * Returns null if allowed; JsonResponse (to return) if forbidden.
     */
    private function ensureCanManage(Request $request, Event $event): ?JsonResponse
    {
        $user = $request->user();
 
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
 
        if ($user->isAdmin()) {
            return null;
        }
 
        if ($user->isOrganiser() && $event->organiser_id === $user->id) {
            return null;
        }
 
        return response()->json(['message' => 'You do not own this event.'], 403);
    }
 
    /**
     * Shape an Event model for JSON output. Centralised here so every
     * endpoint returns the same structure (matches API_CONTRACT.md).
     */
    private function transform(Event $event): array
    {
        return [
            'id'              => $event->id,
            'title'           => $event->title,
            'description'     => $event->description,
            'start_datetime'  => $event->start_datetime?->toISOString(),
            'end_datetime'    => $event->end_datetime?->toISOString(),
            'location'        => $event->location,
            'capacity'        => $event->capacity,
            'available_seats' => max(0, $event->capacity - ($event->confirmed_bookings_count ?? 0)),
            'price'           => (string) $event->price,
            'status'          => $event->status,
            'image_url'       => $event->image_path ? Storage::url($event->image_path) : null,
            'category'        => $event->category ? [
                'id'   => $event->category->id,
                'name' => $event->category->name,
            ] : null,
            'organiser'       => $event->organiser ? [
                'id'   => $event->organiser->id,
                'name' => $event->organiser->name,
            ] : null,
        ];
    }

    
}
