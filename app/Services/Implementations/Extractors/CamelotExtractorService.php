<?php

namespace App\Services\Implementations\Extractors;

use App\Services\Interfaces\DataExtractorInterface;
use RandomState\Camelot\Areas;
use RandomState\Camelot\Camelot;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Log;
use App\Utilities\OperatorUtility;

class CamelotExtractorService implements DataExtractorInterface
{
    public function extract($file_path, $options)
    {
        $table = [];
        $is_convert_tcvn3 = false;
        if ($options['is_specify_advanced_settings']) {
            $advanced_settings_info = $options['advanced_settings_info'];
            if (isset($advanced_settings_info->split_page)) {
                $table = $this->extractSplitPages($file_path, $options);
            } else {
                $table = $this->extractNoSplitPages($file_path, $options);
            }
            if (isset($advanced_settings_info->convert_tcvn3)) {
                $is_convert_tcvn3 = $advanced_settings_info->convert_tcvn3;
            }
        } else {
            $table = $this->extractNoSplitPages($file_path, $options);
        }
        if ($is_convert_tcvn3) {
            foreach ($table as $key=>$item) {
                $table[$key] = OperatorUtility::convertTCVN3ToUnicode($item);
            }
        }
        return $table;
    }

    private function extractNoSplitPages($file_path, $options)
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
                // $instance->inRegions($areas);

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
                $instance->shiftText(...$shift_text);
            }
            if (isset($advanced_settings_info->strip_text)) {
                $strip_text = $advanced_settings_info->strip_text;
                $instance->strip($strip_text);
            }
            if (isset($advanced_settings_info->columns)) {
                $x_coordinates = $advanced_settings_info->columns->x_coordinates;
                $instance->setColumnSeparators($x_coordinates, true);
                // $instance->setColumnSeparators(['44,168,274,333,398,475'], true);
                // $instance->setColumnSeparators(['72,95,209,327,442,529,566,606,683'], false);
            }
            if (isset($advanced_settings_info->flag_size)) {
                $instance->flagSize($advanced_settings_info->flag_size);
            }
            if (isset($advanced_settings_info->copy_text)) {
                $instance->copyTextSpanningCells(...$advanced_settings_info->copy_text);
            }
            if (isset($advanced_settings_info->edge_tol)) {
                $instance->setEdgeTolerance($advanced_settings_info->edge_tol);
            }
            if (isset($advanced_settings_info->row_tol)) {
                $instance->setRowTolerance($advanced_settings_info->row_tol);
            }
            if (isset($advanced_settings_info->process_background)) {
                if ($advanced_settings_info->process_background) {
                    $instance->processBackgroundLines();
                }
            }
        }
        $table = $instance->extract();
        return $table;
    }

    private function extractSplitPages($file_path, $options)
    {
        // Xử lý page đầu
        $page1_table = $this->extractWithPages($file_path, $options, '1');
        // Xử lý từ page 2 trở đi
        $page2_to_end_table = $this->extractWithPages($file_path, $options, '2-end');

        // Merge $page1_table và $page2_to_end_table
        $table = array_merge($page1_table, $page2_to_end_table);
        return $table;
    }

    private function extractWithPages($file_path, $options, $pages)
    {
        $file_path = "\"" . $file_path . "\"";
        $instance = new Camelot($file_path, $options['flavor']);

        $instance->pages($pages);
        // Xử lý cài đặt nâng cao
        if ($options['is_specify_advanced_settings']) {
            $advanced_settings_info = $options['advanced_settings_info'];
            if (isset($advanced_settings_info->line_scale)) {
                $instance->setLineScale($advanced_settings_info->line_scale);
            }
            if (isset($advanced_settings_info->shift_text)) {
                $shift_text = $advanced_settings_info->shift_text;
                $instance->shiftText(...$shift_text);
            }
            if (isset($advanced_settings_info->strip_text)) {
                $strip_text = $advanced_settings_info->strip_text;
                $instance->strip($strip_text);
            }
            if (isset($advanced_settings_info->columns)) {
                $x_coordinates = $advanced_settings_info->columns->x_coordinates;
                $instance->setColumnSeparators($x_coordinates, true);
                // $instance->setColumnSeparators(['44,168,274,333,398,475'], true);
                // $instance->setColumnSeparators(['72,95,209,327,442,529,566,606,683'], false);
            }
            if (isset($advanced_settings_info->flag_size)) {
                $instance->flagSize($advanced_settings_info->flag_size);
            }
            if (isset($advanced_settings_info->copy_text)) {
                $instance->copyTextSpanningCells(...$advanced_settings_info->copy_text);
            }
            if (isset($advanced_settings_info->edge_tol)) {
                $instance->setEdgeTolerance($advanced_settings_info->edge_tol);
            }
            if (isset($advanced_settings_info->row_tol)) {
                $instance->setRowTolerance($advanced_settings_info->row_tol);
            }
            if (isset($advanced_settings_info->process_background)) {
                if ($advanced_settings_info->process_background) {
                    $instance->processBackgroundLines();
                }
            }

            if ($pages == '1') {
                // Xử lý page 1
                if ($advanced_settings_info->split_page->page1) {
                    $page1 = $advanced_settings_info->split_page->page1;
                    // Xử lý table areas
                    $table_area_info = isset($page1->table_areas) ? $page1->table_areas : [];
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

                    // Xử lý region areas
                    $region_area_info = isset($page1->region_areas) ? $page1->region_areas : [];
                    $areas_count = count($region_area_info);
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
                        $instance->inRegions($areas);
                    }
                }
            } elseif ($pages == '2-end') {
                // Xử lý vùng bảng page 2 trở đi
                if ($advanced_settings_info->split_page->page2_to_end) {
                    $page2_to_end = $advanced_settings_info->split_page->page2_to_end;
                    // Xử lý table areas
                    $table_area_info = isset($page2_to_end->table_areas) ? $page2_to_end->table_areas : [];
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

                    // Xử lý region areas
                    $region_area_info = isset($page2_to_end->region_areas) ? $page2_to_end->region_areas : [];
                    $areas_count = count($region_area_info);
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
                        $instance->inRegions($areas);
                    }
                }
            }
        }
        $table = $instance->extract();
        return $table;
    }

    public function getValueTableAreas($file_path, $table_area_info, $x_coordinates) {
        $file_path = "\"" . $file_path . "\"";
        $instance = new Camelot($file_path, 'stream');

        // Xử lý vùng bảng
        if ($table_area_info) {
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
        if ($x_coordinates) {
            $instance->setColumnSeparators($x_coordinates, true);
        }
        $table = $instance->extract();
        return $table;
    }
}
