<?php

use App\Models\Business\FileStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                'name' => "NEW"
            ],
            [
                'code' => "20",
                'name' => "PROCESSING"
            ],
            [
                'code' => "30",
                'name' => "SUCCESS"
            ],
            [
                'code' => "40",
                'name' => "ERROR"
            ],
        ];

        // FileStatus::query()->truncate();
        
        foreach ($file_statuses as $file_status) {
            DB::beginTransaction();
            if (!FileStatus::where('code', $file_status['code'])->where('name', $file_status['name'])->exists()) {
                FileStatus::create($file_status);
            }
            DB::commit();
        }
    
        $this->command->info('FileStatusesSeeder has been completed!');
    }
}
