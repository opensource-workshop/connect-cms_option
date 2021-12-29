{{--
 * DroneStudy・バケツなし画面テンプレート。
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category DroneStudy・プラグイン
--}}
@extends('core.cms_frame_base')

@section("plugin_contents_$frame->id")

{{-- バケツなし --}}
<div class="card border-danger">
    <div class="card-body">
        <p class="text-center cc_margin_bottom_0">{{ __('messages.empty_bucket', ['plugin_name' => 'DroneStudy']) }}</p>
    </div>
</div>

@endsection
