<?php

namespace App\PluginsOption\Api\Dronestudy;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\Validator;

use App\User;
//use App\Models\Core\UsersRoles;

//use App\Traits\ConnectCommonTrait;
use App\Plugins\Api\ApiPluginBase;

/**
 * DroneStudy関係APIクラス
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category DroneStudy関係API
 * @package Contoroller
 */
class DronestudyApi extends ApiPluginBase
{
    //use ConnectCommonTrait;

    /**
     *  ページ初期表示
     */
    public function getUsers($request, $userid)
    {
        // API 共通チェック
        $ret = $this->apiCallCheck($request);
        if (!empty($ret['code'])) {
            return $this->encodeJson($ret, $request);
        }

        // 対象のDroneStudy から、プログラム登録しているユーザを返す。
        $users = User::select('users.id', 'users.name')
                     ->join('dronestudy_posts', 'dronestudy_posts.created_id', '=', 'users.id')
                     ->where('dronestudy_posts.dronestudy_id', $request->dronestudy_id)
                     ->groupBy('users.id', 'users.name')
                     ->get();
        if (empty($user)) {
            $ret = array('code' => 404, 'message' => '該当のユーザがいません。');
        }

        // ソート
        $users = $users->sortBy('id');

        // 戻り値
        $ret = array('code' => 200, 'message' => '', 'userid' => $users);
        return $this->encodeJson($ret, $request);
    }
}
