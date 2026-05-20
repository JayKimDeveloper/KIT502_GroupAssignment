<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    // buy ticket - create booking
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->isAttendee()) {
            return response()->json([
                'message' => 'Only attendee can buy ticket.',
            ], 403);
        }

        $data = $request->validate([
            'event_id' => 'required|integer|exists:events,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $quantity = $data['quantity'];

        $event = Event::find($data['event_id']);

        if ($event->status !== 'published') {
            return response()->json([
                'message' => 'This event is not available.',
            ], 422);
        }

        if ($event->start_datetime->isPast()) {
            return response()->json([
                'message' => 'Event already started.',
            ], 422);
        }

        $paymentStatus = ((float) $event->price === 0.0) ? 'free' : 'unpaid';

        // Check existing booking for this event and user
        $existingBooking = Booking::where('event_id', $event->id)
            ->where('attendee_id', $user->id)
            ->first();

        // If already confirmed, do not create another booking
        if ($existingBooking && $existingBooking->status === 'confirmed') {
            return response()->json([
                'message' => 'You already booked this event.',
                'errors' => [
                    'event_id' => ['You already booked this event.'],
                ],
            ], 422);
        }


        // check the already sold ticekt
        $soldTickets = Booking::where('event_id', $event->id)
            ->where('status', 'confirmed')
            ->sum('quantity');

        if ($soldTickets + $quantity > $event->capacity) {
            return response()->json([
                'message' => 'Not enough seats available.',
            ], 409);
        }

        if ($soldTickets + $quantity > $event->capacity) {
            return response()->json([
                'message' => 'Not enough seats available.',
            ], 409);
        }

        // If the user had cancelled before, update the existing row instead of inserting a new one
        if ($existingBooking && $existingBooking->status === 'cancelled') {
            $existingBooking->update([
                'quantity' => $quantity,
                'status' => 'confirmed',
                'payment_status' => $paymentStatus,
            ]);

            $existingBooking->load('event');

            return response()->json([
                'message' => 'Booking confirmed!',
                'data' => $this->formatBooking($existingBooking),
            ], 200);
        }

        // Create new booking if no previous booking exists
        $booking = Booking::create([
            'event_id' => $event->id,
            'attendee_id' => $user->id,
            'quantity' => $quantity,
            'status' => 'confirmed',
            'payment_status' => $paymentStatus,
        ]);

        $booking->load('event');

        return response()->json([
            'message' => 'Booking confirmed!',
            'data' => $this->formatBooking($booking),
        ], 201);
    }

    
    // get my bookings
    public function mine(Request $request)
    {
        $user = $request->user();

        if (!$user->isAttendee()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $bookings = Booking::where('attendee_id', $user->id)
            ->with('event.category', 'event.organiser')
            ->orderByDesc('created_at')
            ->get();

        $result = [];
        foreach ($bookings as $booking) {
            $result[] = $this->formatBooking($booking);
        }

        return response()->json(['data' => $result]);
    }

    // cancel booking
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $booking = Booking::with('event')->find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        if ($booking->attendee_id !== $user->id) {
            return response()->json(['message' => 'This is not your booking'], 403);
        }

        if ($booking->status === 'cancelled') {
            return response()->json(['message' => 'Already cancelled'], 422);
        }

        if (!$booking->canBeCancelled()) {
            return response()->json([
                'message' => 'Cannot cancel, its less than 1 day before event.',
            ], 422);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Booking cancelled successfully']);
    }

    // get bookings for specific event (for organiser/admin)
    public function forEvent(Request $request, $eventId)
    {
        $user = $request->user();
        $event = Event::find($eventId);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        if (!$user->isAdmin() && !($user->isOrganiser() && $event->organiser_id === $user->id)) {
            return response()->json(['message' => 'You dont own this event'], 403);
        }

        $bookings = Booking::where('event_id', $event->id)
            ->with('attendee:id,name,email')
            ->orderByDesc('created_at')
            ->get();

        $result = [];
        foreach ($bookings as $b) {
            $result[] = [
                'id' => $b->id,
                'booking_reference' => $b->booking_reference,
                'status' => $b->status,
                'payment_status' => $b->payment_status,
                'created_at' => $b->created_at ? $b->created_at->toISOString() : null,
                'attendee' => [
                    'id' => $b->attendee->id,
                    'name' => $b->attendee->name,
                    'email' => $b->attendee->email,
                ],
            ];
        }

        return response()->json(['data' => $result]);
    }

    private function formatBooking($booking)
    {
        $eventData = null;
        if ($booking->event) {
            $eventData = [
                'id' => $booking->event->id,
                'title' => $booking->event->title,
                'description' => $booking->event->description,
                'start_datetime' => $booking->event->start_datetime ? $booking->event->start_datetime->toISOString() : null,
                'end_datetime' => $booking->event->end_datetime ? $booking->event->end_datetime->toISOString() : null,
                'location' => $booking->event->location,
                'price' => (string)$booking->event->price,
                'image_url' => $booking->event->image_path ? Storage::url($booking->event->image_path) : null,
            ];
        }

        return [
            'id' => $booking->id,
            'booking_reference' => $booking->booking_reference,
            'status' => $booking->status,
            'payment_status' => $booking->payment_status,
            'can_be_cancelled' => $booking->canBeCancelled(),
            'created_at' => $booking->created_at ? $booking->created_at->toISOString() : null,
            'event' => $eventData,
        ];
    }
}
