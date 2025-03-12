<?php

namespace Database\Seeders;

use App\Models\ProneArea;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProneAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProneArea::factory()->count(3)->create();
    }
}
