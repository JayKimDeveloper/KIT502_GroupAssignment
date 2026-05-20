<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'attendee_id',
        'booking_reference',
        'status',
        'quantity',
        'payment_status',
    ];

    // auto generate booking reference when creating
    protected static function booted(): void
    {
        static::creating(function ($booking) {
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = 'BK-' . strtoupper(Str::random(8));
            }
        });
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function attendee()
    {
        return $this->belongsTo(User::class, 'attendee_id');
    }

    // check if booking can still be cancelled (1 day before event)
    public function canBeCancelled()
    {
        if ($this->status !== 'confirmed') {
            return false;
        }

        return $this->event->start_datetime->copy()->subDay()->isFuture();
    }
}
