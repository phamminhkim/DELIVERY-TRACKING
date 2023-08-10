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
        $statuses = [
            [
                'id' => '10',
                'name' => 'Đang xử lí đơn hàng',
                'badge_class' => 'badge badge-secondary'
            ],
            [
                'id' => '20',
                'name' => 'Đã duyệt & đang soạn hàng',
                'badge_class' => 'badge badge-dark'
            ],
            [
                'id' => '30',
                'name' => 'Đang vận chuyển',
                'badge_class' => 'badge badge-primary'
            ],
            [
                'id' => '40',
                'name' => 'Đã giao một phần',
                'badge_class' => 'badge badge-warning'
            ],
            [
                'id' => '100',
                'name' => 'Đã giao xong',
                'badge_class' => 'badge badge-info'
            ],
            [
                'id' => '200',
                'name' => 'Đã nhận hàng',
                'badge_class' => 'badge badge-success'
            ],
        ];

        foreach ($statuses as $status) {
            $exist_status = OrderStatus::where('id', $status['id'])->first();
            if (!$exist_status)
                OrderStatus::create($status);
            else {
                $exist_status->name = $status['name'];
                $exist_status->badge_class = $status['badge_class'];
                $exist_status->save();
            }
        }

        $this->command->info('OrderStatusSeeder has been completed!');
    }
}
