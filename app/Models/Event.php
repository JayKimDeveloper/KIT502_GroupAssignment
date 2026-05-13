<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
 
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
        'end_datetime'   => 'datetime',
        'price'          => 'decimal:2',
        'capacity'       => 'integer',
    ];
 
    /* ------------------------------------------------------------------ */
    /* Relationships                                                       */
    /* ------------------------------------------------------------------ */
 
    public function organiser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organiser_id');
    }
 
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
 
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
 
    /** Only count confirmed bookings against capacity. */
    public function confirmedBookings(): HasMany
    {
        return $this->hasMany(Booking::class)->where('status', 'confirmed');
    }
 
    /* ------------------------------------------------------------------ */
    /* Scopes — keep public-event queries terse in controllers.            */
    /* ------------------------------------------------------------------ */
 
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }
 
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('start_datetime', '>=', now());
    }
 
    /* ------------------------------------------------------------------ */
    /* Capacity logic — used by booking controller before creating a row.  */
    /* ------------------------------------------------------------------ */
 
    public function availableSeats(): int
    {
        return max(0, $this->capacity - $this->confirmedBookings()->count());
    }
 
    public function isFull(): bool
    {
        return $this->availableSeats() === 0;
    }
}
 