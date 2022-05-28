<?php

namespace App\PluginsOption\User\Facestudies;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

use App\Models\Common\Buckets;
use App\Models\Common\Frame;
use App\Models\Core\Configs;

use Intervention\Image\Facades\Image;

use App\PluginsOption\User\UserPluginOptionBase;

/**
 * FaceStydy プラグイン
 *
 * DB 定義コマンド
 * DBなし
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category FaceStudy プラグイン
 * @package Controller
 */
class FacestudiesPlugin extends UserPluginOptionBase
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
        $functions['post']  = ['face'];
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
     * 顔認識
     * 外部サービスの呼び出し
     */
    public function face($request, $page_id, $frame_id)
    {
        // ファイル受け取り(リクエスト内)
        if (!$request->hasFile('photo') || !$request->file('photo')->isValid()) {
            return array('location' => 'error');
        }
        $image_file = $request->file('photo');

        // GDのリサイズでメモリを多く使うため、memory_limitセット
        $configs = Configs::getSharedConfigs();
        $memory_limit_for_image_resize = Configs::getConfigsValue($configs, 'memory_limit_for_image_resize', '256M');
        ini_set('memory_limit', $memory_limit_for_image_resize);

        // ファイルのリサイズ(メモリ内)
        $image = Image::make($image_file);

        // リサイズ
        $resize_width = null;
        $resize_height = null;
        if ($image->width() > $image->height()) {
            $resize_width = $request->image_size;
        } else {
            $resize_height = $request->image_size;
        }

        $image = $image->resize($resize_width, $resize_height, function ($constraint) {
            // 横幅を指定する。高さは自動調整
            $constraint->aspectRatio();

            // 小さい画像が大きくなってぼやけるのを防止
            $constraint->upsize();
        });

        // 画像の回転対応: orientate()
        $image = $image->orientate();

        // cURLセッションを初期化する
        $ch = curl_init();

        // 送信データを指定
        $data = [
            'api_key' => config('connect.FACE_AI_API_KEY'),
//            'mosaic_fineness' => $request->mosaic_fineness,
            'method' => $request->method,
            'mosaic_fineness' => 'medium',
            'photo' => base64_encode($image->stream()),
            'extension' => $request->file('photo')->getClientOriginalExtension(),
        ];
        //\Log::debug($data);

        // API URL取得
        $api_url = config('connect.FACE_AI_API_URL');

        // URLとオプションを指定する
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // URLの情報を取得する
        $res = curl_exec($ch);
        //\Log::debug($res);

        // セッションを終了する
        curl_close($ch);

        // ファイルデータをdecode して復元
        $res_base64 = json_decode($res, true);
        //\Log::debug($res_base64);

        // エラーチェック
        if (array_key_exists('errors', $res_base64) && array_key_exists('message', $res_base64['errors']) && !empty($res_base64['errors']['message'])) {
            $msg_array['link_text'] = '<p>エラーが発生しています：' . (array_key_exists('message', $res_base64['errors']) ? $res_base64['errors']['message'] : 'メッセージなし' ) . '</p>';
            return $msg_array;
        }

        // ファイル返却
//        echo base64_decode($res_base64['mosaic_photo']);
        echo $res_base64['mosaic_photo'];

        // 終了
        exit;
    }
}
