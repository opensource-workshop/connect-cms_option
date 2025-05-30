{{--
 * 表示画面テンプレート（デフォルト）
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @author 牟田口 満 <mutaguchi@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category サンプル・プラグイン
 --}}
@extends('core.cms_frame_base')

@section("plugin_contents_$frame->id")
    {{-- 登録後メッセージ表示 --}}
    @include('plugins.common.flash_message_for_frame')

    {{-- 新規登録 --}}
    @can('posts.create',[[null, $frame->plugin_name, $buckets]])
        @if (isset($frame) && $frame->bucket_id)
            <div class="row">
                <p class="text-right col-12">
                    {{-- 新規登録ボタン --}}
                    <button type="button" class="btn btn-success" onclick="location.href='{{url('/')}}/plugin/samples/edit/{{$page->id}}/{{$frame_id}}#frame-{{$frame_id}}'">
                        <i class="far fa-edit"></i> 新規登録
                    </button>
                </p>
            </div>
        @endif
    @endcan

    <div class="row">
        @foreach($posts as $post_item)
            <dt class="col-3">
                <a href="{{url('/')}}/plugin/samples/show/{{$page->id}}/{{$frame_id}}/{{$post_item->id}}#frame-{{$frame->id}}">
                    {{$post_item->title}}
                </a>
            </dt>
            <dd class="col-9">{{$post_item->content}}</dd>
        @endforeach
    </div>

    {{-- ページング処理 --}}
    @include('plugins.common.user_paginate', ['posts' => $posts, 'frame' => $frame, 'aria_label_name' => $frame->id, 'class' => 'mt-3'])
@endsection
