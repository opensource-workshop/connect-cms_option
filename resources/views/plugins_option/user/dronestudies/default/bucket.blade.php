{{--
 * DroneStudy・バケツ編集画面テンプレート。
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category DroneStudy・プラグイン
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
        @if (empty($dronestudy->id))
            新しいDroneStudy設定を登録します。
        @else
            DroneStudy設定を変更します。
        @endif
    </div>

    @if (empty($dronestudy->id))
    <form action="{{url('/')}}/redirect/plugin/dronestudies/saveBuckets/{{$page->id}}/{{$frame_id}}#frame-{{$frame->id}}" method="POST">
        <input type="hidden" name="redirect_path" value="{{url('/')}}/plugin/dronestudies/createBuckets/{{$page->id}}/{{$frame_id}}#frame-{{$frame_id}}">
    @else
    <form action="{{url('/')}}/redirect/plugin/dronestudies/saveBuckets/{{$page->id}}/{{$frame_id}}/{{$dronestudy->bucket_id}}#frame-{{$frame->id}}" method="POST">
        <input type="hidden" name="redirect_path" value="{{url('/')}}/plugin/dronestudies/editBuckets/{{$page->id}}/{{$frame_id}}#frame-{{$frame_id}}">
    @endif
        {{ csrf_field() }}

        @if ($dronestudy->bucket_id)
            <div class="form-group row">
                <label class="{{$frame->getSettingLabelClass()}}">ローカルDroneStudy-ID</label>
                <div class="{{$frame->getSettingInputClass(true)}}">
                    {{$dronestudy->id}}
                    <small class="text-muted pl-2">（リモート設置する際にリモートDroneStudy-IDに登録する値）</small>
                </div>
            </div>
        @endif

        <div class="form-group row">
            <label class="{{$frame->getSettingLabelClass()}}">DroneStudy名 <label class="badge badge-danger">必須</label></label>
            <div class="{{$frame->getSettingInputClass()}}">
                <input type="text" name="name" value="{{old('name', $dronestudy->name)}}" class="form-control @if ($errors && $errors->has('name')) border-danger @endif">
                @include('plugins.common.errors_inline', ['name' => 'name'])
            </div>
        </div>

        <div class="form-group row">
            <label class="{{$frame->getSettingLabelClass()}}">命令間隔（秒）</label>
            <div class="{{$frame->getSettingInputClass()}}">
                <input type="text" name="command_interval" value="{{old('command_interval', $dronestudy->command_interval)}}" class="form-control @if ($errors && $errors->has('command_interval')) border-danger @endif">
                @include('plugins.common.errors_inline', ['name' => 'command_interval'])
                <small class="text-muted pl-2">※ 通常は5～7で設定します。</small>
            </div>
        </div>

        {{-- 映像使用 --}}
        <div class="form-group row mb-0">
            <label class="{{$frame->getSettingLabelClass()}} py-0">映像使用</label>
            <div class="{{$frame->getSettingInputClass(true)}}">
                <div class="custom-control custom-radio custom-control-inline">
                    <input
                        type="radio"
                        value="0"
                        id="use_stream_0"
                        name="use_stream"
                        class="custom-control-input"
                        @if (empty($dronestudy->use_stream)) checked @endif
                    >
                    <label class="custom-control-label" for="use_stream_0" id="label_use_stream_0">
                        使用しない
                    </label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input
                        type="radio"
                        value="1"
                        id="use_stream_1"
                        name="use_stream"
                        class="custom-control-input"
                        @if ($dronestudy->use_stream == 1) checked @endif
                    >
                    <label class="custom-control-label" for="use_stream_1" id="label_use_stream_1">
                        使用する
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <label class="{{$frame->getSettingLabelClass(true)}} py-0 my-0"></label>
            <div class="{{$frame->getSettingInputClass(true)}}">
                <small class="text-muted pl-2">※ FFmpeg の準備及び実行が必要です。（詳しくはマニュアルを参照してください。）</small>
            </div>
        </div>

        <div class="form-group row">
            <label class="{{$frame->getSettingLabelClass()}}">最大ブロック実行数</label>
            <div class="{{$frame->getSettingInputClass()}}">
                <input type="text" name="max_block_count" value="{{old('max_block_count', $dronestudy->max_block_count, 0)}}" class="form-control @if ($errors && $errors->has('max_block_count')) border-danger @endif">
                @include('plugins.common.errors_inline', ['name' => 'max_block_count'])
                <small class="text-muted pl-2">※ 0の場合は、制限しません。</small>
            </div>
        </div>

        <div class="form-group row">
            <label class="{{$frame->getSettingLabelClass()}}">リモートサイトURL</label>
            <div class="{{$frame->getSettingInputClass()}}">
                <input type="text" name="remote_url" value="{{old('remote_url', $dronestudy->remote_url)}}" class="form-control @if ($errors && $errors->has('remote_url')) border-danger @endif">
                @include('plugins.common.errors_inline', ['name' => 'remote_url'])
                <small class="text-muted pl-2">※ リモート側のConnect-CMSのURL。最後のスラッシュは不要。例：https://example.com</small>
            </div>
        </div>

        <div class="form-group row">
            <label class="{{$frame->getSettingLabelClass()}}">リモートDroneStudy-ID</label>
            <div class="{{$frame->getSettingInputClass()}}">
                <input type="text" name="remote_id" value="{{old('remote_id', $dronestudy->remote_id)}}" class="form-control @if ($errors && $errors->has('remote_id')) border-danger @endif">
                @include('plugins.common.errors_inline', ['name' => 'remote_id'])
                <small class="text-muted pl-2">※ リモート側のConnect-CMS、DroneStudy の設定変更で表示されるローカルバケツID</small>
            </div>
        </div>

        <div class="form-group row">
            <label class="{{$frame->getSettingLabelClass()}}">リモート秘密コード</label>
            <div class="{{$frame->getSettingInputClass()}}">
                <input type="text" name="secret_code" value="{{old('secret_code', $dronestudy->secret_code)}}" class="form-control @if ($errors && $errors->has('secret_code')) border-danger @endif">
                @include('plugins.common.errors_inline', ['name' => 'secret_code'])
                <small class="text-muted pl-2">※ リモート側のConnect-CMS 管理画面のAPI管理で設定する秘密コード</small>
            </div>
        </div>

        {{-- テストモード --}}
        <div class="form-group row mb-0">
            <label class="{{$frame->getSettingLabelClass()}} py-0">テストモード</label>
            <div class="{{$frame->getSettingInputClass(true)}}">
                <div class="custom-control custom-radio custom-control-inline">
                    <input
                        type="radio"
                        value="0"
                        id="test_mode_0"
                        name="test_mode"
                        class="custom-control-input"
                        @if (empty($dronestudy->test_mode)) checked @endif
                    >
                    <label class="custom-control-label" for="test_mode_0">
                        OFF
                    </label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input
                        type="radio"
                        value="1"
                        id="test_mode_1"
                        name="test_mode"
                        class="custom-control-input"
                        @if ($dronestudy->test_mode == 1) checked @endif
                    >
                    <label class="custom-control-label" for="test_mode_1">
                        ON
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <label class="{{$frame->getSettingLabelClass(true)}} py-0 my-0"></label>
            <div class="{{$frame->getSettingInputClass(true)}}">
                <small class="text-muted pl-2">※ テストモードONの場合は、Drone に接続しません。</small>
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
                        @if (empty($dronestudy->id))
                            登録確定
                        @else
                            変更確定
                        @endif
                        </span>
                    </button>
                </div>

                {{-- 既存DroneStudyの場合は削除処理のボタンも表示 --}}
                @if (!empty($dronestudy->id))
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
                <span class="text-danger">DroneStudyを削除します。<br>このDroneStudyに登録されているプログラムファイルも削除され、元に戻すことはできないため、よく確認して実行してください。</span>

                <div class="text-center">
                    {{-- 削除ボタン --}}
                    <form action="{{url('/')}}/redirect/plugin/dronestudies/destroyBuckets/{{$page->id}}/{{$frame_id}}/{{$dronestudy->id}}#frame-{{$frame->id}}" method="POST">
                        {{csrf_field()}}
                        <button type="submit" class="btn btn-danger" onclick="javascript:return confirm('データを削除します。\nよろしいですか？')"><i class="fas fa-check"></i> 本当に削除する</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endif
@endsection
