{{--
 * 記事詳細画面テンプレート
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category サンプル・プラグイン
--}}
@extends('core.cms_frame_base')

@section("plugin_contents_$frame->id")
<div class="card form-group">
    <div class="card-header">{{$post->title}}</div>
    <div class="card-body">{{$post->content}}</div>
    @can('posts.update',[[$post, $frame->plugin_name, $buckets]])
    <div class="text-right m-1">
        <div class="btn-group">
            <a href="{{url('/')}}/plugin/samples/edit/{{$page->id}}/{{$frame_id}}/{{$post->id}}#frame-{{$frame->id}}" class="btn btn-success btn-sm">
                <i class="far fa-edit"></i> <span class="d-none d-sm-inline">編集</span>
            </a>
        </div>
    </div>
    @endcan
</div>

{{-- 一覧へ戻る --}}
<nav class="text-center">
    <a href="{{url('/')}}{{$page->getLinkUrl()}}#frame-{{$frame->id}}" class="btn btn-info">
        <i class="fas fa-list"></i> <span class="d-none d-sm-inline">{{__('messages.to_list')}}</span>
    </a>
</nav>
@endsection
