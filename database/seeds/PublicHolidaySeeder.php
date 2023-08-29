<?php

use App\Models\Business\PublicHoliday;
use Illuminate\Database\Seeder;

class PublicHolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $public_holidays = [
            ["name" => "Tết dương lịch", "start_holiday_date" => "2023-01-01", "end_holiday_date" => "2023-01-02"],
            ["name" => "Tết nguyên đán", "start_holiday_date" => "2023-01-21", "end_holiday_date" => "2023-01-27"],
            ["name" => "Giỗ tổ Hùng Vương", "start_holiday_date" => "2023-04-29", "end_holiday_date" => "2023-04-29"],
            ["name" => "Ngày Thống nhất đất nước", "start_holiday_date" => "2023-04-30", "end_holiday_date" => "2023-04-30"],
            ["name" => "Ngày Quốc tế Lao động", "start_holiday_date" => "2023-05-01", "end_holiday_date" => "2023-05-03"],
            ["name" => "Ngày Quốc khánh", "start_holiday_date" => "2023-09-01", "end_holiday_date" => "2023-09-02"]
        ];
        foreach ($public_holidays as $public_holidays) {
            if (!PublicHoliday::where('start_delivery_date', $public_holidays['start_delivery_date'])->where('end_holiday_date', $public_holidays['end_holiday_date'])->exists())
                PublicHoliday::create($public_holidays);
        }

        $this->command->info('PublicHolidaySeeder has been completed!');
    }
}
