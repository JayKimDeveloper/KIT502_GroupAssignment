<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')
                  ->constrained('events')
                  ->cascadeOnDelete();
            $table->foreignId('attendee_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->string('booking_reference', 20)->unique();
            $table->enum('status', ['confirmed', 'cancelled'])->default('confirmed');
            $table->enum('payment_status', ['free', 'unpaid', 'paid'])->default('free');
            $table->timestamps();
 
            $table->unique(['event_id', 'attendee_id']);
            $table->index(['attendee_id', 'status']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
