<?php

use App\Models\Master\OrderStatus;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create some companies
        $statuses = [
            [
                'id' => '10',
                'name' => 'Đang xử lí đơn hàng',
            ],
            [
                'id' => '20',
                'name' => 'Đã duyệt & đang soạn hàng',
            ],
            [
                'id' => '30',
                'name' => 'Đang vận chuyển',
            ],
            [
                'id' => '100',
                'name' => 'Đã giao',
            ],
        ];

        foreach ($statuses as $status) {
            if (!OrderStatus::where('id', $status['id'])->exists())
                OrderStatus::create($status);
        }
    }
}
