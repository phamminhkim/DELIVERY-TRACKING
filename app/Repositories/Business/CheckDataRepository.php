<?php

namespace App\Repositories\Business;

use App\Models\Business\UploadedFile;
use App\Models\Master\CustomerMaterial;
use App\Models\Master\SapMaterial;
use App\Models\Master\SapMaterialMapping;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\Implementations\Files\LocalFileService;
use Throwable;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CheckDataRepository extends RepositoryAbs
{
    protected $file_service;

    public function __construct($request)
    {
        parent::__construct($request);
        $this->file_service = new LocalFileService();
    }
    public function checkMaterialSAP($file_id)
    {
        try {

            $file_record = UploadedFile::find($file_id);

            if (!$file_record) {
                $this->message = 'File không tồn tại';
                return false;
            }

            // Lấy thông tin file
            $file_path = $file_record->file_path;

            // Tìm dữ liệu liên quan dựa trên ID của file
            $customerMaterials = CustomerMaterial::select('customer_sku_code', 'customer_sku_unit')
                ->get();

            $mappingData = [];
            $unmappedData = [];

            foreach ($customerMaterials as $customerMaterial) {
                $customer_sku_code = $customerMaterial->customer_sku_code;
                $customer_sku_unit = $customerMaterial->customer_sku_unit;

                // Tìm bản ghi tương ứng trong bảng sap_material dựa trên mã barcode
                $sapMaterial = SapMaterial::where('bar_code', $customer_sku_code)->first();

                if ($sapMaterial) {
                    $sap_code = $sapMaterial->sap_code;
                    $bar_code = $sapMaterial->bar_code;
                    $name = $sapMaterial->name;

                    // Lưu thông tin ánh xạ vào mảng
                    $mappingData[] = [
                        'customer_sku_code' => $customer_sku_code,
                        'customer_sku_unit' => $customer_sku_unit,
                        'bar_code' => $bar_code,
                        'sap_code' => $sap_code,
                        'name' => $name,
                    ];
                } else {
                    // Tìm bản ghi tương ứng trong bảng sap_material_mapping dựa trên customer_sku_code
                    $sapMaterialMapping = SapMaterialMapping::where('customer_material_id', $customer_sku_code)->first();

                    if ($sapMaterialMapping) {
                        $sap_material_id = $sapMaterialMapping->sap_material_id;

                        // Tìm thông tin từ bảng sap_material dựa trên sap_material_id
                        $sapMaterial = SapMaterial::find($sap_material_id);

                        if ($sapMaterial) {
                            $sap_code = $sapMaterial->sap_code;
                            $name = $sapMaterial->name;

                            // Lưu thông tin ánh xạ vào mảng
                            $mappingData[] = [
                                'customer_sku_code' => $customer_sku_code,
                                'customer_sku_unit' => $customer_sku_unit,
                                'bar_code' => null,
                                'sap_material_id' => $sap_material_id,
                                'sap_code' => $sap_code,
                                'name' => $name,
                            ];
                        } else {
                            // Xử lý trường hợp sap_material không tồn tại
                        }
                    } else {
                        // Lưu thông tin bản ghi chưa được ánh xạ vào mảng
                        $unmappedData[] = [
                            'customer_sku_code' => $customer_sku_code,
                            'customer_sku_unit' => $customer_sku_unit,
                        ];
                    }
                }
            }

            // Trả về thông tin của file và kết quả dò dữ liệu
            return response()->json([
                'file_id' => $file_record,
                'filePath' => $file_path,
                'mappingData' => $mappingData,
                'unmappedData' => $unmappedData,
            ]);
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

}
