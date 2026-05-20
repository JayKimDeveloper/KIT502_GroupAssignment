<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    // list all published events with optional filters
    public function index(Request $request)
    {
        $query = Event::where('status', 'published')
            ->with(['category', 'organiser'])
            ->withCount(['bookings as confirmed_bookings_count' => function ($q) {
                $q->where('status', 'confirmed');
            }]);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('start_datetime', '>=', $request->date);
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $events = $query->orderBy('start_datetime', 'asc')->get();

        $result = [];
        foreach ($events as $event) {
            $result[] = $this->formatEvent($event);
        }

        return response()->json(['data' => $result]);
    }

    // get 2 most recent events for homepage
    public function recent()
    {
        $events = Event::where('status', 'published')
            ->with(['category', 'organiser'])
            ->withCount(['bookings as confirmed_bookings_count' => function ($q) {
                $q->where('status', 'confirmed');
            }])
            ->latest('created_at')
            ->take(2)
            ->get();

        $result = [];
        foreach ($events as $event) {
            $result[] = $this->formatEvent($event);
        }

        return response()->json(['data' => $result]);
    }

    // show single event detail
    public function show($id)
    {
        $event = Event::with(['category', 'organiser'])
            ->withCount(['bookings as confirmed_bookings_count' => function ($q) {
                $q->where('status', 'confirmed');
            }])
            ->find($id);

        if (!$event || $event->status !== 'published') {
            abort(404, 'Event not found');
        }

        return view('event_detail', ['data' => $event]);
    }

    // get events for logged in organiser or admin
    public function mine(Request $request)
    {
        $user = $request->user();

        if (!$user->isOrganiser() && !$user->isAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $query = Event::with(['category', 'organiser'])
            ->withCount(['bookings as confirmed_bookings_count' => function ($q) {
                $q->where('status', 'confirmed');
            }]);

        // admin see all, organiser only their own
        if (!$user->isAdmin()) {
            $query->where('organiser_id', $user->id);
        }

        $events = $query->orderBy('start_datetime', 'desc')->get();

        $result = [];
        foreach ($events as $event) {
            $result[] = $this->formatEvent($event);
        }

        return response()->json(['data' => $result]);
    }

    // create new event
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->isOrganiser() && !$user->isAdmin()) {
            return response()->json(['message' => 'You dont have permission'], 403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'status' => ['required', Rule::in(['draft', 'published', 'cancelled'])],
            'category_id' => 'nullable|integer|exists:categories,id',
            'image' => 'nullable|image|max:2048',
        ]);

        $data['organiser_id'] = $user->id;

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('events', 'public');
        }

        $event = Event::create($data);
        $event->load(['category', 'organiser']);

        return response()->json([
            'message' => 'Event created successfully.',
            'data' => $this->formatEvent($event),
        ], 201);
    }

    // update existing event
    public function update(Request $request, $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        if (!$user->isAdmin() && !($user->isOrganiser() && $event->organiser_id === $user->id)) {
            return response()->json(['message' => 'You dont own this event'], 403);
        }

        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'start_datetime' => 'sometimes|required|date',
            'end_datetime' => 'sometimes|required|date|after_or_equal:start_datetime',
            'location' => 'sometimes|required|string|max:255',
            'capacity' => 'sometimes|required|integer|min:1',
            'price' => 'sometimes|required|numeric|min:0',
            'status' => ['sometimes', 'required', Rule::in(['draft', 'published', 'cancelled'])],
            'category_id' => 'sometimes|nullable|integer|exists:categories,id',
            'image' => 'sometimes|nullable|image|max:2048',
        ]);

        // check capacity not less than current bookings
        if (isset($data['capacity'])) {
            $confirmed = $event->confirmedBookings()->count();
            if ($data['capacity'] < $confirmed) {
                return response()->json([
                    'message' => "Capacity cant be less than confirmed bookings ({$confirmed})",
                ], 422);
            }
        }

        // replace old image if new one uploaded
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
            'data' => $this->formatEvent($event),
        ]);
    }

    // delete event
    public function destroy(Request $request, $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        if (!$user->isAdmin() && !($user->isOrganiser() && $event->organiser_id === $user->id)) {
            return response()->json(['message' => 'You dont own this event'], 403);
        }

        // delete image file too
        if ($event->image_path) {
            Storage::disk('public')->delete($event->image_path);
        }

        $event->delete();

        return response()->json(['message' => 'Event deleted']);
    }

    // format event for response
    private function formatEvent($event)
    {
        $confirmed = $event->confirmed_bookings_count ?? $event->confirmedBookings()->count();

        $data = [
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
            'category' => null,
            'organiser' => null,
        ];

        if ($event->category) {
            $data['category'] = [
                'id' => $event->category->id,
                'name' => $event->category->name,
            ];
        }
        if ($event->organiser) {
            $data['organiser'] = [
                'id' => $event->organiser->id,
                'name' => $event->organiser->name,
            ];
        }

        return $data;
    }
}
