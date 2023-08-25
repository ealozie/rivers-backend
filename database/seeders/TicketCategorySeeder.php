<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $table->string('category_name') //Loading, Offloading, Parking, Entrance, Overloading, Wrong Parking exit;
        // $table->double('amount', 8, 2);
        // $table->string('category_status');
        // $table->foreignId('added_by');
        // $table->boolean('allow_multiple_ticket_purchase');
        // $table->integer('duration');
        $ticket_categories_for_vehicles = [
            [
                'category_name' => 'Loading',
                'amount' => 1000,
                'category_status' => 'active',
                'added_by' => 1,
                'allow_multiple_ticket_purchase' => true,
                'duration' => 1,
            ],
            [
                'category_name' => 'Offloading',
                'amount' => 1200,
                'category_status' => 'active',
                'added_by' => 1,
                'allow_multiple_ticket_purchase' => true,
                'duration' => 1,
            ],
            [
                'category_name' => 'Parking',
                'amount' => 200,
                'category_status' => 'active',
                'added_by' => 1,
                'allow_multiple_ticket_purchase' => true,
                'duration' => 1,
            ],
            [
                'category_name' => 'Entrance',
                'amount' => 300,
                'category_status' => 'active',
                'added_by' => 1,
                'allow_multiple_ticket_purchase' => true,
                'duration' => 1,
            ],
            [
                'category_name' => 'Overloading',
                'amount' => 400,
                'category_status' => 'active',
                'added_by' => 1,
                'allow_multiple_ticket_purchase' => true,
                'duration' => 1,
            ],
            [
                'category_name' => 'Wrong Parking',
                'amount' => 500,
                'category_status' => 'active',
                'added_by' => 1,
                'allow_multiple_ticket_purchase' => true,
                'duration' => 1,
            ],
            [
                'category_name' => 'Exit',
                'amount' => 200,
                'category_status' => 'active',
                'added_by' => 1,
                'allow_multiple_ticket_purchase' => true,
                'duration' => 1,
            ],
        ];
        \App\Models\TicketCategory::truncate();
        foreach ($ticket_categories_for_vehicles as $ticket_category_for_vehicle) {
            \App\Models\TicketCategory::create($ticket_category_for_vehicle);
        }
    }
}
