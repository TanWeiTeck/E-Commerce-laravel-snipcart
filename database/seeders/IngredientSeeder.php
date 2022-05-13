<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ingredients')->insert([
            [
                'name' => 'Quick Oats',
                'description' => '100% whole grain oats (quick cooking)',
                'category' => 'oats',
                'price_per_gram' => 0.007,
            ],
        ]);
        // \App\Models\User::factory(5)->create();
    }
}
