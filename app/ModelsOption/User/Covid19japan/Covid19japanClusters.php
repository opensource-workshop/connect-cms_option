<?php

namespace App\ModelsOption\User\Covid19japan;

use Illuminate\Database\Eloquent\Model;

use App\Userable;

class Covid19japanClusters extends Model
{
    // 保存時のユーザー関連データの保持
    use Userable;

    // 更新する項目の定義
    protected $fillable = ['patient_id', 'knownClusterOne'];
}
