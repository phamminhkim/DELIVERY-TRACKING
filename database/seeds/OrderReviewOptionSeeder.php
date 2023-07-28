<?php

use App\Models\Master\OrderReviewOption;
use Illuminate\Database\Seeder;

class OrderReviewOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reviews = [
            [
                'id' => '0',
                'name' => 'Chưa nhận được hàng',
            ],
            [
                'id' => '10',
                'name' => 'Thiếu thùng',
            ],
            [
                'id' => '11',
                'name' => 'Thiếu hàng bên trong thùng',
            ],
            [
                'id' => '20',
                'name' => 'Thùng móp, ướt, hư hỏng',
            ],
            [
                'id' => '21',
                'name' => 'Hàng bên trong bễ vỡ',
            ],
            [
                'id' => '30',
                'name' => 'Giao trễ',
            ],
            [
                'id' => '100',
                'name' => 'Nhận đủ',
            ],
        ];

        foreach ($reviews as $review) {
            if (!OrderReviewOption::where('id', $review['id'])->exists())
                OrderReviewOption::create($review);
        }
    }
}
