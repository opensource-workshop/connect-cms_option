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
@if (FrameConfig::getConfigValueAndOld($frame_configs, 'dronestudy_language', 'ja_hiragana') == 'ja_hiragana')
    <script src="{{url('/')}}/js/blockly/msg/ja_hiragana.js"></script>
    <script src="{{url('/')}}/js/blockly/msg/ja_hiragana_drone.js"></script>
{{--
@elseif (FrameConfig::getConfigValueAndOld($frame_configs, 'dronestudy_language', 'ja_hiragana') == 'ja_hiragana_mix')
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
        var xml = Blockly.Xml.workspaceToDom(workspace);
        return Blockly.Xml.domToText(xml);
    }
    // ワークスペースをXMLでエクスポートして保存する。
    function save_xml() {
        // POSTするためのinput タグに設定する。
        let el_xml_text = document.getElementById('xml_text');
        el_xml_text.value = get_xml_text();
        // 保存
        form_dronestudy.action = "{{url('/')}}/redirect/plugin/dronestudies/save/{{$page->id}}/{{$frame_id}}#frame-{{$frame->id}}";
        form_dronestudy.submit();
    }
    // 実行
    function drone_run() {

        var jscode = Blockly.PHP.workspaceToCode(workspace);
        alert(jscode);


        form_dronestudy.action = "{{url('/')}}/redirect/plugin/dronestudies/run/{{$page->id}}/{{$frame_id}}#frame-{{$frame->id}}";
        form_dronestudy.submit();
    }
    // リモートモードへ
    function change_remote() {
        location.href="{{url('/')}}/plugin/dronestudies/remote/{{$page->id}}/{{$frame_id}}#frame-{{$frame->id}}";
    }

</script>

<form action="" method="POST" name="form_dronestudy" class="">
    {{csrf_field()}}
    <input type="hidden" name="dronestudy_id" value="{{$dronestudy->id}}">
    <input type="hidden" name="post_id" value="{{$dronestudy_post->id}}">
    <input type="hidden" name="redirect_path" value="{{url('/')}}/plugin/dronestudies/index/{{$page->id}}/{{$frame_id}}#frame-{{$frame->id}}">

    @can("role_article")
        <div class="form-group">
            <div class="card border-info">
                <div class="card-header">現在のモード：ローカル<button type="button" class="btn btn-primary btn-sm ml-3" onclick="javascript:change_remote();">リモートモードを開く</button></div>
            </div>
        </div>
    @endcan

    <div class="form-group">
        <label class="control-label">タイトル <label class="badge badge-danger">必須</label></label><br />
        <input type="text" name="title" value="{{old('title', $dronestudy_post->title)}}" class="form-control">
        @if ($errors && $errors->has('title')) <div class="text-danger">{{$errors->first('title')}}</div> @endif
    </div>

    <div class="form-group">
        <label class="control-label">プログラム <label class="badge badge-danger">必須</label></label><br />
        <input type="hidden" name="xml_text" id="xml_text" value="">
        <div class="table-responsive rounded-left">
            <div id="blocklyDiv"  style="height: 500px; width: 100%;"></div>
        </div>
        @if ($errors && $errors->has('xml_text')) <div class="text-danger">{{$errors->first('xml_text')}}</div> @endif

        <xml xmlns="https://developers.google.com/blockly/xml" id="toolbox" style="display: none">
            <block type="drone_takeoff"></block>
            <block type="drone_land"></block>
            <block type="drone_up"></block>
            <block type="drone_down"></block>
            <block type="drone_forward"></block>
            <block type="drone_back"></block>
            <block type="drone_right"></block>
            <block type="drone_left"></block>
            <block type="drone_ccw"></block>
            <block type="drone_cw"></block>
            <block type="drone_flip"></block>
            <block type="drone_loop"></block>
        </xml>

        <script>
            var blocklyArea = document.getElementById('blocklyArea');
            var blocklyDiv = document.getElementById('blocklyDiv');
            var workspace = Blockly.inject(blocklyDiv, {
                media: 'https://unpkg.com/blockly/media/',
                toolbox: document.getElementById('toolbox'),
                zoom: {
                    controls: true,
                    wheel: false,
                    startScale: 0.9,
                    maxScale: 3,
                    minScale: 0.5,
                    scaleSpeed: 1.2,
                    pinch: true
                },
                trashcan: true
            });
        </script>
    </div>

    {{--
    <div class="form-group">
        <label class="control-label">コード <label class="badge badge-danger">必須</label></label><br />
        <textarea id="xml_text" class="form-control" rows="10" name="xml_text" style="font-family:'ＭＳ ゴシック', 'MS Gothic', 'Osaka－等幅', Osaka-mono, monospace;">{!!old('xml_text', $dronestudy_post->xml_text)!!}</textarea>
        @if ($errors && $errors->has('xml_text')) <div class="text-danger">{{$errors->first('xml_text')}}</div> @endif
    </div>
    --}}

    @can('posts.create',[[null, 'dronestudies', $buckets]])
    <div class="form-group">
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8 mx-auto">
                <div class="text-center">
                    <button type="button" class="btn btn-success mr-3" onclick="javascript:save_xml();"><i class="far fa-save"></i> 保存</button>
                    <button type="button" class="btn btn-primary mr-3" onclick="javascript:drone_run();"><i class="fas fa-check"></i> 実行</button>
                    <button type="button" class="btn btn-secondary" onclick="location.href='{{URL::to($page->permanent_link)}}#frame-{{$frame->id}}'"><i class="fas fa-times"></i> キャンセル</button>
                </div>
            </div>
            <div class="col-sm-2">
                @if (!empty($dronestudy_post->id))
                    <a data-toggle="collapse" href="#collapse{{$dronestudy_post->id}}">
                        <span class="btn btn-danger"><i class="fas fa-trash-alt"></i> <span class="hidden-xs">削除</span></span>
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endcan
</form>

<div id="collapse{{$dronestudy_post->id}}" class="collapse mt-3">
    <div class="card border-danger mb-3">
        <div class="card-body">
            <span class="text-danger">プログラムを削除します。<br>元に戻すことはできないため、よく確認して実行してください。</span>

            <div class="text-center">
                {{-- 削除ボタン --}}
                <form action="{{url('/')}}/plugin/dronestudies/deletecode/{{$page->id}}/{{$frame_id}}/{{$dronestudy_post->id}}#frame-{{$frame->id}}" method="POST">
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
        @foreach($dronestudy_posts as $post)
            @if($dronestudy_post->id == $post->id)
                <li>{{$post->title}}［参照中］</li>
            @else
                <li><a href="{{URL::to('/')}}/plugin/dronestudies/index/{{$page->id}}/{{$frame_id}}/{{$post->id}}#frame-{{$frame->id}}">{{$post->title}}</a></li>
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

@if ($dronestudy_post->xml_text)
<script>
    // ブロック再構築
    var xml = Blockly.Xml.textToDom('{!!$dronestudy_post->xml_text!!}');
    workspace.clear();
    Blockly.Xml.domToWorkspace(xml, workspace);
</script>
@endif