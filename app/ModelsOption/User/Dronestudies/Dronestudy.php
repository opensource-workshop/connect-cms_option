<?php

namespace App\ModelsOption\User\Dronestudies;

use Illuminate\Database\Eloquent\Model;

use App\UserableNohistory;

class Dronestudy extends Model
{
    // 保存時のユーザー関連データの保持
    use UserableNohistory;

    // 更新する項目の定義
    protected $fillable = ['bucket_id', 'name'];

    // Laravel がBbs をすでに複数形と認識するためにテーブル名指定。
//    protected $table = 'bbses';

    /**
     * リモートURLを取得
     * トレーラースラッシュがない場合は、付加して返す。
     *
     * @return string
     */
/*
    public function getRemoteUrlAttribute()
    {
        if (substr($this->remote_url , -1) == '/') {
            return $this->remote_url;
        }
        return $this->remote_url . '/';
    }
*/
}
