{{--
 * バケツ編集画面テンプレート
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category フォトアルバムビューア・プラグイン
--}}
@extends('core.cms_frame_base_setting')

@section("core.cms_frame_edit_tab_$frame->id")
    {{-- プラグイン側のフレームメニュー --}}
    @include('plugins_option.user.photoalbumviewers.photoalbumviewers_frame_edit_tab')
@endsection

@section("plugin_setting_$frame->id")

{{-- 共通エラーメッセージ 呼び出し --}}
@include('plugins.common.errors_form_line')

@if (empty($photoalbumviewer->id) && $action != 'createBuckets')
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-circle"></i> {{ __('messages.empty_bucket_setting', ['plugin_name' => 'フォトアルバムビューア']) }}
    </div>
@else
    <div class="alert alert-info">
        <i class="fas fa-exclamation-circle"></i>
        @if (empty($photoalbumviewer->id))
            新しいフォトアルバムビューア設定を登録します。
        @else
            フォトアルバムビューア設定を変更します。
        @endif
    </div>
    @if (empty($photoalbumviewer->id))
    <form action="{{url('/')}}/redirect/plugin/photoalbumviewers/saveBuckets/{{$page->id}}/{{$frame_id}}#frame-{{$frame->id}}" method="POST">
        <input type="hidden" name="redirect_path" value="{{url('/')}}/plugin/photoalbumviewers/createBuckets/{{$page->id}}/{{$frame_id}}#frame-{{$frame_id}}">
    @else
    <form action="{{url('/')}}/redirect/plugin/photoalbumviewers/saveBuckets/{{$page->id}}/{{$frame_id}}/{{$photoalbumviewer->bucket_id}}#frame-{{$frame->id}}" method="POST">
        <input type="hidden" name="redirect_path" value="{{url('/')}}/plugin/photoalbumviewers/editBuckets/{{$page->id}}/{{$frame_id}}#frame-{{$frame_id}}">
    @endif
        {{ csrf_field() }}

        <div class="form-group row">
            <label class="{{$frame->getSettingLabelClass()}}">バケツ名 <label class="badge badge-danger">必須</label></label>
            <div class="{{$frame->getSettingInputClass()}}">
                <input type="text" name="bucket_name" value="{{old('name', $photoalbumviewer->bucket_name)}}" class="form-control @if ($errors && $errors->has('bucket_name')) border-danger @endif">
                @include('plugins.common.errors_inline', ['name' => 'bucket_name'])
            </div>
        </div>
{{--
        <div class="form-group row">
            <label class="{{$frame->getSettingLabelClass()}}">フォトアルバム <label class="badge badge-danger">必須</label></label>
            <div class="{{$frame->getSettingInputClass()}}">
                <select class="form-control @if ($errors && $errors->has('photoalbum_id')) border-danger @endif" name="photoalbum_id" id="photoalbum_id">
                    @foreach ($photoalbums as $photoalbum)
                    <option value="{{$photoalbum->id}}" @if(old("photoalbum_id", $photoalbumviewer->photoalbum_id) == $photoalbum->id) selected="selected" @endif>
                        {{ $photoalbum->name }}
                    </option>
                    @endforeach
                </select>
                @include('plugins.common.errors_inline', ['name' => 'photoalbum_id'])
            </div>
        </div>
--}}
        <div class="form-group row">
            <label class="{{$frame->getSettingLabelClass()}}">リンク先フレーム</label>
            <div class="{{$frame->getSettingInputClass()}}">
                <select class="form-control @if ($errors && $errors->has('link_frame_id')) border-danger @endif" name="link_frame_id" id="link_frame_id">
                    @foreach ($photoalbum_frames as $photoalbum_frame)
                    <option value="{{$photoalbum_frame->id}}" @if(old("link_frame_id", $photoalbumviewer->link_frame_id) == $photoalbum_frame->id) selected="selected" @endif>
                        {{ $photoalbum_frame->photoalbum_name }} - {{ $photoalbum_frame->page_name }} - {{$photoalbum_frame->frame_title}}
                    </option>
                    @endforeach
                </select>
                <small class="text-muted pl-2">フォトアルバムビューア名 - 配置しているページ名 - フレーム名を表示しています。</small>
                @include('plugins.common.errors_inline', ['name' => 'link_frame_id'])
            </div>
        </div>

        <div class="form-group row">
            <label class="{{$frame->getSettingLabelClass()}}">1行の表示件数 <label class="badge badge-danger">必須</label></label>
            <div class="{{$frame->getSettingInputClass()}}">
                <input type="text" name="col_count" value="{{old('col_count', $photoalbumviewer->col_count)}}" class="form-control @if ($errors && $errors->has('col_count')) border-danger @endif">
                @include('plugins.common.errors_inline', ['name' => 'col_count'])
            </div>
        </div>

        <div class="form-group row">
            <label class="{{$frame->getSettingLabelClass()}}">表示する行数 <label class="badge badge-danger">必須</label></label>
            <div class="{{$frame->getSettingInputClass()}}">
                <input type="text" name="row_count" value="{{old('row_count', $photoalbumviewer->row_count)}}" class="form-control @if ($errors && $errors->has('row_count')) border-danger @endif">
                @include('plugins.common.errors_inline', ['name' => 'row_count'])
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
                        @if (empty($photoalbumviewer->id))
                            登録確定
                        @else
                            変更確定
                        @endif
                        </span>
                    </button>
                </div>

                {{-- 既存サンプルの場合は削除処理のボタンも表示 --}}
                @if (!empty($photoalbumviewer->id))
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
                <span class="text-danger">フォトアルバムビューアを削除します。<br>このフォトアルバムビューアに登録されている設定も削除され、元に戻すことはできないため、よく確認して実行してください。</span>

                <div class="text-center">
                    {{-- 削除ボタン --}}
                    <form action="{{url('/')}}/redirect/plugin/photoalbumviewers/destroyBuckets/{{$page->id}}/{{$frame_id}}/{{$photoalbumviewer->id}}#frame-{{$frame->id}}" method="POST">
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
