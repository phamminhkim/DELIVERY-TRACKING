<?php

use App\Models\Business\RegexPattern;
use Illuminate\Database\Seeder;

class RegexPatternSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regex_patterns = [
            [
                'name' => 'digits',
                'pattern' => '\d'
            ],
            [
                'name' => 'words',
                'pattern' => '\w'
            ],
            [
                'name' => 'prices',
                'pattern' => '[\d,\.]'
            ]
        ];
        foreach ($regex_patterns as $regex_pattern) {
            if (!RegexPattern::where('name', $regex_pattern['name'])->exists())
                RegexPattern::create($regex_pattern);
        }

        $this->command->info('RegexPattern has been completed!');
    }
}
