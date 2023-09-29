<?php

namespace App\Repositories\Master;

use App\Models\Master\DistributionChannel;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;

class DistributionChannelRepository extends RepositoryAbs
{
    public function getAvailableDistributionChannels()
    {
        try {
            $query = DistributionChannel::query();
            $channels = $query->get();
            $result = array();

            if ($this->request->filled('format')) {
                if ($this->request->format == 'treeselect') {
                        foreach ($channels as $channel) {
                            $item = array(
                                'id' => $channel->id,
                                'label' => $channel->name . ' (' . $channel->code . ')',
                                'object' => $channel
                            );
                            array_push($result, $item);
                        }
                }
            }
            else {
                $result = $channels;
            }
            return $result;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createNewDistributionChannel()
    {
        try {
            $validator = Validator::make($this->data, [
                'code' => 'required|string|unique:distribution_channels,code',
                'name' => 'required|string',

            ], [
                'code.required' => 'Yêu cầu nhập mã kênh.',
                'code.string' => 'Mã kênh phải là chuỗi.',
                'code.unique' => 'Mã kênh đã tồn tại.',
                'name.required' => 'Yêu cầu nhập tên kênh phân phối.',
                'name.string' => 'Tên kênh phân phối phải là chuỗi.',


            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $distributionChannel => $validator) {
                    if ($errors->has($distributionChannel)) {
                        $this->errors[$distributionChannel] = $errors->first($distributionChannel);
                        return false;
                    }
                }
            } else {
                $this->data['is_active'] = true;
                $distributionChannel = DistributionChannel::create($this->data);

                return $distributionChannel;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function updateOrInsert(){
        $result = array(
            'insert_count' => 0,
            'update_count' => 0,
            'skip_count' => 0,
            'delete_count' => 0,
            'error_count' => 0,
        );
        foreach ($this->data as $distributionChannel) {
            $validator = Validator::make($distributionChannel, [
                'code' => 'required|string',
            ], [
                'code.required' => 'Yêu cầu nhập mã kho.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $exist_distributionChannel = DistributionChannel::where('code', $distributionChannel['code'])->first();
                if ($exist_distributionChannel) {
                    $exist_distributionChannel->update([
                        'name' => $distributionChannel['name'],
                    ]);
                    $result['update_count']++;
                } else {

                    DistributionChannel::create([
                        'code' => $distributionChannel['code'],
                        'name' => $distributionChannel['name'],
                    ]);
                    $result['insert_count']++;
                }
            }
        }
    }
    public function updateExistingDistributionChannel($id)
    {
        try {
            $validator = Validator::make($this->data, [
                'code' => 'required|string',
                'name' => 'required|string',

            ], [
                'code.required' => 'Yêu cầu nhập mã kênh.',
                'code.string' => 'Mã kênh phải là chuỗi.',
                //'code.unique' => 'Mã kênh đã tồn tại.',
                'name.required' => 'Yêu cầu nhập tên kênh phân phối.',
                'name.string' => 'Tên kênh phân phối phải là chuỗi.',


            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $distributionChannel => $validator) {
                    if ($errors->has($distributionChannel)) {
                        $this->errors[$distributionChannel] = $errors->first($distributionChannel);
                        return false;
                    }
                }
            } else {
                $distributionChannel = DistributionChannel::findOrFail($id);
                $distributionChannel->update($this->data);

                return $distributionChannel;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteExistingDistributionChannel($id)
    {
        try {
            $distributionChannel = DistributionChannel::findOrFail($id);
            $distributionChannel->delete();
            return $distributionChannel;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
