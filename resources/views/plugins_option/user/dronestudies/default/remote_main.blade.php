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
<script src="{{url('/')}}/js/blockly/msg/ja.js"></script>
@if (FrameConfig::getConfigValueAndOld($frame_configs, 'dronestudy_language', 'ja_hiragana') == 'ja_hiragana')
    <script src="{{url('/')}}/js/blockly/drone_block_hiragana.js"></script>
@elseif (FrameConfig::getConfigValueAndOld($frame_configs, 'dronestudy_language', 'ja_hiragana') == 'ja_hiragana_mix')
    <script src="{{url('/')}}/js/blockly/drone_block_hiragana_mix.js"></script>
@else
    <script src="{{url('/')}}/js/blockly/drone_block.js"></script>
@endif

<script type="text/javascript">
    {{-- JavaScript --}}
    function save_xml() {
        // ワークスペースをXMLでエクスポートして保存する。
        var xml = Blockly.Xml.workspaceToDom(workspace);
        var myBlockXml = Blockly.Xml.domToText(xml);
        let el_xml_text = document.getElementById('xml_text');
        //alert(myBlockXml);
        //alert(el_xml_text.value);
        el_xml_text.value = myBlockXml;


        form_dronestudy.submit();
    }
    function change_local() {
        location.href="{{url('/')}}/plugin/dronestudies/index/{{$page->id}}/{{$frame_id}}#frame-{{$frame->id}}";
    }

</script>

<form action="{{url('/')}}/redirect/plugin/dronestudies/save/{{$page->id}}/{{$frame_id}}#frame-{{$frame->id}}" method="POST" name="form_dronestudy" class="">
    {{csrf_field()}}
{{--
    <input type="hidden" name="dronestudy_id" value="{{$dronestudy->id}}">
    <input type="hidden" name="post_id" value="{{$dronestudy_post->id}}">
--}}

    @can("role_article")
        <div class="form-group">
            <div class="card border-info">
                <div class="card-header">現在のモード：リモート<button type="button" class="btn btn-primary btn-sm ml-3" onclick="javascript:change_local();">ローカルモードを開く</button></div>
            </div>
        </div>
    @endcan

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
</form>

{{--
@if ($dronestudy_post->xml_text)
<script>
    // ブロック再構築
    var xml = Blockly.Xml.textToDom('{!!$dronestudy_post->xml_text!!}');
    workspace.clear();
    Blockly.Xml.domToWorkspace(xml, workspace);
</script>
@endif
--}}
