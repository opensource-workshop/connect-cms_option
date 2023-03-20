{{--
 * 表示画面テンプレート（体験）
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category SpeechStudy プラグイン
 --}}
@extends('core.cms_frame_base')

@section("plugin_contents_$frame->id")

    <div>
        <label for="textInput" class="form-label">読ませたい文字を入力してください。（漢字もOK）</label>
        <textarea class="form-control" id="textInput_{{$frame->id}}" style="font-size: 3em;">テスト</textarea>
    </div>

    <div>
        <label for="textInput" class="form-label mt-3">声</label><br />
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="VoiceId2_{{$frame->id}}" name="VoiceId_{{$frame->id}}" class="custom-control-input" value="Takumi" checked="checked">
            <label class="custom-control-label" for="VoiceId2_{{$frame->id}}">Takumi</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="VoiceId1_{{$frame->id}}" name="VoiceId_{{$frame->id}}" class="custom-control-input" value="Kazuha">
            <label class="custom-control-label" for="VoiceId1_{{$frame->id}}">Kazuha</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="VoiceId3_{{$frame->id}}" name="VoiceId_{{$frame->id}}" class="custom-control-input" value="Tomoko">
            <label class="custom-control-label" for="VoiceId3_{{$frame->id}}">Tomoko</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="VoiceId4_{{$frame->id}}" name="VoiceId_{{$frame->id}}" class="custom-control-input" value="Mizuki">
            <label class="custom-control-label" for="VoiceId4_{{$frame->id}}">Mizuki</label>
        </div>
    </div>

    <div>
        <label for="textInput" class="form-label mt-3">速度</label><br />
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="rate1_{{$frame->id}}" name="rate_{{$frame->id}}" class="custom-control-input" value="x-slow">
            <label class="custom-control-label" for="rate1_{{$frame->id}}">とてもゆっくり</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="rate2_{{$frame->id}}" name="rate_{{$frame->id}}" class="custom-control-input" value="slow">
            <label class="custom-control-label" for="rate2_{{$frame->id}}">ゆっくり</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="rate3_{{$frame->id}}" name="rate_{{$frame->id}}" class="custom-control-input" value="medium" checked="checked">
            <label class="custom-control-label" for="rate3_{{$frame->id}}">標準</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="rate4_{{$frame->id}}" name="rate_{{$frame->id}}" class="custom-control-input" value="fast">
            <label class="custom-control-label" for="rate4_{{$frame->id}}">早口</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="rate5_{{$frame->id}}" name="rate_{{$frame->id}}" class="custom-control-input" value="x-fast">
            <label class="custom-control-label" for="rate5_{{$frame->id}}">とても早口</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="rate6_{{$frame->id}}" name="rate_{{$frame->id}}" class="custom-control-input" value="other">
            <label class="custom-control-label" for="rate6_{{$frame->id}}">以下で指定</label>
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <select class="form-control" id="rate_other_{{$frame->id}}" name="rate_other_{{$frame->id}}">
                <option value="100%">指定速度</option>
                @for ($i = 20; $i <= 200; $i = $i + 5)
                    <option value="{{$i}}%">{{$i}}%</option>
                @endfor
            </select>
        </div>
    </div>

    <div class="mt-3">
        <input type="submit" class="btn btn-primary btn-lg" value="ＡＩ読み上げ実行！" onClick="speechSubmit_{{$frame->id}}();">
        <audio id="speech_{{$frame->id}}"><source id="speechSource_{{$frame->id}}" src="" type="audio/mp3"></audio>
    </div>

    <script>
    // HTMLフォームの形式にデータを変換する
    function EncodeHTMLForm_{{$frame->id}}(data)
    {
        var params = [];
        for(var name in data)
        {
            var value = data[name];
            var param = encodeURIComponent(name) + '=' + encodeURIComponent(value);
            params.push(param);
        }
        return params.join('&').replace(/%20/g, '+');
    }

    function speechSubmit_{{$frame->id}}() {
        // (1) XMLHttpRequestオブジェクトを作成
        const xhr = new XMLHttpRequest();

        // (2) 取得するファイルの設定
        xhr.open('post', "{{url('/')}}/redirect/plugin/speechstudies/speech/{{$page->id}}/{{$frame_id}}");

        // サーバに対して解析方法を指定する
        xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );

        xhr.responseType = 'blob';

        // (3) リクエスト（要求）を送信
        var tokens = document.getElementsByName("csrf-token");
        var textInput = document.getElementById("textInput_{{$frame->id}}");
        var voiceId = $('input:radio[name="VoiceId_{{$frame->id}}"]:checked').val();
        var rate = $('input:radio[name="rate_{{$frame->id}}"]:checked').val();
        if (rate == 'other') {
            rate = $("#rate_other_{{$frame->id}}").val();
        }

        var data = { text: textInput.value, _token: tokens[0].content, voiceId: voiceId, rate: rate};
        xhr.send(EncodeHTMLForm_{{$frame->id}}(data));

        xhr.onreadystatechange = function() {

            // (4) 通信が正常に完了したか確認
            if( xhr.readyState === 4 && xhr.status === 200) {

                // (5) 音声ファイルを再生
                const speech = document.getElementById("speech_{{$frame->id}}");
                const speechSource = document.getElementById("speechSource_{{$frame->id}}");
                speechSource.src = URL.createObjectURL(this.response);

                speech.load();
                speech.play();
            }
        };
    }
    </script>

@endsection
