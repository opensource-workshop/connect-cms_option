{{--
 * 表示画面テンプレート（デフォルト）
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category FaceStudy プラグイン
 --}}
@extends('core.cms_frame_base')

@section("plugin_contents_$frame->id")

    <div class="input-group">
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="photo_{{$frame->id}}" name="photo_{{$frame->id}}" aria-describedby="photo_{{$frame->id}}">
            <label class="custom-file-label" for="photo_{{$frame->id}}">顔画像を選択してください。</label>
        </div>
    </div>

    <div>
        <label for="textInput" class="form-label mt-3">処理</label><br>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="method_mosaic_{{$frame->id}}" name="method_{{$frame->id}}" class="custom-control-input" value="rectangle" checked>
            <label class="custom-control-label" for="method_mosaic_{{$frame->id}}" id="label_method_mosaic_{{$frame->id}}">顔認識</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="method_eye_rectangle_{{$frame->id}}" name="method_{{$frame->id}}" class="custom-control-input" value="eye_rectangle">
            <label class="custom-control-label" for="method_eye_rectangle_{{$frame->id}}" id="label_method_eye_rectangle_{{$frame->id}}">目認識</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="method_smile_{{$frame->id}}" name="method_{{$frame->id}}" class="custom-control-input" value="smile">
            <label class="custom-control-label" for="method_smile_{{$frame->id}}" id="label_method_smile_{{$frame->id}}">笑顔認識</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="method_animeface_{{$frame->id}}" name="method_{{$frame->id}}" class="custom-control-input" value="animeface">
            <label class="custom-control-label" for="method_animeface_{{$frame->id}}" id="label_method_animeface_{{$frame->id}}">アイドルアニメの顔認識</label>
        </div>
    </div>

    <div class="mt-3">
        <input type="submit" class="btn btn-primary" value="アップロード＆判定" id="faceSubmit_{{$frame->id}}">
    </div>

    <div class="mt-3">
        <img id="face_{{$frame->id}}" class="img-fluid" src=""></audio>
    </div>

    <script>

    $('.custom-file-input').on('change',function(){
        $(this).next('.custom-file-label').html($(this)[0].files[0].name);
    })

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


    // リクエスト（要求）を送信
    $('#faceSubmit_{{$frame->id}}').on('click', function () {

        var tokens = document.getElementsByName("csrf-token");
        var fd = new FormData();
        var $upfile = $('input[name="photo_{{$frame->id}}"]');
        fd.append("photo", $upfile.prop('files')[0]);
        fd.append("_token", tokens[0].content);
        fd.append("image_size", 800);
        fd.append("method", $('input:radio[name="method_{{$frame->id}}"]:checked').val());

        $.ajax({
            url:'{{url('/')}}/redirect/plugin/facestudies/face/{{$page->id}}/{{$frame_id}}',
            type:'post',
            data: fd,
            processData: false,
            contentType: false,
            cache: false,
        }).done(function (data) {
            // 成功時の処理
            $('#face_{{$frame->id}}').attr("src", 'data:image/png;base64,' + data);

        }).fail(function() {
           // 失敗時の処理
        });
    });
    </script>

@endsection
