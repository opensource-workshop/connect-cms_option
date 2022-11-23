<?php

namespace App\PluginsOption\Api\Dronestudy;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\Validator;

use App\User;
//use App\Models\Core\UsersRoles;
use App\ModelsOption\User\Dronestudies\DronestudyPost;

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
     *  対象ユーザ取得
     *  選択されているバケツにプログラムが登録されているユーザ
     */
    public function getUsers($request)
    {
        // API 共通チェック
        $ret = $this->apiCallCheck($request, 'Dronestudy');
        if (!empty($ret['code'])) {
            return $this->encodeJson($ret, $request);
        }

        // 対象のDroneStudy から、プログラム登録しているユーザを返す。
        $users = User::select('users.id', 'users.name')
                     ->join('dronestudy_posts', 'dronestudy_posts.created_id', '=', 'users.id')
                     ->where('dronestudy_posts.dronestudy_id', $request->dronestudy_id)
                     ->where('users.status', 0)
                     ->groupBy('users.id', 'users.name')
                     ->get();
        if (empty($user)) {
            $ret = array('code' => 404, 'message' => '該当のユーザがいません。');
        }

        // ソート
        $users = $users->sortBy('id');

        // 戻り値
        $ret = array('code' => 200, 'message' => '', 'users' => $users);
        return $this->encodeJson($ret, $request);
    }

    /**
     *  対象プログラムの一覧取得
     *  選択されているバケツの対象ユーザのプログラム
     */
    public function getPosts($request)
    {
        // API 共通チェック
        $ret = $this->apiCallCheck($request, 'Dronestudy');
        if (!empty($ret['code'])) {
            return $this->encodeJson($ret, $request);
        }

        // 対象のDroneStudy、ユーザから、プログラムを返す。
        $posts = DronestudyPost::select('dronestudy_posts.id', 'dronestudy_posts.title', 'users.id AS user_id', 'users.name')
                     ->join('users', 'dronestudy_posts.created_id', '=', 'users.id')
                     ->where('dronestudy_posts.dronestudy_id', $request->dronestudy_id)
                     ->where('dronestudy_posts.created_id', $request->user_id)
                     ->get();
        if (empty($user)) {
            $ret = array('code' => 404, 'message' => '該当のプログラムはありません。');
        }

        // ソート
        $posts = $posts->sortBy('post_id');

        // 戻り値
        $ret = array('code' => 200, 'message' => '', 'posts' => $posts);
        return $this->encodeJson($ret, $request);
    }

    /**
     *  対象プログラム取得
     *  選択されているプログラム
     */
    public function getPost($request)
    {
        // API 共通チェック
        $ret = $this->apiCallCheck($request, 'Dronestudy');
        if (!empty($ret['code'])) {
            return $this->encodeJson($ret, $request);
        }

        // 対象のDroneStudy、ユーザから、プログラムを返す。
        $post = DronestudyPost::find($request->post_id);
        if (empty($post)) {
            $ret = array('code' => 404, 'message' => '該当のプログラムはありません。');
        }

        // 戻り値
        $ret = array('code' => 200, 'message' => '', 'post' => $post);
        return $this->encodeJson($ret, $request);
    }
}
