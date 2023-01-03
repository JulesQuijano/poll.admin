<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Position;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Position::create([
            'name' => 'President',
        ]);

        Position::create([
            'name' => 'Vice President',
        ]);

        Position::create([
            'name' => 'Secretary',
        ]);

        Position::create([
            'name' => 'Treasurer',
        ]);
    }
}
