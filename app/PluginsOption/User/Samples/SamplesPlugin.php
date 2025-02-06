<?php

namespace App\PluginsOption\User\Samples;

use App\Models\Common\Buckets;
use App\Models\Common\Frame;
use App\ModelsOption\User\Samples\Sample;
use App\ModelsOption\User\Samples\SamplePost;
use App\PluginsOption\User\UserPluginOptionBase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

/**
 * サンプル・プラグイン
 *
 * DB 定義コマンド
 * php artisan migrate --path=database/migrations_option
 * php artisan migrate:rollback --path=database/migrations_option
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category サンプル・プラグイン
 * @package Controller
 */
class SamplesPlugin extends UserPluginOptionBase
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
        $functions['get']  = ['index'];
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

        // 指定された記事を取得
        $this->post = SamplePost::whereExists(function ($query) {
            $query->select(\DB::raw(1))
                  ->from('samples')
                  ->whereRaw('sample_posts.sample_id = samples.id')
                  ->where('samples.bucket_id', $this->frame->bucket_id);
        })
        ->firstOrNew(['id' => $id]);

        return $this->post;
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
        // バケツ未設定の場合はバケツ空テンプレートを呼び出す
        if (!isset($this->frame) || !$this->frame->bucket_id) {
            // バケツ空テンプレートを呼び出す。
            return $this->view('empty_bucket');
        }

        // バケツデータ取得
        $sample = $this->getPluginBucket($this->buckets->id);

        // 記事一覧
        $posts = SamplePost::where('sample_id', $sample->id)
            ->orderBy('id', 'desc')
            ->paginate(4, ["*"], "frame_{$frame_id}_page");

        // 表示テンプレートを呼び出す。
        return $this->view('index', [
            'sample' => $sample,
            'posts' => $posts,
        ]);
    }

    /**
     * 記事編集画面
     *
     * @method_title 記事編集
     * @method_desc 記事を編集します。
     * @method_detail 記事の削除もこの画面から行います。
     */
    public function edit($request, $page_id, $frame_id, $post_id = null)
    {
        // 記事取得
        $post = $this->getPost($post_id);

        // 編集時
        if ($post_id) {
            // 記事を取得できなかったら404
            if (empty($post->sample_id)) {
                return $this->viewError("404_inframe", null, '詳細取得NG');
            }
        }

        // 編集画面を呼び出す。
        return $this->view('edit', [
            'post' => $post,
        ]);
    }

    /**
     * POST 登録/更新のバリデーターを取得する。
     *
     * @param \Illuminate\Http\Request $request リクエスト
     * @return \Illuminate\Contracts\Validation\Validator バリデーター
     */
    private function getPostValidator($request)
    {
        // 項目のエラーチェック
        $validator = Validator::make($request->all(), [
            'title' => [
                'required',
                'max:255'
            ],
        ]);
        $validator->setAttributeNames([
            'title' => 'タイトル',
        ]);
        return $validator;
    }

    /**
     * POST コンテンツを登録する。
     *
     * @param \Illuminate\Http\Request $request リクエスト
     * @param int $frame_id フレームID
     * @param int $bucket_id バケツID
     * @return int バケツID
     */
    public function save($request, $page_id, $frame_id, $post_id = null)
    {
        // 入力エラーがあった場合は入力画面に戻る。
        $validator = $this->getPostValidator($request);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // 記事の保存
        $sample = $this->getPluginBucket($this->buckets->id);
        $post = SamplePost::updateOrCreate(
            ['id' => $post_id],
            [
                'sample_id' => $sample->id,
                'title' => $request->title,
                'content' => $request->content,
            ],
        );
        // 登録後はリダイレクトして編集ページを開く。
        return new Collection(['redirect_path' => url('/') . "/plugin/samples/index/" . $page_id . "/" . $frame_id . "/" . $post->id . "#frame-" . $frame_id]);
    }

    /**
     * 詳細表示
     *
     * @method_title 記事表示
     * @method_desc 記事を1件、表示します。
     * @method_detail 記事のURLを特定したい場合にはこの画面のURLを使用します。
     */
    public function show($request, $page_id, $frame_id, $post_id)
    {
        // 記事取得
        $post = $this->getPost($post_id);

        // 記事を取得できなかったら404
        if (empty($post->sample_id)) {
            return $this->viewError("404_inframe", null, '詳細取得NG');
        }

        // 編集画面を呼び出す。
        return $this->view('show', [
            'post' => $post,
        ]);
    }

    /**
     * 削除処理
     */
    public function delete($request, $page_id, $frame_id, $post_id)
    {
        // データを削除
        SamplePost::where('id', $post_id)->delete();

        // 削除後はリダイレクトして一覧ページを開く。
        return new Collection(['redirect_path' => url('/') . "/plugin/samples/index/" . $page_id . "/" . $frame_id . "#frame-" . $frame_id]);
    }

    /**
     * プラグインのバケツ選択表示関数
     *
     * @method_title 選択
     * @method_desc このフレームに表示する掲示板を選択します。
     * @method_detail
     */
    public function listBuckets($request, $page_id, $frame_id, $id = null)
    {
        // 表示テンプレートを呼び出す。
        return $this->view('list_buckets', [
            'plugin_buckets' => Sample::orderBy('created_at', 'desc')->paginate(10, ["*"], "frame_{$frame_id}_page"),
        ]);
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
    }

    /**
     * バケツ新規作成画面
     *
     * @method_title 作成
     * @method_desc 掲示板を新しく作成します。
     * @method_detail 掲示板名やいいねボタンの表示を入力して掲示板を作成できます。
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
            'sample' => $this->getPluginBucket($bucket_id),
        ]);
    }

    /**
     * バケツ登録/更新のバリデーターを取得する。
     *
     * @param \Illuminate\Http\Request $request リクエスト
     * @return \Illuminate\Contracts\Validation\Validator バリデーター
     */
    private function getBucketValidator($request)
    {
        // 項目のエラーチェック
        $validator = Validator::make($request->all(), [
            'bucket_name' => [
                'required',
                'max:255'
            ],
        ]);
        $validator->setAttributeNames([
            'bucket_name' => 'バケツ名',
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

        $bucket_id = $this->saveSample($request, $frame_id, $bucket_id);

        // 登録後はリダイレクトして編集ページを開く。
        return new Collection(['redirect_path' => url('/') . "/plugin/samples/editBuckets/" . $page_id . "/" . $frame_id . "/" . $bucket_id . "#frame-" . $frame_id]);
    }

    /**
     * バケツを登録する。
     *
     * @param \Illuminate\Http\Request $request リクエスト
     * @param int $frame_id フレームID
     * @param int $bucket_id バケツID
     * @return int バケツID
     */
    private function saveSample($request, $frame_id, $bucket_id)
    {
        // バケツの取得。なければ登録。
        $bucket = Buckets::updateOrCreate(
            ['id' => $bucket_id],
            ['bucket_name' => $request->bucket_name, 'plugin_name' => 'samples'],
        );

        // フレームにバケツの紐づけ
        $frame = Frame::find($frame_id)->update(['bucket_id' => $bucket->id]);

        // プラグインバケツを取得(なければ新規オブジェクト)
        // プラグインバケツにデータを設定して保存
        $sample = $this->getPluginBucket($bucket->id);
        $sample->bucket_name = $request->bucket_name;
        $sample->save();

        return $bucket->id;
    }

    /**
     *  バケツ削除処理
     *
     * @param \Illuminate\Http\Request $request リクエスト
     * @param int $page_id ページID
     * @param int $frame_id フレームID
     */
    public function destroyBuckets($request, $page_id, $frame_id, $sample_id)
    {
        // プラグインバケツの取得
        $sample = Sample::find($sample_id);
        if (empty($sample)) {
            return;
        }

        // FrameのバケツIDのクリア
        Frame::where('id', $frame_id)->update(['bucket_id' => null]);

        // バケツ削除
        Buckets::find($sample->bucket_id)->delete();

        // コンテンツ削除
        SamplePost::where('sample_id', $sample->id)->delete();

        // プラグイン・バケツ削除
        $sample->delete();

        return;
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
}
