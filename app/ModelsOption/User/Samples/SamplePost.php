<?php

namespace App\ModelsOption\User\Samples;

use Illuminate\Database\Eloquent\Model;
use App\UserableNohistory;

/**
 * 記事 モデル
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category サンプル・プラグイン
 * @package Controller
 */
class SamplePost extends Model
{
    // 保存時のユーザー関連データの保持
    use UserableNohistory;

    // 更新する項目の定義
    protected $fillable = ['sample_id', 'title', 'content'];
}
