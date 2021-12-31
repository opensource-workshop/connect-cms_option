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

        // 返すデータ取得
        $users = User::get();
        if (empty($user)) {
            $ret = array('code' => 404, 'message' => '該当のユーザがいません。');
        }

        $ret = array('code' => 200, 'message' => '', 'userid' => $users);
        return $this->encodeJson($ret, $request);
    }
}
