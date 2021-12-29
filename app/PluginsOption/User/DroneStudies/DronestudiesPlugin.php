<?php

namespace App\PluginsOption\User\DroneStudies;

use Illuminate\Support\Collection;
//use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
//use Illuminate\Validation\Rule;

use App\Models\Common\Buckets;
use App\Models\Common\Frame;
//use App\Models\Common\Page;
//use App\Models\Common\PageRole;
//use App\Models\Common\Uploads;
//use App\Models\Core\FrameConfig;
use App\ModelsOption\User\Dronestudies\Dronestudy;
//use App\Models\User\Cabinets\CabinetContent;

//use App\Enums\UploadMaxSize;
//use App\Enums\CabinetFrameConfig;
//use App\Enums\CabinetSort;

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

    /* コアから呼び出す関数 */

    /**
     * 関数定義（コアから呼び出す）
     */
    public function getPublicFunctions()
    {
        // 標準関数以外で画面などから呼ばれる関数の定義
        $functions = array();
        $functions['get']  = ['index'];
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
        //$role_check_table["upload"] = array('posts.create');
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

    /* 画面アクション関数 */

    /**
     *  データ初期表示関数
     *  コアがページ表示の際に呼び出す関数
     */
    public function index($request, $page_id, $frame_id)
    {
        // バケツ未設定の場合はバケツ空テンプレートを呼び出す
        if (!isset($this->frame) || !$this->frame->bucket_id) {
            // バケツ空テンプレートを呼び出す。
            return $this->view('empty_bucket');
        }

        // バケツデータ取得
        $drone_study = $this->getPluginBucket($this->buckets->id);

        // 表示テンプレートを呼び出す。
        return $this->view('index', [
            'drone_study' => $drone_study,
        ]);
    }

    /**
     * プラグインのバケツ選択表示関数
     */
    public function listBuckets($request, $page_id, $frame_id, $id = null)
    {
        // 表示テンプレートを呼び出す。
        return $this->view('list_buckets', [
            'plugin_buckets' => Dronestudy::orderBy('created_at', 'desc')->paginate(10),
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
        $dronestudy->save();

        return $bucket->id;
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
        $dronestudy_content = $this->fetchDroneStudyContent(null, $dronestudy->id);
        $this->deleteDroneStudyContents(
            $dronestudy_content->id
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
        $this->saveFrameConfigs($request, $frame_id, DronestudyFrameConfig::getMemberKeys());
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
    protected function saveFrameConfigs(\Illuminate\Http\Request $request, int $frame_id, array $frame_config_names)
    {
        foreach ($frame_config_names as $key => $value) {

            if (empty($request->$value)) {
                return;
            }

            FrameConfig::updateOrCreate(
                ['frame_id' => $frame_id, 'name' => $value],
                ['value' => $request->$value]
            );
        }
    }
}
