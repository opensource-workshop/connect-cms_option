{{--
 * バケツ編集画面テンプレート
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @author 牟田口 満 <mutaguchi@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category サンプル・プラグイン
--}}
@extends('core.cms_frame_base_setting')

@section("core.cms_frame_edit_tab_$frame->id")
    {{-- プラグイン側のフレームメニュー --}}
    @include('plugins_option.user.samples.samples_frame_edit_tab')
@endsection

@section("plugin_setting_$frame->id")

{{-- 共通エラーメッセージ 呼び出し --}}
@include('plugins.common.errors_form_line')
{{-- 登録後メッセージ表示 --}}
@include('plugins.common.flash_message_for_frame')

@if (empty($sample->id) && $action != 'createBuckets')
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-circle"></i> {{ __('messages.empty_bucket_setting', ['plugin_name' => 'サンプル']) }}
    </div>
@else
    <div class="alert alert-info">
        <i class="fas fa-exclamation-circle"></i>
        @if (empty($sample->id))
            新しいサンプル設定を登録します。
        @else
            サンプル設定を変更します。
        @endif
    </div>

    @if (empty($sample->id))
    <form action="{{url('/')}}/redirect/plugin/samples/saveBuckets/{{$page->id}}/{{$frame_id}}#frame-{{$frame->id}}" method="POST">
        <input type="hidden" name="redirect_path" value="{{url('/')}}/plugin/samples/createBuckets/{{$page->id}}/{{$frame_id}}#frame-{{$frame_id}}">
    @else
    <form action="{{url('/')}}/redirect/plugin/samples/saveBuckets/{{$page->id}}/{{$frame_id}}/{{$sample->bucket_id}}#frame-{{$frame->id}}" method="POST">
        <input type="hidden" name="redirect_path" value="{{url('/')}}/plugin/samples/editBuckets/{{$page->id}}/{{$frame_id}}#frame-{{$frame_id}}">
    @endif
        {{ csrf_field() }}

        <div class="form-group row">
            <label class="{{$frame->getSettingLabelClass()}}">バケツ名 <label class="badge badge-danger">必須</label></label>
            <div class="{{$frame->getSettingInputClass()}}">
                <input type="text" name="bucket_name" value="{{old('name', $sample->bucket_name)}}" class="form-control @if ($errors && $errors->has('bucket_name')) border-danger @endif">
                @include('plugins.common.errors_inline', ['name' => 'bucket_name'])
            </div>
        </div>

        {{-- Submitボタン --}}
        <div class="form-group text-center">
            <div class="row">
                <div class="col-3"></div>
                <div class="col-6">
                    <button type="button" class="btn btn-secondary mr-2" onclick="location.href='{{URL::to($page->permanent_link)}}'">
                        <i class="fas fa-times"></i><span class="{{$frame->getSettingButtonCaptionClass('md')}}"> キャンセル</span>
                    </button>
                    <button type="submit" class="btn btn-primary form-horizontal"><i class="fas fa-check"></i>
                        <span class="{{$frame->getSettingButtonCaptionClass()}}">
                        @if (empty($sample->id))
                            登録確定
                        @else
                            変更確定
                        @endif
                        </span>
                    </button>
                </div>

                {{-- 既存サンプルの場合は削除処理のボタンも表示 --}}
                @if (!empty($sample->id))
                <div class="col-3 text-right">
                    <a data-toggle="collapse" href="#collapse{{$frame->id}}">
                        <span class="btn btn-danger"><i class="fas fa-trash-alt"></i><span class="{{$frame->getSettingButtonCaptionClass()}}"> 削除</span></span>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </form>

    <div id="collapse{{$frame->id}}" class="collapse">
        <div class="card border-danger">
            <div class="card-body">
                <span class="text-danger">サンプルを削除します。<br>このサンプルに登録されている記事も削除され、元に戻すことはできないため、よく確認して実行してください。</span>

                <div class="text-center">
                    {{-- 削除ボタン --}}
                    <form action="{{url('/')}}/redirect/plugin/samples/destroyBuckets/{{$page->id}}/{{$frame_id}}/{{$sample->id}}#frame-{{$frame->id}}" method="POST">
                        {{csrf_field()}}
                        <button type="submit" class="btn btn-danger" onclick="javascript:return confirm('データを削除します。\nよろしいですか？')">
                            <i class="fas fa-check"></i> 本当に削除する
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
