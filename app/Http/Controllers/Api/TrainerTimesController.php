<?php

namespace App\Http\Controllers\Api;

use App\Models\TrainerTime;
use App\Transformers\TrainerTimeTransformer;
use Illuminate\Http\Request;

class TrainerTimesController extends Controller
{
    // 获取自己的时刻表
    public function index(Request  $request,TrainerTime $trainerTime)
    {
        $user = $this->user();
        $query = $trainerTime->query()->where('user_id', $user->id);
        $trainerTimes = $query->get();
        return $this->response->collection($trainerTimes, new TrainerTimeTransformer());
    }
    // 教练设置自己的时刻表
    public function update(Request $request, TrainerTime $trainerTime)
    {
        $user = $this->user();
        // 调用接口之前先删除原来关于此教练的时间段
        TrainerTime::where('user_id',$user->id)->delete();
        // 添加或更新操作
        $time_infos = json_decode($request->time_infos,true);
        foreach ($time_infos as $k => $v) {
            foreach ($v as $key => $value) {
                TrainerTime::create([
                    'schedule_id' => $key,
                    'school_car_number' => $value,
                    'user_id' => $user->id,
                ]);
            }
        }
        $attributes['day_times'] = $request->day_times;
        $user->update($attributes);
        return $this->response->array([
            'code' => '0',
            'msg' => '设置成功',
        ]);
    }
}
