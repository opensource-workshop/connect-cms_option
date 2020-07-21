{{--
 * 設定画面テンプレート。
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category covid19japan.com データ活用プラグイン
 --}}
@extends('core.cms_frame_base_setting')

@section("core.cms_frame_edit_tab_$frame->id")
    {{-- プラグイン側のフレームメニュー --}}
    @include('plugins_option.user.covid19japan.covid19japan_frame_edit_tab')
@endsection

@section("plugin_setting_$frame->id")

<form action="{{url('/')}}/plugin/covid19japan/pullData/{{$page->id}}/{{$frame_id}}#frame-{{$frame_id}}" method="POST">
    {{ csrf_field() }}

    {{-- Submitボタン --}}
    <div class="form-group text-center">
        <div class="row">
            <div class="col-3"></div>
            <div class="col-6">
                <button type="button" class="btn btn-secondary mr-2" onclick="location.href='{{URL::to($page->permanent_link)}}'">
                    <i class="fas fa-times"></i><span class="{{$frame->getSettingButtonCaptionClass('md')}}"> キャンセル</span>
                </button>
                <button type="submit" class="btn btn-primary form-horizontal" onclick="return confirm('データを取り込みます。\nよろしいですか？');"><i class="fas fa-check"></i> 
                    <span class="{{$frame->getSettingButtonCaptionClass()}}">
                        取り込み
                    </span>
                </button>
            </div>
        </div>
    </div>
</form>

@endsection
