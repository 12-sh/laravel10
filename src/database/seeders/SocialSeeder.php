<?php

namespace Database\Seeders;

use App\Models\Social;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SocialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void

    {
        $sociale = [
            'name' => 'line',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];


        if (!Social::where('name', $sociale['name'])->exists()) {
            Social::create($sociale);
        }
    }
}
