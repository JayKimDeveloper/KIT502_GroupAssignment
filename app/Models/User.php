<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
 
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];
 
    protected $hidden = [
        'password',
        'remember_token',
    ];
 
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Laravel 10 auto-hashes on set
    ];
 
    /* ------------------------------------------------------------------ */
    /* Relationships                                                       */
    /* ------------------------------------------------------------------ */
 
    /** Events this user has created (when role = organiser/admin). */
    public function organisedEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'organiser_id');
    }
 
    /** Bookings this user has made (when role = attendee). */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'attendee_id');
    }
 
    /** Notifications addressed to this user. */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
 
    /* ------------------------------------------------------------------ */
    /* Role helpers — used in controllers/middleware for authorisation.    */
    /* ------------------------------------------------------------------ */
 
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
 
    public function isOrganiser(): bool
    {
        return $this->role === 'organiser';
    }
 
    public function isAttendee(): bool
    {
        return $this->role === 'attendee';
    }

}
