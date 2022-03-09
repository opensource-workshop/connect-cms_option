<?php

namespace App\ModelsOption\User\Dronestudies;

use Illuminate\Database\Eloquent\Model;

use App\UserableNohistory;

class Dronestudy extends Model
{
    // 保存時のユーザー関連データの保持
    use UserableNohistory;

    // 更新する項目の定義
    protected $fillable = ['bucket_id', 'name', 'command_interval', 'use_stream', 'max_block_count', 'remote_url', 'remote_id', 'secret_code', 'test_mode'];
}
