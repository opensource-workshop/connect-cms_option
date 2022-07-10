<?php

namespace App\PluginsOption\User\Dronestudies;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\Common\Buckets;
use App\Models\Common\Frame;
use App\Models\Core\FrameConfig;
use App\ModelsOption\User\Dronestudies\Dronestudy;
use App\ModelsOption\User\Dronestudies\DronestudyPost;

use App\Rules\CustomValiTextMax;

use App\PluginsOption\User\Dronestudies\Tello;

use App\PluginsOption\User\UserPluginOptionBase;

/**
 * DroneStudy・プラグイン
 *
 *  php artisan migrate --path=database/migrations_option
 *  php artisan migrate:rollback --path=database/migrations_option
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category DroneStudy・プラグイン
 * @package Controller
 */
class DronestudiesPlugin extends UserPluginOptionBase
{
    /* オブジェクト変数 */

    /**
     * 変更時のPOSTデータ
     */
    public $post = null;

    /* コアから呼び出す関数 */

    /**
     * 関数定義（コアから呼び出す）
     */
    public function getPublicFunctions()
    {
        // 標準関数以外で画面などから呼ばれる関数の定義
        $functions = array();
        $functions['get']  = ['index', 'remote'];
        $functions['post'] = ['run', 'remote'];
        return $functions;
    }

    /**
     *  権限定義
     */
    public function declareRole()
    {
        // 権限チェックテーブル
        $role_check_table = array();
        $role_check_table['remote'] = array('role_article');
        return $role_check_table;
    }

    /**
     * 編集画面の最初のタブ（コアから呼び出す）
     *
     * スーパークラスをオーバーライド
     */
    public function getFirstFrameEditAction()
    {
        return "editBuckets";
    }

    /**
     * プラグインのバケツ取得関数
     */
    private function getPluginBucket($bucket_id)
    {
        // プラグインのメインデータを取得する。
        return Dronestudy::firstOrNew(['bucket_id' => $bucket_id]);
    }

    /**
     * データ取得時の権限条件の付与
     */
    protected function appendAuthWhere($query, $table_name)
    {
        return $this->appendAuthWhereBase($query, $table_name);
    }

    /**
     * POST取得関数（コアから呼び出す）
     * コアがPOSTチェックの際に呼び出す関数
     */
    public function getPost($id, $action = null)
    {
        if (is_null($action)) {
            // プラグイン内からの呼び出しを想定。処理を通す。
        } elseif (in_array($action, ['index', 'save', 'delete'])) {
            // コアから呼び出し。posts.update|posts.deleteの権限チェックを指定したアクションは、処理を通す。
        } else {
            // それ以外のアクションは null で返す。
            return null;
        }

        // 一度読んでいれば、そのPOSTを再利用する。
        if (!empty($this->post)) {
            return $this->post;
        }

        // 権限によって表示する記事を絞る
        $this->post = DronestudyPost::
            where(function ($query) {
                $query = $this->appendAuthWhere($query, 'dronestudy_posts');
            })
            ->firstOrNew(['id' => $id]);

        return $this->post;
    }

    /* 画面アクション関数 */

    /**
     *  データ初期表示関数
     *  コアがページ表示の際に呼び出す関数
     */
    public function index($request, $page_id, $frame_id, $post_id = null)
    {
        // バケツ未設定の場合はバケツ空テンプレートを呼び出す
        if (!isset($this->frame) || !$this->frame->bucket_id) {
            // バケツ空テンプレートを呼び出す。
            return $this->view('empty_bucket');
        }

        // バケツデータ取得
        $dronestudy = $this->getPluginBucket($this->buckets->id);

        // ログインチェック
        if (Auth::check()) {
            // 編集対象のプログラム
            $post = $this->getPost($post_id);

            // 対象のDroneStudy
            $dronestudy = Dronestudy::where('dronestudies.bucket_id', $this->frame->bucket_id)->first();

            // ユーザのプログラム一覧
            $posts = DronestudyPost::select('dronestudy_posts.*')
                                   ->where('dronestudy_id', $dronestudy->id)
                                   ->where('created_id', Auth::user()->id)
                                   ->get();

        } else {
            $post = new DronestudyPost();
            $posts = new Collection();
        }

        // 表示テンプレートを呼び出す。
        return $this->view('index', [
            'dronestudy' => $dronestudy,
            'post' => $post,
            'posts' => $posts,
        ]);
    }

    /**
     *  API呼び出し
     */
    private function callApi($request_url)
    {
        // API 呼び出し
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $return_json = curl_exec($ch);
        //\Log::debug(json_decode($return_json, JSON_UNESCAPED_UNICODE));

        // セッションを終了する
        curl_close($ch);

        // デバッグ用コード
        // $check_result = json_decode($return_json, true);
        // Log::debug(print_r($check_result, true));

        // 結果取得(Std オブジェクト)
        $result_obj = json_decode($return_json);

        // API 結果の判定
        if (empty($return_json)) {
            abort(0, 'APIの返答がありませんでした。');
        } elseif ($result_obj->code == 200) {
            // OK
        } else {
            // 何らかの
            abort($result_obj->code, $result_obj->message);
        }
        return $return_json;
    }

    /**
     *  ユーザ取得
     */
    private function apiGetUsers($dronestudy)
    {
        // バケツデータ取得
        $dronestudy = $this->getPluginBucket($this->buckets->id);

        // リモートのURL 組み立て
        $request_url = $dronestudy->remote_url . "/api/dronestudy/getUsers?secret_code=" . $dronestudy->secret_code . "&dronestudy_id=" . $dronestudy->remote_id;

        // API呼び出し
        $return_json = $this->callApi($request_url);

        return json_decode($return_json)->users;
    }

    /**
     *  プログラムの一覧取得
     */
    private function apiGetPosts($dronestudy, $remote_user_id)
    {
        // バケツデータ取得
        $dronestudy = $this->getPluginBucket($this->buckets->id);

        // リモートのURL 組み立て
        $request_url = $dronestudy->remote_url . "/api/dronestudy/getPosts?secret_code=" . $dronestudy->secret_code . "&dronestudy_id=" . $dronestudy->remote_id . "&user_id=" . $remote_user_id;

        // API呼び出し
        $return_json = $this->callApi($request_url);

        return json_decode($return_json)->posts;
    }

    /**
     *  プログラム取得
     */
    private function apiGetPost($dronestudy, $remote_post_id)
    {
        // バケツデータ取得
        $dronestudy = $this->getPluginBucket($this->buckets->id);

        // リモートのURL 組み立て
        $request_url = $dronestudy->remote_url . "/api/dronestudy/getPost?secret_code=" . $dronestudy->secret_code . "&post_id=" . $remote_post_id;

        // API呼び出し
        $return_json = $this->callApi($request_url);

        return json_decode($return_json)->post;
    }

    /**
     *  リモート初期表示関数
     */
    public function remote($request, $page_id, $frame_id, $post_id = null)
    {
        // 項目のエラーチェック
        $validator = Validator::make($request->all(), []);
        $validator->setAttributeNames([
            'name' => 'DroneStudy名',
        ]);

        // バケツデータ取得
        $dronestudy = $this->getPluginBucket($this->buckets->id);

        // ユーザ取得
        $remote_users = $this->apiGetUsers($dronestudy);

        // API 結果確認
//        if (empty($return_json) || $return_json->code != 200) {
//            abort(403, '権限がありません。');
//        }

        if (old("remote_user_id", $request->filled("remote_user_id"))) {
            // ユーザが選択されていたら、プログラム一覧を取得する。
            $posts = $this->apiGetPosts($dronestudy, old("remote_user_id", $request->remote_user_id));
        } else {
            $posts = array();
        }

        // プログラムが選択されていたら、プログラムコードを取得する。
        if (old("remote_user_id", $request->filled("remote_post_id"))) {
            $post = $this->apiGetPost($dronestudy, old("remote_post_id", $request->remote_post_id));
        } else {
            $post = array();
        }

        // 表示テンプレートを呼び出す。
        return $this->view('remote', [
            'dronestudy' => $dronestudy,
            'remote_users' => $remote_users,
            'remote_user_id' => old("remote_user_id", $request->remote_user_id),
            'posts' => $posts,
            'post' => $post,
            'remote_post_id' => old("remote_post_id", $request->remote_post_id),
        ]);
    }

    /**
     *  Tello メソッドクリーニング
     */
    private function cleaningMethod($method_line)
    {
        // メソッドのリスト
        $run_method = [
            'takeoff'   => '',
            'land'      => '',
            'up'        => 'numeric',
            'down'      => 'numeric',
            'forward'   => 'numeric',
            'back'      => 'numeric',
            'right'     => 'numeric',
            'left'      => 'numeric',
            'cw'        => 'numeric',
            'ccw'       => 'numeric',
            'flip'      => 'f,b,r,l',
            'streamon'  => '',
            'streamoff' => '',
        ];
        $method_explode = explode(',', trim($method_line));

        // メソッドが想定のものか確認
        if (!array_key_exists($method_explode[0], $run_method)) {
            return "";
        }

        // 引数が想定のものか確認
        if ($run_method[$method_explode[0]] == 'numeric') {
            // 数値形式のチェック
            if (!is_numeric($method_explode[1])) {
                return "";
            }
        } elseif ($run_method[$method_explode[0]] == 'f,b,r,l') {
            // 前後左右のチェック
            if (!in_array($method_explode[1], explode(',', $run_method[$method_explode[0]]))) {
                return "";
            }
        }

        // [メソッド,引数] の配列を返す。
        $return_method = [$method_explode[0], count($method_explode) > 1 ? $method_explode[1] : ''];
        return $return_method;
    }

    /**
     *  実行
     */
    public function run($request, $page_id, $frame_id, $post_id = null)
    {
        // 入力エラーがあった場合は入力画面に戻る。
        if ($request->mode == 'local') {
            $validator = $this->getPostValidator($request);
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            }
        }

        // バケツデータ取得
        $dronestudy = $this->getPluginBucket($this->buckets->id);

        // 実行結果を表示するための記録用変数
        $run_result = array();

        // Tello API でソケット通信した際に、例外が発生する。
        try {
            $tello = new Tello($dronestudy->test_mode);
            // ブロックを個別命令に変換したものを受け取る。
            $drone_methods = $request->drone_methods;
            $method_lines = explode("\n", trim($drone_methods));
            // ブロックを実行
            foreach ($method_lines as $method_line) {
                $run_method = $this->cleaningMethod($method_line);
                //\Log::debug($run_method);
                // 可変関数が独立した変数で定義する必要があるため、配列要素から代入して使用
                $var_method = $run_method[0];
                $run_result[] = $var_method . "(" . $run_method[1] . ")";
                $tello->$var_method($run_method[1]);
                // テストモードの場合は間隔無し
                if (!$dronestudy->test_mode) {
                    sleep($dronestudy->command_interval);
                }
            }
        } catch (\Throwable $t) {
            // 画面でエラーが発生したことを伝える。
            $validator = Validator::make($request->all(), []);
            $error_msg = $t->getMessage();
            $validator->errors()->add('tello_exception', $error_msg);
            //\Log::debug($t->getTraceAsString()); // デバッグ用
            return back()->withInput($request->all())->withErrors($validator);
        }

        // 実行した詳細をフラッシュメッセージで画面に渡す。
        \Session::flash('run_result', $run_result);

        return back()->withInput();
    }

    /**
     * プラグインのバケツ選択表示関数
     */
    public function listBuckets($request, $page_id, $frame_id, $id = null)
    {
        // 表示テンプレートを呼び出す。
        return $this->view('list_buckets', [
            'plugin_buckets' => Dronestudy::orderBy('created_at', 'desc')->paginate(10, ["*"], "frame_{$frame_id}_page"),
        ]);
    }

    /**
     * バケツ新規作成画面
     */
    public function createBuckets($request, $page_id, $frame_id)
    {
        // 処理的には編集画面を呼ぶ
        return $this->editBuckets($request, $page_id, $frame_id);
    }

    /**
     * バケツ設定変更画面の表示
     */
    public function editBuckets($request, $page_id, $frame_id)
    {
        // コアがbucket_id なしで呼び出してくるため、bucket_id は frame_id から探す。
        if ($this->action == 'createBuckets') {
            $bucket_id = null;
        } else {
            $bucket_id = $this->getBucketId();
        }

        // 表示テンプレートを呼び出す。
        return $this->view('bucket', [
            // 表示中のバケツデータ
            'dronestudy' => $this->getPluginBucket($bucket_id),
        ]);
    }

    /**
     * DroneStudy登録/更新のバリデーターを取得する。
     *
     * @param \Illuminate\Http\Request $request リクエスト
     * @return \Illuminate\Contracts\Validation\Validator バリデーター
     */
    private function getBucketValidator($request)
    {
        // 項目のエラーチェック
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'max:255'
            ],
            'command_interval' => [
                'required',
                'numeric'
            ],
            'remote_id' => [
                'nullable',
                'numeric'
            ],
            'max_block_count' => [
                'nullable',
                'numeric'
            ],
        ]);
        $validator->setAttributeNames([
            'name' => 'DroneStudy名',
            'command_interval' => '命令間隔（秒）',
            'remote_id' => 'リモートID',
            'max_block_count' => '最大ブロック数',
        ]);

        return $validator;
    }

    /**
     *  バケツ登録処理
     */
    public function saveBuckets($request, $page_id, $frame_id, $bucket_id = null)
    {
        // 入力エラーがあった場合は入力画面に戻る。
        $validator = $this->getBucketValidator($request);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $bucket_id = $this->saveDronestudy($request, $frame_id, $bucket_id);

        // 登録後はリダイレクトして編集ページを開く。
        return new Collection(['redirect_path' => url('/') . "/plugin/dronestudies/editBuckets/" . $page_id . "/" . $frame_id . "/" . $bucket_id . "#frame-" . $frame_id]);
    }

    /**
     * DroneStudy を登録する。
     *
     * @param \Illuminate\Http\Request $request リクエスト
     * @param int $frame_id フレームID
     * @param int $bucket_id バケツID
     * @return int バケツID
     */
    private function saveDronestudy($request, $frame_id, $bucket_id)
    {
        // バケツの取得。なければ登録。
        $bucket = Buckets::updateOrCreate(
            ['id' => $bucket_id],
            ['bucket_name' => $request->name, 'plugin_name' => 'dronestudies'],
        );

        // フレームにバケツの紐づけ
        $frame = Frame::find($frame_id)->update(['bucket_id' => $bucket->id]);

        // プラグインバケツを取得(なければ新規オブジェクト)
        // プラグインバケツにデータを設定して保存
        $dronestudy = $this->getPluginBucket($bucket->id);
        $dronestudy->name = $request->name;
        $dronestudy->command_interval = $request->command_interval;
        $dronestudy->use_stream = $request->use_stream;
        $dronestudy->max_block_count = empty($request->max_block_count) ? 0 : $request->max_block_count;
        $dronestudy->remote_url = $request->remote_url;
        $dronestudy->remote_id = $request->remote_id;
        $dronestudy->secret_code = $request->secret_code;
        $dronestudy->test_mode = $request->test_mode;
        $dronestudy->save();

        return $bucket->id;
    }

    /**
     * プログラム登録/更新のバリデーターを取得する。
     *
     * @param \Illuminate\Http\Request $request リクエスト
     * @return \Illuminate\Contracts\Validation\Validator バリデーター
     */
    private function getPostValidator($request)
    {
//\Log::debug($request->xml_text);
        // 項目のエラーチェック
        $validator = Validator::make($request->all(), [
            //'title' => [
            //    'required',
            //    'max:255'
            //],
            'xml_text' => [
                'required',
                new CustomValiTextMax()
            ],
        ]);
        $validator->setAttributeNames([
            'title' => 'タイトル',
            'xml_text' => 'プログラム',
        ]);

        return $validator;
    }

    /**
     * DroneStudy コンテンツを登録する。
     *
     * @param \Illuminate\Http\Request $request リクエスト
     * @param int $frame_id フレームID
     * @param int $bucket_id バケツID
     * @return int バケツID
     */
    public function save($request, $page_id, $frame_id)
    {
        // 入力エラーがあった場合は入力画面に戻る。
        $validator = $this->getPostValidator($request);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // タイトルがすでにある場合は、「タイトル」＋「-」として、- を一つ追加する。
        $title = empty($request->title) ? '[無題]' : $request->title;
        $check_title = DronestudyPost::where('created_id', Auth::user()->id)->where('title', $title)->where('id', '<>', $request->post_id)->first();
        if (!empty($check_title)) {
            $title = $title . "-";
        }

        // ブロックのXML をそのまま保存する。
        $dronestudy = $this->getPluginBucket($this->buckets->id);
        $post = DronestudyPost::updateOrCreate(
            ['id' => $request->post_id],
            [
                'dronestudy_id' => $dronestudy->id,
                'title' => $title,
                'xml_text' => $request->xml_text,
            ],
        );
        // 登録後はリダイレクトして編集ページを開く。
        return new Collection(['redirect_path' => url('/') . "/plugin/dronestudies/index/" . $page_id . "/" . $frame_id . "/" . $post->id . "#frame-" . $frame_id]);
    }

    /**
     * 削除処理
     */
    public function delete($request, $page_id, $frame_id, $post_id)
    {
        // id がある場合、データを削除
        if ($post_id) {
            DronestudyPost::where('id', $post_id)->delete();
        }
        // 登録後はリダイレクトして一覧ページを開く。
        return new Collection(['redirect_path' => url('/') . "/plugin/dronestudies/index/" . $page_id . "/" . $frame_id . "#frame-" . $frame_id]);
        return;
    }

    /**
     *  DroneStudy削除処理
     *
     * @param \Illuminate\Http\Request $request リクエスト
     * @param int $page_id ページID
     * @param int $frame_id フレームID
     */
    public function destroyBuckets($request, $page_id, $frame_id, $dronestudy_id)
    {
        // プラグインバケツの取得
        $dronestudy = Dronestudy::find($dronestudy_id);
        if (empty($dronestudy)) {
            return;
        }

        // FrameのバケツIDの更新
        Frame::where('id', $frame_id)->update(['bucket_id' => null]);

        // バケツ削除
        Buckets::find($dronestudy->bucket_id)->delete();

        // DroneStudyコンテンツ削除
        $dronestudy_post = $this->fetchDroneStudyPost(null, $dronestudy->id);
        $this->deleteDroneStudyPosts(
            $dronestudy_post->id
        );

        // プラグインデータ削除
        $dronestudy->delete();

        return;
    }

    /**
     * データ紐づけ変更関数
     *
     * @param \Illuminate\Http\Request $request リクエスト
     * @param int $page_id ページID
     * @param int $frame_id フレームID
     */
    public function changeBuckets($request, $page_id, $frame_id)
    {
        // FrameのバケツIDの更新
        Frame::where('id', $frame_id)->update(['bucket_id' => $request->select_bucket]);

        // DroneStudy の特定
        $plugin_bucket = $this->getPluginBucket($request->select_bucket);
    }

    /**
     * 権限設定　変更画面を表示する
     *
     * @see UserPluginBase::editBucketsRoles()
     */
    public function editBucketsRoles($request, $page_id, $frame_id, $id = null, $use_approval = false)
    {
        // 承認機能は使わない
        return parent::editBucketsRoles($request, $page_id, $frame_id, $id, $use_approval);
    }

    /**
     * 権限設定を保存する
     *
     * @see UserPluginBase::saveBucketsRoles()
     */
    public function saveBucketsRoles($request, $page_id, $frame_id, $id = null, $use_approval = false)
    {
        // 承認機能は使わない
        return parent::saveBucketsRoles($request, $page_id, $frame_id, $id, $use_approval);
    }

    /**
     * フレーム表示設定画面の表示
     */
    public function editView($request, $page_id, $frame_id)
    {
        // 表示テンプレートを呼び出す。
        return $this->view('frame', [
            'dronestudy' => $this->getPluginBucket($this->getBucketId()),
        ]);
    }

    /**
     * フレーム表示設定の保存
     */
    public function saveView($request, $page_id, $frame_id, $dronestudy_id)
    {
        // フレーム設定保存
        $this->saveFrameConfigs($request, $frame_id);
        // 更新したので、frame_configsを設定しなおす
        $this->refreshFrameConfigs();

        return;
    }

    /**
     * フレーム設定を保存する。
     *
     * @param Illuminate\Http\Request $request リクエスト
     * @param int $frame_id フレームID
     * @param array $frame_config_names フレーム設定のname配列
     */
    protected function saveFrameConfigs(\Illuminate\Http\Request $request, int $frame_id)
    {
        FrameConfig::updateOrCreate(
            ['frame_id' => $frame_id, 'name' => 'dronestudy_language'],
            ['value' => $request->dronestudy_language]
        );
        FrameConfig::updateOrCreate(
            ['frame_id' => $frame_id, 'name' => 'dronestudy_local_notrun'],
            ['value' => $request->dronestudy_local_notrun]
        );
    }
}
