<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'attendee_id',
        'booking_reference',
        'status',
        'payment_status',
    ];

    /**
     * Auto-generate a unique booking_reference if one isn't supplied.
     * Format: BK-XXXXXXXX (8 uppercase chars). Collision-resistant for our scale.
     */
    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = 'BK-' . strtoupper(Str::random(8));
            }
        });
    }

    /* ------------------------------------------------------------------ */
    /* Relationships                                                       */
    /* ------------------------------------------------------------------ */

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function attendee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attendee_id');
    }

    /* ------------------------------------------------------------------ */
    /* Business rule — cancellation cutoff is 1 day before event start    */
    /* (assignment spec, section 7 — Event Management for Attendees).      */
    /* ------------------------------------------------------------------ */

    public function canBeCancelled(): bool
    {
        if ($this->status !== 'confirmed') {
            return false;
        }
        return $this->event->start_datetime->subDay()->isFuture();
    }
}