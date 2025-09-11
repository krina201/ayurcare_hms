<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterRoom;

class MasterRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            [
                'room_number' => '101',
                'room_type' => 'Abhyanga Suite',
                'capacity' => 1,
                'charges' => 1500.00,
                'status' => 1,
            ],
            [
                'room_number' => '102',
                'room_type' => 'Shirodhara Room',
                'capacity' => 1,
                'charges' => 1200.00,
                'status' => 1,
            ],
            [
                'room_number' => '103',
                'room_type' => 'General Treatment',
                'capacity' => 1,
                'charges' => 800.00,
                'status' => 1,
            ],
            [
                'room_number' => '104',
                'room_type' => 'Pinda Sweda Room',
                'capacity' => 1,
                'charges' => 1000.00,
                'status' => 1,
            ],
            [
                'room_number' => '105',
                'room_type' => 'Basti Suite',
                'capacity' => 1,
                'charges' => 2000.00,
                'status' => 1,
            ],
            [
                'room_number' => '106',
                'room_type' => 'Nasya Room',
                'capacity' => 1,
                'charges' => 600.00,
                'status' => 1,
            ],
            [
                'room_number' => '107',
                'room_type' => 'Udvartana Room',
                'capacity' => 1,
                'charges' => 900.00,
                'status' => 1,
            ],
            [
                'room_number' => '108',
                'room_type' => 'Multi-purpose',
                'capacity' => 2,
                'charges' => 1100.00,
                'status' => 2, // Under maintenance
            ],
            [
                'room_number' => '109',
                'room_type' => 'Vamana Room',
                'capacity' => 1,
                'charges' => 1800.00,
                'status' => 1,
            ],
            [
                'room_number' => '110',
                'room_type' => 'Virechana Room',
                'capacity' => 1,
                'charges' => 1600.00,
                'status' => 1,
            ],
        ];

        foreach ($rooms as $room) {
            MasterRoom::create($room);
        }
    }
}
