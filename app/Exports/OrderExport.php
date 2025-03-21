<?php

namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;


class OrderExport implements FromArray
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        // Chuyển dữ liệu thành định dạng bảng Excel
        $header = ['Tên_ns', 'Loại phiếu', 'Tên nhà sách', 'Mã vạch', 'Tên sản phẩm', 'Giá bán', 'Số lượng', 'Quy cách', 'Barcode_cty', 'Mã SAP', 'Tên SP', 'Đơn vị tính'];
        $items = array_map(function ($item) {
            return [
                $item['customer_key'],
                $item['type']?? null,
                $item['customer_name'],
                $item['barcode'],
                $item['product_name'],
                $item['product_name'],
                $item['quantity'],
                $item['specifications'],
                $item['barcode_cty'],
                $item['sap_code'],
                $item['sap_name'],
                $item['unit'],
            ];
        }, $this->data['items']);

        return array_merge([$header], $items);
    }
}
