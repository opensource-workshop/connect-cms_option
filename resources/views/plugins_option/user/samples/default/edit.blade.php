{{--
 * 記事登録画面テンプレート
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category サンプル・プラグイン
--}}
@extends('core.cms_frame_base')

@section("plugin_contents_$frame->id")

{{-- 共通エラーメッセージ 呼び出し --}}
@include('plugins.common.errors_form_line')

{{-- 投稿用フォーム --}}
<form action="{{url('/')}}/redirect/plugin/samples/save/{{$page->id}}/{{$frame_id}}@if($post->id)/{{$post->id}}@endif#frame-{{$frame->id}}" method="post" name="form_post{{$frame_id}}">
    {{ csrf_field() }}
    <input type="hidden" name="redirect_path" value="{{url('/')}}/plugin/samples/edit/{{$page->id}}/{{$frame_id}}@if($post->id)/{{$post->id}}@endif#frame-{{$frame_id}}">

    <div class="form-group row">
        <label class="col-md-2 control-label text-md-right">タイトル <span class="badge badge-danger">必須</span></label>
        <div class="col-md-10">
            <input type="text" name="title" value="{{old('title', $post->title)}}" class="form-control @if ($errors->has('title')) border-danger @endif">
            @include('plugins.common.errors_inline', ['name' => 'title'])
        </div>
    </div>

    <div class="form-group row">
        <label class="col-md-2 control-label text-md-right">説明</label>
        <div class="col-md-10">
            <textarea name="content" class="form-control @if ($errors->has('content')) border-danger @endif" rows=5>{{old('content', $post->content)}}</textarea>
            @include('plugins.common.errors_inline', ['name' => 'content'])
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            @if (empty($post->id))
            <div class="col-12">
            @else
            <div class="col-3 d-none d-xl-block"></div>
            <div class="col-9 col-xl-6">
            @endif
                <div class="text-center">
                    <button type="button" class="btn btn-secondary mr-2" onclick="location.href='{{url($page->permanent_link)}}#frame-{{$frame->id}}'">
                        <i class="fas fa-times"></i>
                        <span class="{{$frame->getSettingButtonCaptionClass('lg')}}"> キャンセル</span>
                    </button>
                    <input type="hidden" name="bucket_id" value="">
                    @if (empty($post->id))
                        <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> 登録確定</button>
                    @else
                        <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> 変更確定</button>
                    @endif
                </div>
            </div>
            @if (!empty($post->id))
            <div class="col-3 col-xl-3 text-right">
                <a data-toggle="collapse" href="#collapse{{$frame_id}}">
                    <span class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i>
                        <span class="{{$frame->getSettingButtonCaptionClass('md')}}"> 削除</span>
                    </span>
                </a>
            </div>
            @endif
        </div>
    </div>
</form>

<div id="collapse{{$frame_id}}" class="collapse">
    <div class="card border-danger">
        <div class="card-body">
            <span class="text-danger">データを削除します。<br>元に戻すことはできないため、よく確認して実行してください。</span>
            <div class="text-center">
                {{-- 削除ボタン --}}
                <form action="{{url('/')}}/redirect/plugin/samples/delete/{{$page->id}}/{{$frame_id}}/{{$post->id}}#frame-{{$frame->id}}" method="POST">
                    {{csrf_field()}}
                    <input type="hidden" name="redirect_path" value="{{url($page->permanent_link)}}#frame-{{$frame_id}}">
                    <button type="submit" class="btn btn-danger" onclick="javascript:return confirm('データを削除します。\nよろしいですか？')">
                        <i class="fas fa-check"></i> 本当に削除する
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
