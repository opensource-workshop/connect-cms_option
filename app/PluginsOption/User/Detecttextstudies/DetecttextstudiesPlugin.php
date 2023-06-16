<?php

namespace App\PluginsOption\User\Detecttextstudies;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

//use App\Models\Core\Configs;
//use Intervention\Image\Facades\Image;

use App\PluginsOption\User\UserPluginOptionBase;

/**
 * DetectTextStudy プラグイン
 *
 * DB 定義コマンド
 * DBなし
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category DetectTextStudy プラグイン
 * @package Controller
 */
class DetecttextstudiesPlugin extends UserPluginOptionBase
{
    /* オブジェクト変数 */

    /* コアから呼び出す関数 */

    /**
     * 関数定義（コアから呼び出す）
     */
    public function getPublicFunctions()
    {
        // 標準関数以外で画面などから呼ばれる関数の定義
        $functions = array();
        $functions['get']  = ['index'];
        $functions['post']  = ['detect'];
        return $functions;
    }

    /**
     *  権限定義
     */
    public function declareRole()
    {
        // 権限チェックテーブル
        $role_check_table = array();
        return $role_check_table;
    }

    /* 画面アクション関数 */

    /**
     * データ初期表示関数
     * コアがページ表示の際に呼び出す関数
     *
     * @method_title 記事編集
     * @method_desc 記事一覧を表示します。
     * @method_detail
     */
    public function index($request, $page_id, $frame_id, $post_id = null)
    {
        // 表示テンプレートを呼び出す。
        return $this->view('index', [
        ]);
    }

    /**
     * 文字認識
     */
    public function detect($request, $page_id, $frame_id)
    {
        // ファイル受け取り(リクエスト内)
        if (!$request->hasFile('photo') || !$request->file('photo')->isValid()) {
            return array('location' => 'error');
        }
        $image_file = $request->file('photo');

        //外部ファイル「aws.phar」を参照します。
        require('aws.phar');

        //認識する画像がアップロードされているURLを定義します。
        //$image_path = "http://study.localhost/".dirname($_SERVER["SCRIPT_NAME"])."/".$file;
//        $image_path = "https://osws2.sakura.ne.jp/study/".$file;
        //画像イメージを取得します。
//        $image = file_get_contents($image_path);

        // 終了
        exit;
    }
}
