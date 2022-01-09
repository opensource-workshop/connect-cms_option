{{--
 * リモート画面テンプレート。
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category DroneStudyプラグイン
 --}}
@extends('core.cms_frame_base')

@section("plugin_contents_$frame->id")

    @if ($errors && $errors->has('tello_exception'))
        <div class="card border-danger mb-3">
            <div class="card-header bg-danger text-white">Drone 実行でエラーが発生しました。</div>
            <div class="card-body">
                {{$errors->first('tello_exception')}}
            </div>
        </div>
    @endif

    @if (isset($frame) && $frame->bucket_id)
        {{-- バケツあり --}}
        @include('plugins_option.user.dronestudies.default.remote_main')
    @else
        {{-- バケツなし --}}
        <div class="card border-danger">
            <div class="card-body">
                <p class="text-center cc_margin_bottom_0">フレームの設定画面から、使用するDroneStudyを選択するか、作成してください。</p>
            </div>
        </div>
    @endif
@endsection
