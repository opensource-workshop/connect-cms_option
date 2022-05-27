<?php

namespace App\PluginsOption\User\Speechstudies;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

use App\Models\Common\Buckets;
use App\Models\Common\Frame;

use App\PluginsOption\User\UserPluginOptionBase;

/**
 * SpeechStydy プラグイン
 *
 * DB 定義コマンド
 * DBなし
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category サンプル・プラグイン
 * @package Controller
 */
class SpeechstudiesPlugin extends UserPluginOptionBase
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
        $functions['post']  = ['speech'];
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

    /**
     * プラグインのバケツ取得関数
     */
    private function getPluginBucket($bucket_id)
    {
        // プラグインのメインデータを取得する。
        return Sample::firstOrNew(['bucket_id' => $bucket_id]);
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
     * 音声合成
     * 外部サービスの呼び出し
     */
    public function speech($request, $page_id, $frame_id)
    {
        // cURLセッションを初期化する
        $ch = curl_init();

        // 送信データを指定
        $data = [
            'api_key' => config('connect.SPEECH_API_KEY'),
            'text'    => str_replace(array("\r\n", "\r", "\n"), '', $request->text),
            'voiceId' => $request->voiceId,
            'rate'    => $request->rate,
        ];
        //\Log::debug($data);

        // API URL取得
        $api_url = config('connect.SPEECH_API_URL');

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

        // ファイルデータをdecode して復元、保存
        $res_base64 = json_decode($res, true);
        //\Log::debug($res_base64);

        // エラーチェック
        if (array_key_exists('errors', $res_base64) && array_key_exists('message', $res_base64['errors']) && !empty($res_base64['errors']['message'])) {
            $msg_array['link_text'] = '<p>エラーが発生しています：' . (array_key_exists('message', $res_base64['errors']) ? $res_base64['errors']['message'] : 'メッセージなし' ) . '</p>';
            return $msg_array;
        }

        // ファイル返却
        echo base64_decode($res_base64['AudioStream']);

        // 終了
        exit;
    }
}
