{{--
 * フレーム表示設定編集画面テンプレート。
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category DroneStudyプラグイン
 --}}
@extends('core.cms_frame_base_setting')

@section("core.cms_frame_edit_tab_$frame->id")
    {{-- プラグイン側のフレームメニュー --}}
    @include('plugins_option.user.dronestudies.dronestudies_frame_edit_tab')
@endsection

@section("plugin_setting_$frame->id")

{{-- 共通エラーメッセージ 呼び出し --}}
@include('plugins.common.errors_form_line')

@if (empty($dronestudy->id) && $action != 'createBuckets')
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-circle"></i> {{ __('messages.empty_bucket_setting', ['plugin_name' => 'DroneStudy']) }}
    </div>
@else
    <div class="alert alert-info">
        <i class="fas fa-exclamation-circle"></i>
        フレームごとの表示設定を変更します。
    </div>

    <form action="{{url('/')}}/redirect/plugin/dronestudies/saveView/{{$page->id}}/{{$frame_id}}/{{$dronestudy->id}}#frame-{{$frame->id}}" method="POST" class="">
        {{ csrf_field() }}
        <input type="hidden" name="redirect_path" value="{{url('/')}}/plugin/dronestudies/editView/{{$page->id}}/{{$frame_id}}/{{$dronestudy->bucket_id}}#frame-{{$frame_id}}">

        {{-- 表示言語 --}}
        <div class="form-group row">
            <label class="{{$frame->getSettingLabelClass(true)}}">表示言語</label>
            <div class="{{$frame->getSettingInputClass(true)}}">
                <div class="custom-control custom-radio custom-control-inline">
                    <input
                        type="radio"
                        value="ja"
                        id="dronestudy_language_ja"
                        name="dronestudy_language"
                        class="custom-control-input"
                        {{ FrameConfig::getConfigValueAndOld($frame_configs, 'dronestudy_language', 'ja') == 'ja' ? 'checked' : '' }}
                    >
                    <label class="custom-control-label" for="dronestudy_language_ja">
                        日本語(漢字)
                    </label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input
                        type="radio"
                        value="ja_hiragana"
                        id="dronestudy_language_ja_hiragana"
                        name="dronestudy_language"
                        class="custom-control-input"
                        {{ FrameConfig::getConfigValueAndOld($frame_configs, 'dronestudy_language', 'ja') == 'ja_hiragana' ? 'checked' : '' }}
                    >
                    <label class="custom-control-label" for="dronestudy_language_ja_hiragana">
                        日本語(ひらがな)
                    </label>
                </div>
{{--
                <div class="custom-control custom-radio custom-control-inline">
                    <input
                        type="radio"
                        value="ja_hiragana_mix"
                        id="dronestudy_language_ja_hiragana_mix"
                        name="dronestudy_language"
                        class="custom-control-input"
                        {{ FrameConfig::getConfigValueAndOld($frame_configs, 'dronestudy_language', 'ja') == 'ja_hiragana_mix' ? 'checked' : '' }}
                    >
                    <label class="custom-control-label" for="dronestudy_language_ja_hiragana_mix">
                        日本語(ひらがなMIX)
                    </label>
                </div>
--}}
            </div>
        </div>

        {{-- ローカルの実行ボタン --}}
        <div class="form-group row">
            <label class="{{$frame->getSettingLabelClass(true)}}">ローカルモードの実行ボタン</label>
            <div class="{{$frame->getSettingInputClass(true)}}">
                <div class="custom-control custom-radio custom-control-inline">
                    <input
                        type="radio"
                        value="1"
                        id="dronestudy_local_notrun_1"
                        name="dronestudy_local_notrun"
                        class="custom-control-input"
                        {{ FrameConfig::getConfigValueAndOld($frame_configs, 'dronestudy_local_notrun', '0') === '1' ? 'checked' : '' }}
                    >
                    <label class="custom-control-label" for="dronestudy_local_notrun_1">
                        表示しない
                    </label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input
                        type="radio"
                        value="0"
                        id="dronestudy_local_notrun_0"
                        name="dronestudy_local_notrun"
                        class="custom-control-input"
                        {{ FrameConfig::getConfigValueAndOld($frame_configs, 'dronestudy_local_notrun', '0') === '0' ? 'checked' : '' }}
                    >
                    <label class="custom-control-label" for="dronestudy_local_notrun_0">
                        表示する
                    </label>
                </div>
            </div>
        </div>

        {{-- Submitボタン --}}
        <div class="text-center">
            <a class="btn btn-secondary mr-2" href="{{URL::to($page->permanent_link)}}#frame-{{$frame->id}}">
                <i class="fas fa-times"></i><span class="{{$frame->getSettingButtonCaptionClass('md')}}"> キャンセル</span>
            </a>
            <button type="submit" class="btn btn-primary form-horizontal">
                <i class="fas fa-check"></i>
                <span class="{{$frame->getSettingButtonCaptionClass()}}">
                    変更確定
                </span>
            </button>
        </div>
    </form>
@endif
@endsection
