<?php

namespace App\Models;

class UserLog extends BaseModel
{
    const ACTION_LOGIN = '登录系统';
    const ACTION_LOGOUT = '退出系统';
    const ACTION_CREATE = '添加';
    const ACTION_UPDATE = '修改';
    const ACTION_DELETE = '删除';
    const ACTION_STATE = '状态';

    protected $fillable = [
        'refer_id',
        'refer_type',
        'action',
        'ip',
        'data',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function refer()
    {
        return $this->morphTo();
    }

    public static function record($action, $user_id = 0, $model = null, $data = [])
    {
        if ($model) {
            $model->useLogs()->create([
                'action' => $action,
                'ip' => get_client_ip(),
                'data' => json_encode($data),
                'user_id' => $user_id,
            ]);
        } else {
            UserLog::create([
                'action' => $action,
                'ip' => get_client_ip(),
                'data' => json_encode($data),
                'user_id' => $user_id,
            ]);
        }

    }
}
