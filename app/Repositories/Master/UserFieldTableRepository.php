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
            if ($fields['user_id']) {
                $userFieldTable = UserFieldTable::where('user_id', $fields['user_id'])->first();
                if ($fields['tables'][0] == "[]") {
                    if (!$userFieldTable) {
                        $fields_default = [
                            [
                                'key' => 'selected',
                                'label' => '',
                                'class' => 'text-nowrap   ',
                                'tdClass' => 'checkbox-sticky-left text-center',
                                'thClass' => 'checkbox-sticky-left text-center',
                                'isShow' => true,
                                'sort_table' => 0,
                            ],
                            [
                                'key' => 'action',
                                'label' => '',
                                'class' => 'text-nowrap ',
                                'tdClass' => 'checkbox-sticky-center text-center',
                                'thClass' => 'checkbox-sticky-center text-center',
                                'isShow' => true,
                                'sort_table' => 1,

                            ],
                            [
                                'key' => 'index',
                                'label' => 'STT',
                                'class' => 'text-nowrap text-center  ',
                                'sort_table' => 2,
                                'tdClass' => 'checkbox-sticky-end text-center border',
                                'thClass' => 'checkbox-sticky-header-end text-center',
                                'isShow' => true,

                            ],
                            [
                                'key' => 'customer_name',
                                'label' => 'Makh Key',
                                'class' => 'text-nowrap  ',
                                'sort_table' => 3,
                                'thClass' => 'border',
                                'isShow' => true,


                            ],
                            [
                                'key' => 'sap_so_number',
                                'label' => 'Mã Sap So',
                                'class' => 'text-nowrap   ',
                                'sort_table' => 4,
                                'thClass' => 'border',
                                'isShow' => true,

                            ],
                            [
                                'key' => 'so_sap_note',
                                'label' => 'Sap note',
                                'class' => 'text-nowrap   ',
                                'sort_table' => 5,
                                'thClass' => 'border',
                                'isShow' => true,

                            ],
                            [
                                'key' => 'barcode',
                                'label' => 'Barcode_cty',
                                'class' => 'text-nowrap   ',
                                'sort_table' => 6,
                                'thClass' => 'border',
                                'isShow' => true,


                            ],
                            [
                                'key' => 'sku_sap_code',
                                'label' => 'Masap',
                                'class' => 'text-nowrap text-center  ',
                                'sort_table' => 7,
                                'thClass' => 'border',
                                'isShow' => true,


                            ],
                            [
                                'key' => 'sku_sap_name',
                                'label' => 'Tensp',
                                'class' => 'text-nowrap   ',
                                'sort_table' => 8,
                                'thClass' => 'border',
                                'isShow' => true,



                            ],
                            [
                                'key' => 'quantity3_sap',
                                'label' => 'SL_sap',
                                'class' => 'text-nowrap   ',
                                'sort_table' => 9,
                                'thClass' => 'border',
                                'isShow' => true,



                            ],
                            [
                                'key' => 'sku_sap_unit',
                                'label' => 'Dvt',
                                'class' => 'text-nowrap   ',
                                'sort_table' => 10,
                                'thClass' => 'border',
                                'isShow' => true,


                            ],

                            [
                                'key' => 'promotive',
                                'label' => 'Km',
                                'class' => 'text-nowrap   ',
                                'tdClass' => 'voucher-custom border p-0 ',
                                'sort_table' => 11,
                                'thClass' => 'border',
                                'isShow' => true,


                            ],
                            [
                                'key' => 'note',
                                'label' => 'Ghi_chu',
                                'class' => 'text-nowrap   ',
                                'sort_table' => 12,
                                'thClass' => 'border',
                                'isShow' => true,


                            ],
                            [
                                'key' => 'customer_code',
                                'label' => 'Makh',
                                'class' => 'text-nowrap   ',
                                'sort_table' => 13,
                                'thClass' => 'border',
                                'isShow' => true,


                            ],
                            [
                                'key' => 'customer_sku_code',
                                'label' => 'Unit_barcode',
                                'class' => 'text-nowrap text-center  ',
                                'sort_table' => 14,
                                'thClass' => 'border',
                                'isShow' => true,
                            ],
                            [
                                'key' => 'customer_sku_name',
                                'label' => 'Unit_barcode_description',
                                'class' => 'text-nowrap   ',
                                'sort_table' => 15,
                                'thClass' => 'border',
                                'isShow' => true,


                            ],
                            [
                                'key' => 'customer_sku_unit',
                                'label' => 'Dvt_po',
                                'class' => 'text-nowrap   ',
                                'sort_table' => 16,
                                'thClass' => 'border',
                                'isShow' => true,


                            ],
                            [
                                'key' => 'po',
                                'label' => 'Po',
                                'class' => 'text-nowrap   ',
                                'sort_table' => 17,
                                'thClass' => 'border',
                                'isShow' => true,


                            ],
                            [
                                'key' => 'quantity1_po',
                                'label' => 'Qty',
                                'class' => "text-nowrap  ",
                                'sort_table' => 18,
                                'thClass' => 'border',
                                'isShow' => true,


                            ],
                            [
                                'key' => 'promotive_name',
                                'label' => 'Combo',
                                'class' => 'text-nowrap   ',
                                'sort_table' => 19,
                                'thClass' => 'border',
                                'isShow' => true,



                            ],
                            [
                                'key' => 'inventory_quantity',
                                'label' => 'Check tồn',
                                'class' => "text-nowrap  ",
                                'sort_table' => 20,
                                'thClass' => 'border',
                                'isShow' => true,


                            ],
                            [
                                'key' => 'quantity2_po',
                                'label' => 'Po_qty',
                                'class' => "text-nowrap  ",
                                'sort_table' => 21,
                                'thClass' => 'border',
                                'isShow' => true,


                            ],
                            [
                                'key' => 'variant_quantity',
                                'label' => 'SL Chênh lệch',
                                'class' => "text-nowrap  ",
                                'sort_table' => 22,
                                'thClass' => 'border',
                                'isShow' => true,
                            ],
                            [
                                'key' => 'price_po',
                                'label' => 'Pur_price',
                                'class' => "text-nowrap  ",
                                'sort_table' => 23,
                                'thClass' => 'border',
                                'isShow' => true,


                            ],

                            [
                                'key' => 'amount_po',
                                'label' => 'Amount',
                                'class' => "text-nowrap  ",
                                'sort_table' => 24,
                                'thClass' => 'border',
                                'isShow' => true,


                            ],
                            [
                                'key' => 'compliance',
                                'label' => 'QC',
                                'class' => 'text-nowrap   ',
                                'sort_table' => 25,
                                'thClass' => 'border',
                                'isShow' => true,

                            ],
                            [
                                'key' => 'is_compliant',
                                'label' => 'Đúng_QC',
                                'sort_table' => 26,
                                'thClass' => 'border',
                                'class' => 'text-center   text-nowrap',
                                'isShow' => true,

                            ],
                            [
                                'key' => 'note1',
                                'label' => 'Ghi chú 1',
                                'class' => 'text-nowrap   ',
                                'sort_table' => 27,
                                'thClass' => 'border',
                                'isShow' => true,


                            ],
                            [
                                'key' => 'company_price',
                                'label' => 'Gia_cty',
                                'class' => 'text-nowrap   ',
                                'sort_table' => 28,
                                'thClass' => 'border',
                                'isShow' => true,


                            ],
                            [
                                'key' => 'level2',
                                'label' => 'Level_2',
                                'class' => 'text-nowrap   ',
                                'sort_table' => 29,
                                'thClass' => 'border',
                                'isShow' => true,


                            ],
                            [
                                'key' => 'level3',
                                'label' => 'Level_3',
                                'class' => 'text-nowrap   ',
                                'sort_table' => 30,
                                'thClass' => 'border',
                                'isShow' => true,


                            ],
                            [
                                'key' => 'level4',
                                'label' => 'Level_4',
                                'class' => 'text-nowrap   ',
                                'sort_table' => 31,
                                'thClass' => 'border',
                                'isShow' => true,


                            ],
                            [
                                'key' => 'po_number',
                                'label' => 'po_number',
                                'class' => 'text-nowrap   ',
                                'sort_table' => 32,
                                'thClass' => 'border',
                                'isShow' => true,


                            ],
                            [
                                'key' => 'po_delivery_date',
                                'label' => 'po_delivery_date',
                                'class' => 'text-nowrap   ',
                                'sort_table' => 33,
                                'thClass' => 'border',
                                'isShow' => true,

                            ],
                        ];
                        $userFieldTable = UserFieldTable::create([
                            'user_id' => $user->id,
                            'field_table' => $fields_default
                        ]);
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
}
