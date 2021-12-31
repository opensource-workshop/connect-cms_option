<?php

namespace App\PluginsOption\User\DroneStudies;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
//use Illuminate\Validation\Rule;

use App\Models\Common\Buckets;
use App\Models\Common\Frame;
//use App\Models\Common\Page;
//use App\Models\Common\PageRole;
//use App\Models\Common\Uploads;
use App\Models\Core\FrameConfig;
use App\ModelsOption\User\Dronestudies\Dronestudy;
use App\ModelsOption\User\Dronestudies\DronestudyPost;

//use App\Enums\UploadMaxSize;
//use App\Enums\CabinetFrameConfig;
//use App\Enums\CabinetSort;

use App\PluginsOption\User\DroneStudies\Tello;

use App\PluginsOption\User\UserPluginOptionBase;

// use function PHPUnit\Framework\isEmpty;

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
        $functions['get']  = ['index', 'remote', 'run'];
        $functions['post'] = [];
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
            $dronestudy_post = $this->getPost($post_id);

            // ユーザのプログラム一覧
            $dronestudy_posts = DronestudyPost::where('created_id', Auth::user()->id)->get();
        } else {
            $dronestudy_post = new DronestudyPost();
            $dronestudy_posts = new Collection();
        }

        // 表示テンプレートを呼び出す。
        return $this->view('index', [
            'dronestudy' => $dronestudy,
            'dronestudy_post' => $dronestudy_post,
            'dronestudy_posts' => $dronestudy_posts,
        ]);
    }

    /**
     *  ユーザ取得
     */
    private function apiGetUsers($dronestudy)
    {
        // リモートのURL 組み立て
        $request_url = $dronestudy->remote_url . "/getUsers?secret_code=" . $dronestudy->secret_code;

        // API 呼び出し
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $return_json = curl_exec($ch);
        //\Log::debug(json_decode($return_json, JSON_UNESCAPED_UNICODE));

        // JSON データを複合化
//        $check_result = json_decode($return_json, true);
        //Log::debug(print_r($check_result, true));

        // 権限エラー
//        if (!$check_result["check"]) {
//            abort(403, "認証エラー。");
//        }
    }

    /**
     *  リモート初期表示関数
     */
    public function remote($request, $page_id, $frame_id, $post_id = null)
    {
        // バケツデータ取得
        $dronestudy = $this->getPluginBucket($this->buckets->id);

        // ユーザ取得
        $this->apiGetUsers($dronestudy);



        // 表示テンプレートを呼び出す。
        return $this->view('remote', [
//            'dronestudy' => $dronestudy,
//            'dronestudy_post' => $dronestudy_post,
//            'dronestudy_posts' => $dronestudy_posts,
        ]);
    }

    /**
     *  実行
     */
    public function run($request, $page_id, $frame_id, $post_id = null)
    {
        $tello = new Tello();

        $tello->takeoff();

        sleep(5);

        $tello->land();

        return $this->index($request, $page_id, $frame_id, $post_id);
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
        ]);
        $validator->setAttributeNames([
            'name' => 'DroneStudy名',
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
        $dronestudy->remote_url = $request->remote_url;
        $dronestudy->secret_code = $request->secret_code;
        $dronestudy->save();

        return $bucket->id;
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
        // ブロックのXML をそのまま保存する。
        $dronestudy = $this->getPluginBucket($this->buckets->id);
        $post = DronestudyPost::updateOrCreate(
            ['id' => $request->post_id],
            [
                'dronestudy_id' => $dronestudy->id,
                'title' => $request->title,
                'xml_text' => $request->xml_text,
            ],
        );
        // 登録後はリダイレクトして編集ページを開く。
        return new Collection(['redirect_path' => url('/') . "/plugin/dronestudies/index/" . $page_id . "/" . $frame_id . "/" . $post->id . "#frame-" . $frame_id]);
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
    }
}
