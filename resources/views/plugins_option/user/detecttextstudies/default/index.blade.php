{{--
 * 表示画面テンプレート（デフォルト）
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category DetectTextStudy プラグイン
 --}}
@extends('core.cms_frame_base')

@section("plugin_contents_$frame->id")

    <div class="input-group">
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="photo_{{$frame->id}}" name="photo_{{$frame->id}}" aria-describedby="photo_{{$frame->id}}">
            <label class="custom-file-label" for="photo_{{$frame->id}}">画像を選択してください。</label>
        </div>
    </div>

    <div class="mt-3">
        <input type="submit" class="btn btn-primary" value="アップロード＆判定" id="faceSubmit_{{$frame->id}}">
    </div>

@endsection
