<?php

namespace App\Repositories\Master;

use App\Models\Master\SapMaterial;
use App\Models\Master\SapUnit;
use App\Repositories\Abstracts\RepositoryAbs;
use App\Services\Excel\ExcelExtractor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\Company;


class SapMaterialRepository extends RepositoryAbs
{
    public function getAvailableSapMaterials($is_minified)
    {
        try {
            $query = SapMaterial::query();

            $is_searching = false;
            if ($this->request->filled('search')) {
                $query->search($this->request->search);
                $query->with(['unit']);
                $query->limit(50);
                $is_searching = true;
            }
            if ($this->request->filled('unit_ids')) {
                $query->whereIn('unit_id', $this->request->unit_ids);
            }
            if ($this->request->filled('ids')) {
                $query->whereIn('id', $this->request->ids);
            }
            if ($is_minified) {
                $query->select('id', 'name', 'sap_code', 'unit_id');
            }
            $sap_materials = $query->get();
            if ($is_searching) {
                $sap_materials_clone = clone $sap_materials;
                $sap_materials_clone->where('code', $this->request->search);
                if (!$sap_materials_clone->count()) {
                    $sap_materials = $sap_materials_clone;
                }
            }
            return $sap_materials;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function createNewSapMaterial()
    {
        try {
            $validator = Validator::make($this->data, [

                'sap_code' => 'required|string|unique:sap_materials,sap_code',
                'unit_id' => 'required|integer|exists:sap_units,id',
                'name' => 'required|string',
            ], [
                'sap_code.required' => 'Yêu cầu nhập mã material.',
                'sap_code.string' => 'Mã công ty phải là chuỗi.',
                'sap_code.unique' => 'Mã material đã tồn tại.',
                'unit_id.integer' => 'Mã unit phải là chuỗi.',
                'unit_id.exists' => 'Mã unit không tồn tại.',
                'name.required' => 'Yêu cầu nhập tên material.',
                'name.string' => 'Tên material phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $sapMaterial => $validator) {
                    if ($errors->has($sapMaterial)) {
                        $this->errors[$sapMaterial] = $errors->first($sapMaterial);
                        return false;
                    }
                }
            } else {

                $sap_unit = SapUnit::find($this->data['unit_id']);
                if (!$sap_unit) {
                    $this->errors = 'Không tìm thấy mã sap_unit ' . $this->data['unit_id'];
                    return false;
                }

                $this->data['unit_id'] = $sap_unit->id ?? null;

                $sapMaterial = SapMaterial::create([
                    'sap_code' => $this->data['sap_code'],
                    'unit_id' => $this->data['unit_id'],
                    'name' => $this->data['name'],
                ]);


                return $sapMaterial;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function updateOrInsert()
    {
        $validator = Validator::make($this->data, [
            // '*.company_code' => 'required|string|exists:companies,code',
            '*.code' => 'required|string',
            '*.name' => 'required|string',
        ], [
            // '*.company_code.required' => 'Yêu cầu nhập mã công ty.',
            // '*.company_code.string' => 'Mã công ty phải là chuỗi.',
            // '*.company_code.exists' => 'Mã công ty không tồn tại.',
            '*.code.required' => 'Yêu cầu nhập mã material.',
            '*.code.string' => 'Mã material phải là chuỗi.',
            '*.name.required' => 'Yêu cầu nhập tên material.',
            '*.name.string' => 'Tên material phải là chuỗi.',
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors()->all();
            return $this->errors;
        } else {
            try {
                $result = array(
                    'insert_count' => 0,
                    'update_count' => 0,
                    'skip_count' => 0,
                    'delete_count' => 0,
                    'error_count' => 0,
                );
                foreach ($this->data as $material) {
                    // $company_id = '';
                    $unit = SapUnit::where('unit_code', $material['unit_code'])->first();
                    if (!$unit) {
                        $unit = SapUnit::create(['unit_code' => $material['unit_code']]);
                    }
                    $exist_sap_material = SapMaterial::query()
                        ->where('sap_code', $material['code'])
                        ->where('unit_id', $unit->id)->first();

                    if ($material['status'] == "delete") {
                        $exist_sap_material->is_deleted = true;
                        $exist_sap_material->save();
                    } {
                        if ($exist_sap_material) {
                            $exist_sap_material->update([
                                'name' => $material['name'],
                            ]);
                            $result['update_count']++;
                        } else {

                            SapMaterial::create([
                                'unit_id' => $unit->id,
                                'sap_code' => $material['code'],
                                'name' => $material['name'],
                            ]);
                            $result['insert_count']++;
                        }
                    }
                }
                return $result;
            } catch (\Exception $exception) {
                $this->message = $exception->getMessage();
                $this->errors = $exception->getTrace();
            }
        }
    }
    public function updateExistingSapMaterial($id)
    {
        try {
            $validator = Validator::make($this->data, [

                'sap_code' => 'string:sap_materials,sap_code',
                'unit_id' => 'integer|exists:sap_units,id',
                'name' => 'required|string',
            ], [
                'sap_code.required' => 'Yêu cầu nhập mã material.',
                'sap_code.string' => 'Mã công ty phải là chuỗi.',
                //'sap_code.unique' => 'Mã material đã tồn tại.',
                'unit_id.integer' => 'Mã unit phải là chuỗi.',
                'unit_id.exists' => 'Mã unit không tồn tại.',
                'name.required' => 'Yêu cầu nhập tên material.',
                'name.string' => 'Tên material phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $SapMaterial => $validator) {
                    if ($errors->has($SapMaterial)) {
                        $this->errors[$SapMaterial] = $errors->first($SapMaterial);
                        return false;
                    }
                }
            } else {

                $sap_unit = SapUnit::find($this->data['unit_id']);
                if (!$sap_unit) {
                    $this->errors = 'Không tìm thấy mã sap_unit ' . $this->data['unit_id'];
                    return false;
                }

                $sapMaterial = SapMaterial::findOrFail($id);
                $sapMaterial->fill([
                    'sap_code' => $this->data['sap_code'],
                    'unit_id' => $this->data['unit_id'],
                    'name' => $this->data['name'],
                ]);
                $sapMaterial->save();

                return $sapMaterial;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteExistingSapMaterial($id)
    {
        try {
            $sapMaterial = SapMaterial::findOrFail($id);
            $sapMaterial->delete();
            return $sapMaterial;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
