{{--
 * 表示画面テンプレート。
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category DroneStudyプラグイン
 --}}
<script src="{{url('/')}}/js/blockly/blockly_compressed.js"></script>
<script src="{{url('/')}}/js/blockly/blocks_compressed.js"></script>
<script src="{{url('/')}}/js/blockly/javascript_compressed.js"></script>
<script src="{{url('/')}}/js/blockly/php_compressed.js"></script>
@if (FrameConfig::getConfigValueAndOld($frame_configs, 'dronestudy_language') == 'ja_hiragana')
    <script src="{{url('/')}}/js/blockly/msg/ja_hiragana.js"></script>
    <script src="{{url('/')}}/js/blockly/msg/ja_hiragana_drone.js"></script>
{{--
@elseif (FrameConfig::getConfigValueAndOld($frame_configs, 'dronestudy_language') == 'ja_hiragana_mix')
    <script src="{{url('/')}}/js/blockly/msg/ja_hiragana_mix.js"></script>
    <script src="{{url('/')}}/js/blockly/msg/ja_hiragana_mix_drone.js"></script>
--}}
@else
    <script src="{{url('/')}}/js/blockly/msg/ja.js"></script>
    <script src="{{url('/')}}/js/blockly/msg/ja_drone.js"></script>
@endif
<script src="{{url('/')}}/js/blockly/drone_block.js"></script>

<script type="text/javascript">
    {{-- JavaScript --}}
    // プログラムのXMLを取得する
    function get_xml_text() {
        let xml = Blockly.Xml.workspaceToDom(workspace);
        return Blockly.Xml.domToText(xml);
    }
    // ワークスペースをXMLでエクスポートして保存する。
    function save_xml() {

        // 最大ブロック実行数のチェック
        @if ($dronestudy->max_block_count > 0)
        let drone_methods = Blockly.PHP.workspaceToCode(workspace);
        let drone_methods_trim = drone_methods.trim();
        let split_drone_methods = drone_methods_trim.split(/\n/);
        if (Array.isArray(split_drone_methods)) {
            method_count = split_drone_methods.length;
            // alert(method_count);
            if (method_count > {{$dronestudy->max_block_count}}) {
                alert("ブロックの実行数が " +  method_count + " 個になります。\n最大ブロック実行数を超えています。\n保存できません。\nブロック実行数は{{$dronestudy->max_block_count}}以下にしてください。");
                return false;
            }
        }
        @endif

        // POSTするためのinput タグに設定する。
        let el_xml_text = document.getElementById('xml_text');
        el_xml_text.value = get_xml_text();
        // alert(el_xml_text.value);
        // 保存
        form_dronestudy.action = "{{url('/')}}/redirect/plugin/dronestudies/save/{{$page->id}}/{{$frame_id}}#frame-{{$frame->id}}";
        form_dronestudy.submit();
    }
    // 実行
    function drone_run() {

        let drone_methods = Blockly.PHP.workspaceToCode(workspace);
        //alert(drone_methods);

        let el_drone_methods = document.getElementById('drone_methods');
        el_drone_methods.value = drone_methods;

        let el_xml_text = document.getElementById('xml_text');
        el_xml_text.value = get_xml_text();
        form_dronestudy.action = "{{url('/')}}/redirect/plugin/dronestudies/run/{{$page->id}}/{{$frame_id}}#frame-{{$frame->id}}";
        form_dronestudy.submit();
    }
    // リモートモードへ
    function change_remote() {
        location.href="{{url('/')}}/plugin/dronestudies/remote/{{$page->id}}/{{$frame_id}}#frame-{{$frame->id}}";
    }

</script>

<form action="" method="POST" name="form_dronestudy" class="" onsubmit="return false;">
    {{csrf_field()}}
    <input type="hidden" name="dronestudy_id" value="{{$dronestudy->id}}">
    <input type="hidden" name="post_id" value="{{old("post_id", $post->id)}}">
    <input type="hidden" name="redirect_path" value="{{url('/')}}/plugin/dronestudies/index/{{$page->id}}/{{$frame_id}}#frame-{{$frame->id}}">
    <input type="hidden" name="xml_text" value="">
    <input type="hidden" name="drone_methods" id="drone_methods" value="">
    <input type="hidden" name="mode" value="local">

    @if ($dronestudy->test_mode)
        <span class="badge badge-pill badge-danger mb-3 blink">テストモード</span>
    @endif

    @can("role_article")
        <div class="form-group">
            <div class="card border-info">
                <div class="card-header">現在のモード：ローカル<button type="button" class="btn btn-primary btn-sm ml-3" onclick="javascript:change_remote();">リモートモードを開く</button></div>
            </div>
        </div>
    @endcan

    @if ($dronestudy->max_block_count > 0)
        <div class="alert alert-info">
            <i class="fas fa-exclamation-circle"></i>
            ブロックの実行数は {{$dronestudy->max_block_count}} 個以下になるように作ってください。<br />
            （繰り返し自体は実行数に含みません。繰り返しの中は繰り返す分、実行数になります。）
        </div>
    @endif

    @if ($errors && $errors->has('tello_exception'))
        <div class="card border-danger mb-3">
            <div class="card-header bg-danger text-white">Drone 実行でエラーが発生しました。</div>
            <div class="card-body">
                {{$errors->first('tello_exception')}}
            </div>
        </div>
    @endif

    <!-- フラッシュメッセージ -->
{{--
    @if (session('run_result'))
        <div class="card border-success mb-3">
            <div class="card-header bg-success text-white">実行内容</div>
            <div class="card-body">
                <ul>
                    @foreach(session('run_result') as $run_result)
                    <li>{{$run_result}}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
--}}
    <div class="form-group">
        <label class="control-label">タイトル <label class="badge badge-danger">必須</label></label><br />
        <input type="text" name="title" value="{{old('title', $post->title)}}" class="form-control">
        @if ($errors && $errors->has('title')) <div class="text-danger">{{$errors->first('title')}}</div> @endif
    </div>

    {{-- プログラム（Blockly） --}}
    @include('plugins_option.user.dronestudies.default.block_input')

    @can('posts.create',[[null, 'dronestudies', $buckets]])
    <div class="form-group">
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8 mx-auto">
                <div class="text-center">
                    <button type="button" class="btn btn-success mr-3" onclick="javascript:save_xml();"><i class="far fa-save"></i> 保存</button>
                    @if (FrameConfig::getConfigValueAndOld($frame_configs, 'dronestudy_local_notrun', '0') == '0')
                        <button type="button" class="btn btn-primary mr-3" onclick="javascript:drone_run();"><i class="fas fa-check"></i> 実行</button>
                    @endif
                    <button type="button" class="btn btn-secondary" onclick="location.href='{{URL::to($page->permanent_link)}}#frame-{{$frame->id}}'"><i class="fas fa-times"></i> キャンセル</button>
                </div>
            </div>
            <div class="col-sm-2">
                @if (!empty(old("post_id", $post->id)))
                    <a data-toggle="collapse" href="#collapse{{$post->id}}">
                        <span class="btn btn-danger"><i class="fas fa-trash-alt"></i> <span class="d-none d-sm-inline">削除</span></span>
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endcan
</form>

<div id="collapse{{$post->id}}" class="collapse mt-3">
    <div class="card border-danger mb-3">
        <div class="card-body">
            <span class="text-danger">プログラムを削除します。<br>元に戻すことはできないため、よく確認して実行してください。</span>

            <div class="text-center">
                {{-- 削除ボタン --}}
                <form action="{{url('/')}}/redirect/plugin/dronestudies/delete/{{$page->id}}/{{$frame_id}}/{{$post->id}}#frame-{{$frame->id}}" method="POST">
                    {{csrf_field()}}
                    <button type="submit" class="btn btn-danger" onclick="javascript:return confirm('プログラムを削除します。\nよろしいですか？')"><i class="fas fa-check"></i> 本当に削除する</button>
                </form>
            </div>
        </div>
    </div>
</div>

@can('posts.create',[[null, 'dronestudies', $buckets]])
<div class="card border-info">
    <div class="card-header">保存済みプログラム</div>
    <div class="card-body">
        <ol>
        @foreach($posts as $post_item)
            @if(old("post_id", $post->id) == $post_item->id)
                <li>{{$post_item->title}}［参照中］</li>
            @else
                <li><a href="{{URL::to('/')}}/plugin/dronestudies/index/{{$page->id}}/{{$frame_id}}/{{$post_item->id}}#frame-{{$frame->id}}">{{$post_item->title}}</a></li>
            @endif
        @endforeach
        </ol>
    </div>
</div>
@else
<div class="card border-primary">
    <div class="card-header">プログラムの保存や実行</div>
    <div class="card-body">
        投稿権限のあるユーザでログインすることで、プログラムの保存や実行ができます。
    </div>
</div>
@endcan

@if (old('xml_text', $post->xml_text))
<script>
    // ブロック再構築
    var xml = Blockly.Xml.textToDom('{!!old('xml_text', $post->xml_text)!!}');
    workspace.clear();
    Blockly.Xml.domToWorkspace(xml, workspace);
</script>
@endif
