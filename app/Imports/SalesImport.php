<?php

namespace App\Imports;

use App\CustomerPartnerStore;
use App\Models\Master\CustomerPartner;
use App\Models\Master\SapCompliance;
use App\Models\Master\SapMaterial;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
// use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithConditionalSheets;


class SalesImport implements ToCollection, WithCalculatedFormulas
{
    // use WithConditionalSheets;

    protected $data = [];
    protected $index_header = 0;
    protected $index_header_table = 0;
    protected $index_header_ns = 0;
    protected $index_end = -1;
    protected $index_header_price = 0;


    // public function conditionalSheets(): array
    // {
    //     return [
    //         'CHI TIET NS DAT HANG_2' => $this,
    //     ];
    // }
    // public function sheets(): array
    // {
    //     return [
    //         'CHI TIET NS DAT HANG_2' => $this,

    //     ];
    // }
    public function collection(Collection $collection)
    {

        // dd($collection->toArray());

        $is_text_customer = [
            [
                'CTY',
                'FABICO',
                'TTFA',
                'VPTT',
            ],
        ];
        $is_text_table = [
            [
                // 'STT',
                // 'Barcode (Mã BH)',
                'Tên Sản Phẩm',

            ],
            [
                null,
            ]
        ];
        $is_text_ns = [
            [
                // 'Nhà sách',
                'NS FAHASA',
                'Nhà sách Fahasa',
                'Nhà sách',
                // 'NS',
            ]
        ];
        $is_text_price = [
            [
                'Giá bán',
            ]
        ];
        $is_text_end = [
            [
                'Tổng SL / Nhà sách',
                'Tổng cộng:',
            ]
        ];
        $is_text_header_end = [
            [
                'TỔNG SL / SẢN PHẨM',
                'Tổng SL Sản phẩm',
            ]
        ];

        $index_customer_parent = 0;
        $index_customer_child = 0;

        $index_material_code = 0;
        $index_key = -1;
        // dd($collection->toArray());

        for ($i = 0; $i < count($collection->toArray()); $i++) {
            $rows = $collection[$i];
            foreach ($rows as $index => $value) {
                foreach ($is_text_customer[0] as $text) {
                    if (strpos($value, $text) !== false) {
                        $index_customer_parent = $i;
                        $index_customer_child = $index;
                        break;
                    }
                }
            }
            if ($index_customer_parent != 0 && $index_customer_child != 0) {
                break;
            }
        }
        $this->processingIndexHeaderTable($collection->toArray(), $is_text_table);
        $this->processingIndexHeaderNs($collection->toArray(), $is_text_ns);

        if ($this->index_header_ns == 0) {
            $this->processingIndexHeaderPrice($collection->toArray(), $is_text_price);
            $this->index_header_ns = $this->index_header_price + 1;
        }
        $customer_name = $collection[$index_customer_parent][$index_customer_child];

        $header = $collection[$this->index_header_table]->toArray();
        $header = array_map(function ($item) {
            return mb_convert_case($item, MB_CASE_LOWER, "UTF-8");
        }, $header);

        $header = array_filter($header, function ($item) use ($is_text_header_end) {
            // dd($item == 'tổng sl / sản phẩm');
            $normalizedItem = str_replace(["_x000d_", "\n"], "", $item); // Loại bỏ ký tự không mong muốn
            // return $item != 'tổng sl / sản phẩm';
            foreach ($is_text_header_end[0] as $text) {
                if (strpos(mb_strtolower($normalizedItem), mb_strtolower($text)) !== false) {
                    return false;
                }
            }
            return true;
            // return $normalizedItem !== 'tổng sl / sản phẩm';
        });
        $data_excel = $collection->skip($this->index_header_table + 1);
        // xóa cuối mảng data_excel
        // $data_excel->pop();
        $result = [];
        $data_demos = [];
        $stopText = 'Tổng SL / Nhà sách';
        // $stopIndex = array_search($stopText,$data_excel); // Tìm vị trí (O(n))
        foreach ($data_excel as $key => $row) {

            $query_sap_material = SapMaterial::query()->with(['unit']);
            $query_sap_complience = SapCompliance::query();

            $row = $row->map(function ($item) {
                return trim($item);
            });
            $result[$key]['type'] = ''; // Di chuyển ra ngoài vòng lặp

            $result[$key]['note'] = ''; // Di nchuyển ra ngoài vòng lặp
            $result[$key]['count_votes'] = ''; // Di nchuyển ra ngoài vòng lặp

            $added_to_ns = false; // Cờ kiểm soát cho "details"
            $children = [];

            foreach ($header as $row_number => $column_name) {
                $header[$row_number] = preg_replace('/\s+/', ' ', $header[$row_number]);
                $header[$row_number] = preg_replace('/\n/', '', $header[$row_number]);
                $header[$row_number] = preg_replace('/\s$/', '', $header[$row_number]);
                $header[$row_number] = preg_replace('/^\s/', '', $header[$row_number]);
                $header[$row_number] = preg_replace('/\n\n/', '', $header[$row_number]);
                $header[$row_number] = str_replace('_x000d_', '', $header[$row_number]);
                // cho text $header[$row_number] thành chữ in hoa
                foreach ($is_text_end[0] as $text) {
                    if (strpos(mb_strtolower(trim($row[$row_number])), mb_strtolower($text)) !== false) {
                        $this->index_end = $key;
                        break;
                    }
                }
                if ($this->index_end != -1) {
                    break;
                }
                if ($row_number >= $this->index_header_ns && isset($row[$row_number]) && $row[$row_number] && !$added_to_ns) {
                    $header[$row_number] = mb_convert_case($header[$row_number], MB_CASE_UPPER, "UTF-8");
                    $query_customer_partner = CustomerPartnerStore::query();
                    $customer_partner = $query_customer_partner->whereRaw('LOWER(TRIM(name)) = ?', $header[$row_number])->first();
                    $result[$key]['customer_key'] = $customer_partner ? $customer_partner->code : '';
                    $result[$key]['customer_name'] = $header[$row_number]; // Đặt giá trị "customer_name"
                    $result[$key]['quantity'] = $row[$row_number]; // Đặt giá trị "quantity"
                    $added_to_ns = true; // Đánh dấu là đã thêm vào "added_to_ns"
                }
                if ($row_number >= $this->index_header_ns && isset($row[$row_number]) && $row[$row_number] && $added_to_ns) {
                    $header[$row_number] = mb_convert_case($header[$row_number], MB_CASE_UPPER, "UTF-8");
                    // $customer_partner = $query_customer_partner->where('name', $header[$row_number])->first();
                    $query_customer_partner = CustomerPartnerStore::query();

                    $customer_partner = $query_customer_partner->whereRaw('LOWER(TRIM(name)) = ?', $header[$row_number])->first();
                    $children[$key]['children'][] = [
                        'row_number' => $row_number,
                        'quantity' => $row[$row_number],
                        'customer_name' => $header[$row_number],
                        'customer_key' => $customer_partner ? $customer_partner->code : '',
                        'is_equal' => $header[$row_number] == $result[$key]['customer_name']
                    ];
                }
                if ($row_number < $this->index_header_ns) {

                    switch ($column_name) {
                        case 'barcode':
                            $result[$key]['barcode'] = $row[$row_number];
                            $sap_material = $query_sap_material->whereNotNull('bar_code')->where('bar_code', 'like', "%$row[$row_number]%")->first();
                            $sap_code = $sap_material ? $sap_material->sap_code : null;
                            $result[$key]['barcode_cty'] = $sap_material ? $sap_material->bar_code : null;
                            $result[$key]['sap_code'] = $sap_code;
                            $result[$key]['sap_name'] = $sap_material ? $sap_material->name : null;
                            $result[$key]['unit'] = $sap_material ? $sap_material['unit']['unit_code'] : null;
                            $sap_complience = $query_sap_complience->where('sap_code', $sap_code)->first();
                            $result[$key]['specifications'] = $sap_complience ? $sap_complience->compliance : null;

                            break;
                        case 'barcode (mã bh)':
                            $result[$key]['barcode'] = $row[$row_number];
                            $sap_material = $query_sap_material->whereNotNull('bar_code')->where('bar_code', 'like', "%$row[$row_number]%")->first();
                            $sap_code = $sap_material ? $sap_material->sap_code : null;
                            $result[$key]['barcode_cty'] = $sap_material ? $sap_material->bar_code : null;
                            $result[$key]['sap_code'] = $sap_code;
                            $result[$key]['sap_name'] = $sap_material ? $sap_material->name : null;
                            $result[$key]['unit'] = $sap_material ? $sap_material['unit']['unit_code'] : null;
                            $sap_complience = $query_sap_complience->where('sap_code', $sap_code)->first();
                            $result[$key]['specifications'] = $sap_complience ? $sap_complience->compliance : null;

                            break;
                            // case null:
                            //     if ($row_number == 2) {
                            //         $result[$key]['sap_code'] = $row[$row_number];
                            //         $result[$key]['sap_code'] = $row[$row_number];
                            //     }
                            //     break;
                        case 'tên sản phẩm':

                            $result[$key]['product_name'] = $row[$row_number];
                            // $result[$key]['sap_name'] = $row[$row_number];
                            break;
                        case 'giá bán':
                            $result[$key]['price'] = $row[$row_number];
                            break;
                        default:
                            break;
                    }
                }
            }
            if (count($children) > 0) {
                $values = [];
                foreach ($children as $key => $value) {
                    // dd($value);
                    foreach ($value['children'] as $key_value => $value_child) {
                        // $customer_partner = $query_customer_partner->where('name', $value_child['customer_name'])->first();
                        // dd($customer_partner);
                        $values[] = [
                            'barcode' => $result[$key]['barcode'],
                            'barcode_cty' => $result[$key]['barcode_cty'],
                            'product_name' => $result[$key]['product_name'],
                            'sap_code' => $result[$key]['sap_code'],
                            'sap_name' => $result[$key]['sap_name'],
                            'specifications' => $result[$key]['specifications'],
                            'unit' => $result[$key]['unit'],
                            'price' => $result[$key]['price'] ?? '',
                            'quantity'   => $value_child['quantity'],
                            'customer_name'     => $value_child['customer_name'],
                            'is_specifications' => $result[$key]['specifications'] > $value_child['quantity'] ? 1 : 0,
                            'customer_key' => $value_child['customer_key'],
                            'count_votes' => $result[$key]['count_votes'],
                            'note' => $result[$key]['note'],
                            // 'customer_key' => $customer_partner ? ($customer_partner->customer_partner_stores ? $customer_partner->customer_partner_stores->code : '') : '',

                        ];
                    }
                }
                $result = array_merge($result, $values); // Gán mảng mới cho $result
                $data_demos[] = $values;
            }
            if ($key == $this->index_end) {
                break;
            }
        }
        $dataArray = [];
        foreach ($data_demos as $key => $data_demo) {
            foreach ($data_demo as $key_data_demo => $value_data_demo) {
                $dataArray[] = $value_data_demo;
            }
        }
        $customer_key = array_column($dataArray, 'customer_name');
        array_multisort($customer_key, SORT_ASC, $dataArray);
        $this->data['items'] = $dataArray;
        $this->data['header']['customer_name'] = $customer_name;
        return $dataArray;
    }

    public function getData()
    {
        return $this->data;
    }
    public function processingIndexHeaderTable($array = [], $is_text_table)
    {
        for ($i = 1; $i < count($array); $i++) {
            $rows = $array[$i];
            foreach ($rows as $index => $value) {
                foreach ($is_text_table[0] as $text) {
                    if (strpos($value, $text) !== false) {
                        $this->index_header_table = $i;
                        break;
                    }
                }
                if ($this->index_header_table != 0) {
                    break;
                }
            }
            if ($this->index_header_table != 0) {
                break;
            }
        }
        return $this->index_header_table;
    }

    public function processingIndexHeaderNs($array = [], $is_text_ns)
    {
        for ($i = 0; $i < count($array); $i++) {
            $rows = $array[$i];
            foreach ($rows as $index => $value) {
                foreach ($is_text_ns[0] as $text) {
                    if (strpos($value, $text) !== false) {
                        // dd($value,$text,$i,$index, mb_strtolower(trim($value)) == mb_strtolower('Tổng SL / Nhà sách'));
                        if (mb_strtolower(trim($value)) !== mb_strtolower('Tổng SL / Nhà sách')) {
                            $this->index_header_ns = $index;
                            break;
                        }
                    }
                }
                if ($this->index_header_ns != 0) {
                    break;
                }
            }
            if ($this->index_header_ns != 0) {
                break;
            }
        }
        return $this->index_header_ns;
    }
    public function processingIndexHeaderPrice($array = [], $is_text_price)
    {
        for ($i = 0; $i < count($array); $i++) {
            $rows = $array[$i];
            foreach ($rows as $index => $value) {
                // dd($rows,$i,$index);
                foreach ($is_text_price[0] as $text) {
                    if (strpos(mb_strtolower($value), mb_strtolower($text)) !== false) {
                        // dd($value,$text,$i,$index);
                        $this->index_header_price = $index;
                        break;
                    }
                }
                if ($this->index_header_price != 0) {
                    break;
                }
            }
            if ($this->index_header_price != 0) {
                break;
            }
        }
        return $this->index_header_price;
    }
}
