<?php

namespace App\Repositories\Master;

use App\Models\Master\UserFieldTable;
use App\User;
use App\Models\System\Role;
use App\Utilities\MenuUtility;
use App\Utilities\RedisUtility;
use App\Models\Shared\ConfigUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Auth;

class UserFieldTableRepository extends RepositoryAbs
{
    public function getUserFieldTable()
    {
        try {

            $fields = $this->request->all();
            $user = Auth::user();
            $fields_default = [
                [
                    'key' => 'selected',
                    'label' => '',
                    'class' => 'text-nowrap   ',
                    'tdClass' => 'checkbox-sticky-left text-center',
                    'thClass' => 'checkbox-sticky-left text-center',
                    'isShow' => true,
                    'sort_table' => 0,
                    'set_width' => 40,
                ],
                [
                    'key' => 'action',
                    'label' => '',
                    'class' => 'text-nowrap ',
                    'tdClass' => 'checkbox-sticky-center text-center',
                    'thClass' => 'checkbox-sticky-center text-center',
                    'isShow' => true,
                    'sort_table' => 1,
                    'set_width' => 40,

                ],
                [
                    'key' => 'index',
                    'label' => 'STT',
                    'class' => 'text-nowrap text-center  ',
                    'sort_table' => 2,
                    'tdClass' => 'checkbox-sticky-end text-center border',
                    'thClass' => 'checkbox-sticky-header-end text-center',
                    'isShow' => true,
                    'set_width' => 40,
                ],
                [
                    'key' => 'customer_name',
                    'label' => 'Makh Key',
                    'class' => 'text-nowrap  ',
                    'sort_table' => 3,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 100,

                ],
                [
                    'key' => 'sap_so_number',
                    'label' => 'Mã Sap So',
                    'class' => 'text-nowrap   ',
                    'sort_table' => 4,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 170,
                ],
                [
                    'key' => 'so_sap_note',
                    'label' => 'Sap note',
                    'class' => 'text-nowrap   ',
                    'sort_table' => 5,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 100,

                ],
                [
                    'key' => 'barcode',
                    'label' => 'Barcode_cty',
                    'class' => 'text-nowrap   ',
                    'sort_table' => 6,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 100,


                ],
                [
                    'key' => 'sku_sap_code',
                    'label' => 'Masap',
                    'class' => 'text-nowrap text-center  ',
                    'sort_table' => 7,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 100,


                ],
                [
                    'key' => 'sku_sap_name',
                    'label' => 'Tensp',
                    'class' => 'text-nowrap   ',
                    'sort_table' => 8,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 100,


                ],
                [
                    'key' => 'quantity3_sap',
                    'label' => 'SL_sap',
                    'class' => 'text-nowrap   ',
                    'sort_table' => 9,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 100,


                ],
                [
                    'key' => 'sku_sap_unit',
                    'label' => 'Dvt',
                    'class' => 'text-nowrap   ',
                    'sort_table' => 10,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 100,

                ],

                [
                    'key' => 'promotive',
                    'label' => 'Km',
                    'class' => 'text-nowrap   ',
                    'tdClass' => 'voucher-custom border p-0 ',
                    'sort_table' => 11,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 150,

                ],
                [
                    'key' => 'note',
                    'label' => 'Ghi_chu',
                    'class' => 'text-nowrap   ',
                    'sort_table' => 12,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 150,

                ],
                [
                    'key' => 'customer_code',
                    'label' => 'Makh',
                    'class' => 'text-nowrap   ',
                    'sort_table' => 13,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 150,

                ],
                [
                    'key' => 'customer_sku_code',
                    'label' => 'Unit_barcode',
                    'class' => 'text-nowrap ',
                    'sort_table' => 14,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 50,
                ],
                [
                    'key' => 'customer_sku_name',
                    'label' => 'Unit_barcode_description',
                    'class' => 'text-nowrap   ',
                    'sort_table' => 15,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 200,


                ],
                [
                    'key' => 'customer_sku_unit',
                    'label' => 'Dvt_po',
                    'class' => 'text-nowrap   ',
                    'sort_table' => 16,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 100,


                ],
                [
                    'key' => 'po',
                    'label' => 'Po',
                    'class' => 'text-nowrap   ',
                    'sort_table' => 17,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 100,


                ],
                [
                    'key' => 'quantity1_po',
                    'label' => 'Qty',
                    'class' => "text-nowrap  ",
                    'sort_table' => 18,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 50,


                ],
                [
                    'key' => 'promotive_name',
                    'label' => 'Combo',
                    'class' => 'text-nowrap   ',
                    'sort_table' => 19,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 100,



                ],
                [
                    'key' => 'inventory_quantity',
                    'label' => 'Check tồn',
                    'class' => "text-nowrap  ",
                    'sort_table' => 20,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 100,


                ],
                [
                    'key' => 'quantity2_po',
                    'label' => 'Po_qty',
                    'class' => "text-nowrap  ",
                    'sort_table' => 21,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 100,


                ],
                [
                    'key' => 'variant_quantity',
                    'label' => 'SL Chênh lệch',
                    'class' => "text-nowrap  ",
                    'sort_table' => 22,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 100,

                ],
                [
                    'key' => 'price_po',
                    'label' => 'Pur_price',
                    'class' => "text-nowrap  ",
                    'sort_table' => 23,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 100,


                ],

                [
                    'key' => 'amount_po',
                    'label' => 'Amount',
                    'class' => "text-nowrap  ",
                    'sort_table' => 24,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 100,


                ],
                [
                    'key' => 'compliance',
                    'label' => 'QC',
                    'class' => 'text-nowrap   ',
                    'sort_table' => 25,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 100,

                ],
                [
                    'key' => 'is_compliant',
                    'label' => 'Đúng_QC',
                    'sort_table' => 26,
                    'thClass' => 'border',
                    'class' => 'text-center   text-nowrap',
                    'isShow' => true,
                    'set_width' => 100,

                ],
                [
                    'key' => 'note1',
                    'label' => 'Ghi chú 1',
                    'class' => 'text-nowrap   ',
                    'sort_table' => 27,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 150,


                ],
                [
                    'key' => 'company_price',
                    'label' => 'Gia_cty',
                    'class' => 'text-nowrap   ',
                    'sort_table' => 28,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 100,


                ],
                [
                    'key' => 'level2',
                    'label' => 'Level_2',
                    'class' => 'text-nowrap   ',
                    'sort_table' => 29,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 100,


                ],
                [
                    'key' => 'level3',
                    'label' => 'Level_3',
                    'class' => 'text-nowrap   ',
                    'sort_table' => 30,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 100,


                ],
                [
                    'key' => 'level4',
                    'label' => 'Level_4',
                    'class' => 'text-nowrap   ',
                    'sort_table' => 31,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 100,


                ],
                [
                    'key' => 'po_number',
                    'label' => 'po_number',
                    'class' => 'text-nowrap   ',
                    'sort_table' => 32,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 150,


                ],
                [
                    'key' => 'po_delivery_date',
                    'label' => 'po_delivery_date',
                    'class' => 'text-nowrap   ',
                    'sort_table' => 33,
                    'thClass' => 'border',
                    'isShow' => true,
                    'set_width' => 100,

                ],
            ];
            if ($fields['user_id']) {
                $userFieldTable = UserFieldTable::where('user_id', $fields['user_id'])->where('version', '1.0.0')->first();
                if ($fields['tables'][0] == "[]") {
                    if (!$userFieldTable) {
                        $userFieldTable = UserFieldTable::create([
                            'user_id' => $user->id,
                            'field_table' => $fields_default
                        ]);
                    }
                    $field_tables = $userFieldTable->field_table;
                    if (!isset($field_tables[0]['set_width'])) {
                        $userFieldTable->field_table = $fields_default;
                        $userFieldTable->save();
                    }
                } else {
                    $json = json_decode($fields['tables'][0]);
                    $userFieldTable->field_table = $json;
                    $userFieldTable->save();
                    return $userFieldTable;
                }
                return $userFieldTable;
            }
            return false;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function getUserFieldTableVersion_2()
    {
        try {

            $fields = $this->request->all();
            $user = Auth::user();
            $fields_default = [
                [
                    'title' => 'ID',
                    'field' => 'id',
                    'headerSort' => false,
                    'visible' => false,
                    'width' => 100
                ],
                [
                    'title' => 'MaKh_Key',
                    'field' => 'customer_name',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 150
                ],
                [
                    'title' => 'Mã sap so',
                    'field' => 'sap_so_number',
                    'editor' => 'input',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 150
                ],
                [
                    'title' => 'Sap_note',
                    'field' => 'so_sap_note',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 150
                ],
                [
                    'title' => 'Barcode_Cty',
                    'field' => 'barcode',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 150
                ],
                [
                    'title' => 'MaSap',
                    'field' => 'sku_sap_code',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 150
                ],
                [
                    'title' => 'TenSp',
                    'field' => 'sku_sap_name',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 200
                ],
                [
                    'title' => 'SL_sap',
                    'field' => 'quantity3_sap',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 100
                ],
                [
                    'title' => 'Dvt',
                    'field' => 'sku_sap_unit',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 100
                ],
                [
                    'title' => 'Km',
                    'field' => 'promotive',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 100
                ],
                [
                    'title' => 'Ghi chú',
                    'field' => 'note',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 200
                ],
                [
                    'title' => 'MaKh',
                    'field' => 'customer_code',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 150
                ],
                [
                    'title' => 'Unit_barcode',
                    'field' => 'customer_sku_code',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 150
                ],
                [
                    'title' => 'Unit_barcode_description',
                    'field' => 'customer_sku_name',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 200
                ],
                [
                    'title' => 'Dvt_po',
                    'field' => 'customer_sku_unit',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 100
                ],
                [
                    'title' => 'Po',
                    'field' => 'po',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 150
                ],
                [
                    'title' => 'Qty',
                    'field' => 'quantity1_po',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 100
                ],
                [
                    'title' => 'Combo',
                    'field' => 'promotive_name',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 150
                ],
                [
                    'title' => 'Check Tồn',
                    'field' => 'inventory_quantity',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 150
                ],
                [
                    'title' => 'Po_qty',
                    'field' => 'quantity2_po',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 100
                ],
                [
                    'title' => 'SL Chênh Lệch',
                    'field' => 'variant_quantity',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 150
                ],
                [
                    'title' => 'Pur_price',
                    'field' => 'price_po',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 150
                ],
                [
                    'title' => 'Amount',
                    'field' => 'amount_po',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 150
                ],
                [
                    'title' => 'QC',
                    'field' => 'compliance',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 100
                ],
                [
                    'title' => 'Đúng_QC',
                    'field' => 'is_compliant',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 100
                ],
                [
                    'title' => 'Ghi chú 1',
                    'field' => 'note1',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 200
                ],
                [
                    'title' => 'Giá_cty',
                    'field' => 'company_price',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 150
                ],
                [
                    'title' => 'Level2',
                    'field' => 'level2',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 100
                ],
                [
                    'title' => 'Level3',
                    'field' => 'level3',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 100
                ],
                [
                    'title' => 'Level4',
                    'field' => 'level4',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 100
                ],
                [
                    'title' => 'Po_number',
                    'field' => 'po_number',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 150
                ],
                [
                    'title' => 'po_delivery_date',
                    'field' => 'po_delivery_date',
                    'headerSort' => false,
                    'visible' => true,
                    'width' => 150
                ]
            ];
            if ($fields['user_id']) {
                $json = json_decode($fields['tables'][0]);
                $userFieldTable = UserFieldTable::where('user_id', $fields['user_id'])->where('version', '2.0.0')->first();
                if ($fields['tables'][0] == "[]") {
                    if (!$userFieldTable) {
                        $userFieldTable = UserFieldTable::create([
                            'user_id' => $user->id,
                            'field_table' => $fields_default,
                            'version' => '2.0.0'
                        ]);
                    }
                    // $field_tables = $userFieldTable->field_table;
                    // if (!isset($field_tables[0]['set_width'])) {
                    //     $userFieldTable->field_table = $fields_default;
                    //     $userFieldTable->save();
                    // }
                } else {
                    $json = json_decode($fields['tables'][0]);
                    $userFieldTable->field_table = $json;
                    $userFieldTable->save();
                    return $userFieldTable;
                }
                return $userFieldTable;
            }
            return false;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
