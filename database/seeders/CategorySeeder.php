<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Important', 'color' => '#FFE4E1'],      // Light pink/rose
            ['name' => 'Lecture notes', 'color' => '#E0FFFF'],  // Light cyan
            ['name' => 'To-do lists', 'color' => '#FFFACD'],    // Light yellow
            ['name' => 'Shopping', 'color' => '#E8FFE8'],       // Light green
            ['name' => 'Work', 'color' => '#E6E6FA'],           // Lavender
            ['name' => 'Personal', 'color' => '#FFF0F5'],       // Lavender blush
            ['name' => 'Ideas', 'color' => '#F0FFF0'],          // Honeydew
            ['name' => 'Meeting notes', 'color' => '#FFEFD5'],  // Papaya whip
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
