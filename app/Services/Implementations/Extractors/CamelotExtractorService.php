<?php

namespace App\Services\Implementations\Extractors;

use App\Services\Interfaces\DataExtractorInterface;
use RandomState\Camelot\Areas;
use RandomState\Camelot\Camelot;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Log;

class CamelotExtractorService implements DataExtractorInterface
{
    public function extract($file_path, $options)
    {
        $file_path = "\"" . $file_path . "\"";
        $instance = new Camelot($file_path, $options['flavor']);

        // Nếu để mặc định thì Camelot chỉ lấy 1 page đầu
        if ($options['is_merge_pages']) {
            $instance->pages('1-end');
        }
        // Xử lý vùng bảng
        if ($options['is_specify_table_area']) {
            $table_area_info = $options['table_area_info'];
            $areas_count = count($table_area_info);
            if ($areas_count > 0) {
                // Vùng đầu tiên
                $x_top_left = $table_area_info[0]->x_top_left ? $table_area_info[0]->x_top_left : 0;
                $y_top_left = $table_area_info[0]->y_top_left ? $table_area_info[0]->y_top_left : 0;
                $x_bottom_right = $table_area_info[0]->x_bottom_right ? $table_area_info[0]->x_bottom_right : 0;
                $y_bottom_right = $table_area_info[0]->y_bottom_right ? $table_area_info[0]->y_bottom_right : 0;

                $areas = Areas::from($x_top_left, $y_top_left, $x_bottom_right, $y_bottom_right);
                // Add thêm vùng nếu có
                for ($area_index = 1; $area_index < $areas_count; $area_index++) {
                    $x_top_left = $table_area_info[$area_index]->x_top_left ? $table_area_info[$area_index]->x_top_left : 0;
                    $y_top_left = $table_area_info[$area_index]->y_top_left ? $table_area_info[$area_index]->y_top_left : 0;
                    $x_bottom_right = $table_area_info[$area_index]->x_bottom_right ? $table_area_info[$area_index]->x_bottom_right : 0;
                    $y_bottom_right = $table_area_info[$area_index]->y_bottom_right ? $table_area_info[$area_index]->y_bottom_right : 0;
                    $areas->add($x_top_left, $y_top_left, $x_bottom_right, $y_bottom_right);
                }
                $instance->inAreas($areas);
            }
        }

        $table = $instance->extract();
        return $table;
    }
}
