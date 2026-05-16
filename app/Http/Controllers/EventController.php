<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
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
            $query->whereDate('start_datetime', '>=', $request->date('date'));
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->string('location') . '%');
        }

        $events = $query->orderBy('start_datetime', 'asc')->get();

        return response()->json([
            'data' => $events->map(fn ($e) => $this->transform($e)),
        ]);
    }

    /**
     * GET /api/events/recent
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

        if ($event->status !== 'published') {
            $user = $request->user();
            $canSee = $user && ($user->isAdmin() || $event->organiser_id === $user->id);

            if (! $canSee) {
                return response()->json(['message' => 'Event not found.'], 404);
            }
        }

        return response()->json(['data' => $this->transform($event)]);
    }

    /**
     * GET /api/events/mine
     */
    public function mine(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! ($user->isOrganiser() || $user->isAdmin())) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $query = Event::query()
            ->with(['category', 'organiser'])
            ->withCount(['bookings as confirmed_bookings_count' => function ($q) {
                $q->where('status', 'confirmed');
            }]);

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
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

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
            'image'          => ['nullable', 'image', 'max:2048'],
        ]);

        $data['organiser_id'] = $user->id;

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

        if (isset($data['capacity'])) {
            $confirmed = $event->confirmedBookings()->count();

            if ($data['capacity'] < $confirmed) {
                return response()->json([
                    'message' => "Capacity cannot be less than the number of confirmed bookings ({$confirmed}).",
                ], 422);
            }
        }

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

    private function transform(Event $event): array
    {
        $confirmedBookings = $event->confirmed_bookings_count ?? $event->confirmedBookings()->count();

        return [
            'id'               => $event->id,
            'title'            => $event->title,
            'description'      => $event->description,
            'start_datetime'   => $event->start_datetime?->toISOString(),
            'end_datetime'     => $event->end_datetime?->toISOString(),
            'location'         => $event->location,
            'capacity'         => $event->capacity,
            'registered_count' => $confirmedBookings,
            'available_seats'  => max(0, $event->capacity - $confirmedBookings),
            'price'            => (string) $event->price,
            'status'           => $event->status,
            'image_url'        => $event->image_path ? Storage::url($event->image_path) : null,
            'category'         => $event->category ? [
                'id'   => $event->category->id,
                'name' => $event->category->name,
            ] : null,
            'organiser'        => $event->organiser ? [
                'id'   => $event->organiser->id,
                'name' => $event->organiser->name,
            ] : null,
        ];
    }
}