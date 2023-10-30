<?php

use App\Models\Business\FileStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FileStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file_statuses = [
            [
                'code' => "10",
                'name' => "Mới",
                'badge_class' => "badge badge-primary"
            ],
            [
                'code' => "20",
                'name' => "Đang xử lý",
                'badge_class' => "badge badge-warning"
            ],
            [
                'code' => "30",
                'name' => "Hoàn thành",
                'badge_class' => "badge badge-success"
            ],
            [
                'code' => "40",
                'name' => "Lỗi",
                'badge_class' => "badge badge-danger"
            ],
        ];


        Schema::disableForeignKeyConstraints();
        FileStatus::query()->truncate();
        foreach ($file_statuses as $file_status) {
            DB::beginTransaction();
            if (!FileStatus::where('code', $file_status['code'])->where('name', $file_status['name'])->exists()) {
                FileStatus::create($file_status);
            }
            DB::commit();
        }
        Schema::enableForeignKeyConstraints();
        $this->command->info('FileStatusesSeeder has been completed!');
    }
}
