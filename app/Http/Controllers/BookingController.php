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
    //
        public function store(Request $request): JsonResponse
    {
        $user = $request->user();
 
        // Only attendees can buy tickets (spec §6).
        if (! $user->isAttendee()) {
            return response()->json([
                'message' => 'Only attendees can purchase tickets.',
            ], 403);
        }
 
        $data = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
        ]);
 
        // Wrap in transaction with row-level lock so two simultaneous
        // ticket purchases can't both squeeze in past the capacity limit.
        // (Important on shared servers where requests can overlap.)
        try {
            $booking = DB::transaction(function () use ($data, $user) {
                /** @var Event $event */
                $event = Event::lockForUpdate()->find($data['event_id']);
 
                // Must be published — drafts/cancelled events not bookable.
                if ($event->status !== 'published') {
                    abort(response()->json([
                        'message' => 'This event is not available for booking.',
                    ], 422));
                }
 
                // Don't allow booking events that have already started.
                if ($event->start_datetime->isPast()) {
                    abort(response()->json([
                        'message' => 'This event has already started.',
                    ], 422));
                }
 
                // Reject duplicates explicitly (DB unique constraint would
                // also catch this, but a friendly message is nicer).
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
 
                // Capacity check — count confirmed bookings only.
                $confirmedCount = $event->confirmedBookings()->count();
                if ($confirmedCount >= $event->capacity) {
                    abort(response()->json([
                        'message' => 'This event has reached its capacity.',
                    ], 409));
                }
 
                // Free events skip payment entirely; paid events start as 'unpaid'
                // so an optional payment simulation step can mark them 'paid'.
                $paymentStatus = ((float) $event->price === 0.0) ? 'free' : 'unpaid';
 
                return Booking::create([
                    'event_id'       => $event->id,
                    'attendee_id'    => $user->id,
                    'status'         => 'confirmed',
                    'payment_status' => $paymentStatus,
                ]);
            });
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            // Bubble up the JsonResponse we built inside the transaction.
            throw $e;
        }
 
        $booking->load('event');
 
        // TODO (Advanced — Notifications, 2.5 pts):
        // Notification::create([
        //     'user_id' => $user->id,
        //     'type'    => 'booking_confirmed',
        //     'message' => "Your booking {$booking->booking_reference} is confirmed.",
        //     'related_id' => $booking->id,
        // ]);
        // Notification::create([
        //     'user_id' => $booking->event->organiser_id,
        //     'type'    => 'new_registration',
        //     'message' => "{$user->name} registered for '{$booking->event->title}'.",
        //     'related_id' => $booking->id,
        // ]);
 
        return response()->json([
            'message' => 'Booking confirmed.',
            'data'    => $this->transform($booking),
        ], 201);
    }
 
    /* ================================================================== */
    /* GET /api/bookings/mine — the current user's bookings                */
    /* ================================================================== */
    public function mine(Request $request): JsonResponse
    {
        $user = $request->user();
 
        $bookings = Booking::query()
            ->where('attendee_id', $user->id)
            ->with('event.category', 'event.organiser')
            ->orderByDesc('created_at')
            ->get();
 
        return response()->json([
            'data' => $bookings->map(fn ($b) => $this->transform($b)),
        ]);
    }
 
    /* ================================================================== */
    /* DELETE /api/bookings/{id} — attendee cancels their own booking      */
    /* ================================================================== */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
 
        $booking = Booking::with('event')->find($id);
        if (! $booking) {
            return response()->json(['message' => 'Booking not found.'], 404);
        }
 
        // Ownership check — attendees can only cancel their own bookings.
        if ($booking->attendee_id !== $user->id) {
            return response()->json(['message' => 'You do not own this booking.'], 403);
        }
 
        // Already cancelled — nothing to do, but return cleanly rather than 500.
        if ($booking->status === 'cancelled') {
            return response()->json(['message' => 'Booking is already cancelled.'], 422);
        }
 
        // Cutoff: 1 day before event (spec §7).
        // canBeCancelled() lives on the Booking model and encapsulates this rule.
        if (! $booking->canBeCancelled()) {
            return response()->json([
                'message' => 'Cancellation cutoff has passed (1 day before the event).',
            ], 422);
        }
 
        $booking->update(['status' => 'cancelled']);
 
        // TODO (Advanced — Waitlist promotion, 5 pts):
        // If a waitlist exists for this event, promote the first waitlisted
        // user to 'confirmed' here and notify them.
 
        return response()->json(['message' => 'Booking cancelled.']);
    }
 
    /* ================================================================== */
    /* GET /api/events/{id}/bookings — organiser views attendees           */
    /* ================================================================== */
    public function forEvent(Request $request, int $eventId): JsonResponse
    {
        $user = $request->user();
 
        $event = Event::find($eventId);
        if (! $event) {
            return response()->json(['message' => 'Event not found.'], 404);
        }
 
        // Only the event's organiser (or admin) may see its attendee list.
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
 
    /* ================================================================== */
    /* Helpers                                                             */
    /* ================================================================== */
 
    /**
     * Shape a Booking for JSON output. Matches API_CONTRACT.md.
     */
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
