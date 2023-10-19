<?php

namespace App\ModelsOption\User\Photoalbumviewers;

use Illuminate\Database\Eloquent\Model;
use App\UserableNohistory;

/**
 * フォトアルバムビューア・バケツ モデル
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category フォトアルバムビューア・プラグイン
 * @package Controller
 */
class Photoalbumviewer extends Model
{
    // 保存時のユーザー関連データの保持
    use UserableNohistory;

    // 更新する項目の定義
    protected $fillable = ['bucket_id', 'bucket_name', 'photoalbum_id', 'col_count', 'row_count', 'link_frame_id'];
}
