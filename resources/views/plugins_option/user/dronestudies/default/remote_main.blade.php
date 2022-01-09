{{--
 * リモート画面テンプレート。
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
    // ローカルモードへ
    function change_local() {
        location.href="{{url('/')}}/plugin/dronestudies/index/{{$page->id}}/{{$frame_id}}#frame-{{$frame->id}}";
    }
    // プログラムの読み込み
    function code_load() {
        form_dronestudy.submit();
    }

</script>

<form action="{{url('/')}}/plugin/dronestudies/remote/{{$page->id}}/{{$frame_id}}#frame-{{$frame->id}}" method="POST" name="form_dronestudy" class="">
    {{csrf_field()}}
    <input type="hidden" name="redirect_path" value="{{url('/')}}/plugin/dronestudies/remote/{{$page->id}}/{{$frame_id}}#frame-{{$frame->id}}">
    <input type="hidden" name="remote_user_id" value="{{$remote_user_id}}">
    <input type="hidden" name="xml_text" value="">
    <input type="hidden" name="drone_methods" id="drone_methods" value="">
    <input type="hidden" name="mode" value="remote">

    @if ($dronestudy->test_mode)
        <span class="badge badge-pill badge-danger mb-3 blink">テストモード</span>
    @endif

    @can("role_article")
        <div class="form-group">
            <div class="card border-info">
                <div class="card-header">現在のモード：リモート<button type="button" class="btn btn-primary btn-sm ml-3" onclick="javascript:change_local();">ローカルモードを開く</button></div>
            </div>
        </div>
    @endcan

    <div class="form-group">
        <label class="control-label">ユーザ</label><small class="text-muted pl-2">※ 変更すると画面を読み直します。</small><br />
        <select class="form-control" name="remote_user_id" onchange="javascript:submit(this.form);">
            <option value="">対象ユーザ</option>
            @foreach ($remote_users as $remote_user)
                @if (old('remote_user_id', $remote_user->id) == $remote_user_id)
                    <option value="{{$remote_user->id}}" selected class="text-white bg-primary">{{$remote_user->name}}</option>
                @else
                    <option value="{{$remote_user->id}}">{{$remote_user->name}}</option>
                @endif
            @endforeach
        </select>
    </div>

    @if ($remote_user_id)
        <div class="form-group">
            <label class="control-label">プログラム</label><br />
            <select class="form-control" name="remote_post_id" size="5">
                @foreach ($posts as $post_item)
                    @if ($post && $post_item->id == $remote_post_id)
                        <option value="{{$post_item->id}}" selected class="text-white bg-primary">{{$post_item->title}}</option>
                    @else
                        <option value="{{$post_item->id}}">{{$post_item->title}}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-8 mx-auto">
                    <div class="text-center">
                        <button type="button" class="btn btn-primary mr-3" onclick="javascript:code_load();"><i class="fas fa-check"></i> プログラム呼び出し</button>
                    </div>
                </div>
            </div>
        </div>

        @if ($post)
            {{-- プログラム（Blockly） --}}
            @include('plugins_option.user.dronestudies.default.block_input')

            @can('posts.create',[[null, 'dronestudies', $buckets]])
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-8 mx-auto">
                        <div class="text-center">
                            <button type="button" class="btn btn-primary mr-3" onclick="javascript:drone_run();"><i class="fas fa-check"></i> 実行</button>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
        @endif
    @endif
</form>

@if (!empty($post) && old('xml_text', $post->xml_text))
<script>
    // ブロック再構築
    var xml = Blockly.Xml.textToDom('{!!old('xml_text', $post->xml_text)!!}');
    workspace.clear();
    Blockly.Xml.domToWorkspace(xml, workspace);
</script>
@endif
