<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'organiser_id',
        'category_id',
        'title',
        'description',
        'start_datetime',
        'end_datetime',
        'location',
        'capacity',
        'price',
        'status',
        'image_path',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'price' => 'decimal:2',
        'capacity' => 'integer',
    ];

    public function organiser()
    {
        return $this->belongsTo(User::class, 'organiser_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function confirmedBookings()
    {
        return $this->hasMany(Booking::class)->where('status', 'confirmed');
    }

    // scope for published events only
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_datetime', '>=', now());
    }

    public function availableSeats()
    {
        return max(0, $this->capacity - $this->confirmedBookings()->count());
    }

    public function isFull()
    {
        return $this->availableSeats() === 0;
    }
}
