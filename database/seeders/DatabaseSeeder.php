<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Qazxsw135!@'),
            'role' => 'admin',
        ]);

        $organiser = User::create([
            'name'     => 'Test Organiser',
            'email'    => 'organiser@gmail.com',
            'password' => Hash::make('Qazxsw135!@'),
            'role'     => 'organiser',
        ]);
 
        $attendee = User::create([
            'name'     => 'Test Attendee',
            'email'    => 'attendee@gmail.com',
            'password' => Hash::make('Qazxsw135!@'),
            'role'     => 'attendee',
        ]);
 




        /* ---------- Categories ---------- */
 
        $categories = collect([
            'Workshop',
            'Tech Talk',
            'Hackathon',
            'Networking',
            'Conference',
        ])->map(fn ($name) => Category::create(['name' => $name]));
 


        /* ---------- Events ---------- */
        $eventsData = [
            [
                'title'       => 'Intro to Laravel for Students',
                'description' => 'Hands-on workshop covering routing, Eloquent, and Blade. Bring a laptop.',
                'days_ahead'  => 7,
                'location'    => 'Hobart Campus',
                'capacity'    => 30,
                'price'       => 0,
                'category'    => 'Workshop',
                'image_path'  => 'images/event1.png'
            ],
            [
                'title'       => 'Cybersecurity Career Panel',
                'description' => 'Industry panel discussion with security professionals from Hobart.',
                'days_ahead'  => 14,
                'location'    => 'Hobart Campus',
                'capacity'    => 100,
                'price'       => 5.00,
                'category'    => 'Tech Talk',
                'image_path'  => 'images/event2.png'
            ],
            [
                'title'       => 'UTAS Hackathon 2026',
                'description' => '24-hour hackathon — build something that helps the community. Prizes for top three teams.',
                'days_ahead'  => 21,
                'location'    => 'Launceston Campus',
                'capacity'    => 50,
                'price'       => 10.00,
                'category'    => 'Hackathon',
                'image_path'  => 'images/event3.png'
            ],
            [
                'title'       => 'Tech Networking Night',
                'description' => 'Meet local developers, recruiters, and founders. Light refreshments provided.',
                'days_ahead'  => 30,
                'location'    => 'Melbourne Campus',
                'capacity'    => 80,
                'price'       => 0,
                'category'    => 'Networking',
                'image_path'  => 'images/event4.png'
            ],
        ];
 
        $events = collect();
 
        foreach ($eventsData as $data) {
            $events->push(Event::create([
                'organiser_id'   => $organiser->id,
                'category_id'    => $categories->firstWhere('name', $data['category'])->id,
                'title'          => $data['title'],
                'description'    => $data['description'],
                'start_datetime' => now()->addDays($data['days_ahead'])->setTime(18, 0),
                'end_datetime'   => now()->addDays($data['days_ahead'])->setTime(21, 0),
                'location'       => $data['location'],
                'capacity'       => $data['capacity'],
                'price'          => $data['price'],
                'status'         => 'published',
                'image_path'     => $data['image_path'],
            ]));
        }
 




        
        /* ---------- One sample booking ---------- */
                Booking::create([
            'event_id'       => $events->first()->id,
            'attendee_id'    => $attendee->id,
            'quantity'       => 1,
            'status'         => 'confirmed',
            'payment_status' => 'free',
        ]);


    }
}
