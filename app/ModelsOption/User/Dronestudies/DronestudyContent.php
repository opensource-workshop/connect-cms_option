<?php

namespace App\ModelsOption\User\Dronestudies;

use Illuminate\Database\Eloquent\Model;

use App\UserableNohistory;

class DronestudyContent extends Model
{
    // 保存時のユーザー関連データの保持
    use UserableNohistory;

    // 更新する項目の定義
    protected $fillable = ['dronestudy_id', 'title', 'xml_text'];
}
