{{--
 * ブロック入力テンプレート。
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category DroneStudyプラグイン
 --}}
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
        @if ($dronestudy->use_stream)
            <block type="drone_streamon"></block>
            <block type="drone_streamoff"></block>
        @endif
{{--
        <block type="controls_repeat_ext"></block>
        <block type="math_number">
            <field name="NUM">3</field>
        </block>
        <block type="text"></block>
        <block type="text_print"></block>
        <block type="drone_test"></block>
--}}
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
