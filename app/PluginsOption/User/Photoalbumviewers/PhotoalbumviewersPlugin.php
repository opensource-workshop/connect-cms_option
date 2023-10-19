<?php

namespace App\PluginsOption\User\Photoalbumviewers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

use App\Models\Common\Buckets;
use App\Models\Common\Frame;
use App\Models\Core\Configs;
use App\Models\User\Photoalbums\Photoalbum;
use App\Models\User\Photoalbums\PhotoalbumContent;
use App\ModelsOption\User\Photoalbumviewers\Photoalbumviewer;

use App\PluginsOption\User\UserPluginOptionBase;

/**
 * フォトアルバム・ビューア プラグイン
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category フォトアルバム・ビューア プラグイン
 * @package Controller
 */
class PhotoalbumviewersPlugin extends UserPluginOptionBase
{
    /* オブジェクト変数 */

    // バケツ設定
    private $photoalbumviewer = null;

    /* コアから呼び出す関数 */

    /**
     * 関数定義（コアから呼び出す）
     */
    public function getPublicFunctions()
    {
        // 標準関数以外で画面などから呼ばれる関数の定義
        $functions = array();
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
        $this->photoalbumviewer = Photoalbumviewer::firstOrNew(['bucket_id' => $bucket_id]);
        return $this->photoalbumviewer;
    }

    /* ------------------ */
    /* 画面アクション関数 */
    /* ------------------ */

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
        // バケツがない場合の処理
        if (empty($this->buckets)) {
            return $this->view('no_bucket', []);
        }


        // フォトアルバムビューアの設定
        $photoalbumviewer = $this->getPluginBucket($this->buckets->id);

        // リンク先のページ
        $link_page = Frame::select('frames.*', 'pages.permanent_link')
                          ->join('pages', 'pages.id', '=', 'frames.page_id')
                          ->where('frames.id', $photoalbumviewer->link_frame_id)
                          ->first();

        // フォトアルバムのコンテンツ
        $photoalbum_contents = PhotoalbumContent::select('photoalbum_contents.*', 'parent.name as parent_name')
                                                ->leftJoin('photoalbum_contents as parent', 'parent.id', '=', 'photoalbum_contents.parent_id')
                                                ->where('photoalbum_contents.photoalbum_id', $photoalbumviewer->photoalbum_id)
                                                ->where('photoalbum_contents.mimetype', 'LIKE', 'image/%')
                                              //->whereColumn('width', '>', 'height') // 縦のみ、横のみの実験
                                              //->whereColumn('width', '<', 'height')
                                                ->inRandomOrder()
                                                ->limit($photoalbumviewer->col_count * $photoalbumviewer->row_count)
                                                ->get();

        // リンク先のページ情報
        

        // 表示テンプレートを呼び出す。
        return $this->view('index', [
              'photoalbum_contents' => $photoalbum_contents,
              'link_page'           => $link_page,
              'photoalbum_contents2' => $photoalbum_contents->chunk($photoalbumviewer->col_count),
        ]);
    }

    /* ------------------ */
    /* バケツ関係         */
    /* ------------------ */

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
            'plugin_buckets' => Photoalbumviewer::orderBy('created_at', 'desc')->paginate(10, ["*"], "frame_{$frame_id}_page"),
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

        // フォトアルバムの選択肢
        $photoalbums = Photoalbum::orderBy('id')->get();

        // フォトアルバムのフレーム
        $photoalbum_frames = Frame::select('frames.*', 'pages.page_name', 'photoalbums.name as photoalbum_name')
                       ->join('pages', 'pages.id', '=', 'frames.page_id')
                       ->join('photoalbums', 'photoalbums.bucket_id', '=', 'frames.bucket_id')
                       ->where('frames.plugin_name', 'photoalbums')
                       ->orderBy('frames.page_id')->get();

        // 表示テンプレートを呼び出す。
        return $this->view('bucket', [
            // 表示中のバケツデータ
            'photoalbumviewer' => $this->getPluginBucket($bucket_id),
            'photoalbums' => $photoalbums,
            'photoalbum_frames' => $photoalbum_frames,
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
            'col_count' => [
                'required',
                'integer'
            ],
            'row_count' => [
                'required',
                'integer'
            ],
        ]);
        $validator->setAttributeNames([
            'bucket_name' => 'バケツ名',
            'col_count' => '1行の表示件数',
            'row_count' => '表示する行数',
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

        $bucket_id = $this->savePhotoalbumviewer($request, $frame_id, $bucket_id);

        // 登録後はリダイレクトして編集ページを開く。
        return new Collection(['redirect_path' => url('/') . "/plugin/photoalbumviewers/editBuckets/" . $page_id . "/" . $frame_id . "/" . $bucket_id . "#frame-" . $frame_id]);
    }

    /**
     * バケツを登録する。
     *
     * @param \Illuminate\Http\Request $request リクエスト
     * @param int $frame_id フレームID
     * @param int $bucket_id バケツID
     * @return int バケツID
     */
    private function savePhotoalbumviewer($request, $frame_id, $bucket_id)
    {
        // バケツの取得。なければ登録。
        $bucket = Buckets::updateOrCreate(
            ['id' => $bucket_id],
            ['bucket_name' => $request->bucket_name, 'plugin_name' => 'photoalbumviewers'],
        );

        // フレームにバケツの紐づけ
        $frame = Frame::find($frame_id)->update(['bucket_id' => $bucket->id]);

        // 紐づけるフォトアルバムの取得
        $photoalbum = Frame::select('frames.*', 'photoalbums.id as photoalbum_id')
                       ->join('photoalbums', 'photoalbums.bucket_id', '=', 'frames.bucket_id')
                       ->where('frames.id', $request->link_frame_id)
                       ->first();


        // プラグインバケツを取得(なければ新規オブジェクト)
        // プラグインバケツにデータを設定して保存
        $this->getPluginBucket($bucket->id);
        $this->photoalbumviewer->bucket_id = $bucket->id;
        $this->photoalbumviewer->bucket_name = $request->bucket_name;
        $this->photoalbumviewer->photoalbum_id = $photoalbum->photoalbum_id;
        $this->photoalbumviewer->link_frame_id = $request->link_frame_id;
        $this->photoalbumviewer->col_count = $request->col_count;
        $this->photoalbumviewer->row_count = $request->row_count;
        $this->photoalbumviewer->save();

        return $bucket->id;
    }

    /**
     *  バケツ削除処理
     *
     * @param \Illuminate\Http\Request $request リクエスト
     * @param int $page_id ページID
     * @param int $frame_id フレームID
     */
    public function destroyBuckets($request, $page_id, $frame_id, $Photoalbumviewer_id)
    {
        // プラグインバケツの取得
        $photoalbumviewer = Photoalbumviewer::find($Photoalbumviewer_id);
        if (empty($photoalbumviewer)) {
            return;
        }

        // FrameのバケツIDのクリア
        Frame::where('id', $frame_id)->update(['bucket_id' => null]);

        // バケツ削除
        Buckets::find($photoalbumviewer->bucket_id)->delete();

        // プラグイン・バケツ削除
        $photoalbumviewer->delete();

        return;
    }
}
