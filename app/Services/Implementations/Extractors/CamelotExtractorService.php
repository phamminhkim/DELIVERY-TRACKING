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
        // Xử lý cài đặt nâng cao
        if ($options['is_specify_advanced_settings']) {
            $advanced_settings_info = $options['advanced_settings_info'];
            if (isset($advanced_settings_info->line_scale)) {
                $instance->setLineScale($advanced_settings_info->line_scale);
            }
            if (isset($advanced_settings_info->shift_text)) {
                $shift_text = $advanced_settings_info->shift_text;
                $instance->shiftText($shift_text);
            }
            if (isset($advanced_settings_info->strip_text)) {
                $strip_text = $advanced_settings_info->strip_text;
                $instance->strip($strip_text);
            }
            if (isset($advanced_settings_info->split_text)) {
                $split_text = (array) $advanced_settings_info->split_text;
                $instance->setColumnSeparators($split_text);
            }
            if (isset($advanced_settings_info->flag_size)) {
                $instance->flagSize($advanced_settings_info->flag_size);
            }
            if (isset($advanced_settings_info->copy_text)) {
                $instance->copyTextSpanningCells($advanced_settings_info->copy_text);
            }
            if (isset($advanced_settings_info->edge_tol)) {
                $instance->setEdgeTolerance($advanced_settings_info->edge_tol);
            }
            if (isset($advanced_settings_info->row_tol)) {
                $instance->setRowTolerance($advanced_settings_info->row_tol);
            }
        }
        $table = $instance->extract();
        return $table;
    }
}
