<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    /**
     * POST /api/bookings
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->isAttendee()) {
            return response()->json([
                'message' => 'Only attendees can purchase tickets.',
            ], 403);
        }

        $data = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
        ]);

        try {
            $booking = DB::transaction(function () use ($data, $user) {
                $event = Event::lockForUpdate()->find($data['event_id']);

                if ($event->status !== 'published') {
                    abort(response()->json([
                        'message' => 'This event is not available for booking.',
                    ], 422));
                }

                if ($event->start_datetime->isPast()) {
                    abort(response()->json([
                        'message' => 'This event has already started.',
                    ], 422));
                }

                $existing = Booking::where('event_id', $event->id)
                    ->where('attendee_id', $user->id)
                    ->where('status', 'confirmed')
                    ->exists();

                if ($existing) {
                    abort(response()->json([
                        'message' => 'You have already booked this event.',
                        'errors'  => ['event_id' => ['You have already booked this event.']],
                    ], 422));
                }

                $confirmedCount = $event->confirmedBookings()->count();

                if ($confirmedCount >= $event->capacity) {
                    abort(response()->json([
                        'message' => 'This event has reached its capacity.',
                    ], 409));
                }

                $paymentStatus = ((float) $event->price === 0.0) ? 'free' : 'unpaid';

                return Booking::create([
                    'event_id'       => $event->id,
                    'attendee_id'    => $user->id,
                    'status'         => 'confirmed',
                    'payment_status' => $paymentStatus,
                ]);
            });
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        }

        $booking->load('event');

        return response()->json([
            'message' => 'Booking confirmed.',
            'data'    => $this->transform($booking),
        ], 201);
    }

    /**
     * GET /api/bookings/mine
     */
    public function mine(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->isAttendee()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $bookings = Booking::query()
            ->where('attendee_id', $user->id)
            ->with('event.category', 'event.organiser')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $bookings->map(fn ($b) => $this->transform($b)),
        ]);
    }

    /**
     * DELETE /api/bookings/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $booking = Booking::with('event')->find($id);

        if (! $booking) {
            return response()->json(['message' => 'Booking not found.'], 404);
        }

        if ($booking->attendee_id !== $user->id) {
            return response()->json(['message' => 'You do not own this booking.'], 403);
        }

        if ($booking->status === 'cancelled') {
            return response()->json(['message' => 'Booking is already cancelled.'], 422);
        }

        if (! $booking->canBeCancelled()) {
            return response()->json([
                'message' => 'Cancellation cutoff has passed (1 day before the event).',
            ], 422);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Booking cancelled.']);
    }

    /**
     * GET /api/events/{id}/bookings
     */
    public function forEvent(Request $request, int $eventId): JsonResponse
    {
        $user = $request->user();

        $event = Event::find($eventId);

        if (! $event) {
            return response()->json(['message' => 'Event not found.'], 404);
        }

        $allowed = $user->isAdmin() ||
            ($user->isOrganiser() && $event->organiser_id === $user->id);

        if (! $allowed) {
            return response()->json(['message' => 'You do not own this event.'], 403);
        }

        $bookings = Booking::query()
            ->where('event_id', $event->id)
            ->with('attendee:id,name,email')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $bookings->map(function (Booking $b) {
                return [
                    'id'                => $b->id,
                    'booking_reference' => $b->booking_reference,
                    'status'            => $b->status,
                    'payment_status'    => $b->payment_status,
                    'created_at'        => $b->created_at?->toISOString(),
                    'attendee'          => [
                        'id'    => $b->attendee->id,
                        'name'  => $b->attendee->name,
                        'email' => $b->attendee->email,
                    ],
                ];
            }),
        ]);
    }

    private function transform(Booking $b): array
    {
        return [
            'id'                => $b->id,
            'booking_reference' => $b->booking_reference,
            'status'            => $b->status,
            'payment_status'    => $b->payment_status,
            'can_be_cancelled'  => $b->canBeCancelled(),
            'created_at'        => $b->created_at?->toISOString(),
            'event'             => $b->event ? [
                'id'             => $b->event->id,
                'title'          => $b->event->title,
                'description'    => $b->event->description,
                'start_datetime' => $b->event->start_datetime?->toISOString(),
                'end_datetime'   => $b->event->end_datetime?->toISOString(),
                'location'       => $b->event->location,
                'price'          => (string) $b->event->price,
                'image_url'      => $b->event->image_path
                    ? Storage::url($b->event->image_path)
                    : null,
            ] : null,
        ];
    }
}