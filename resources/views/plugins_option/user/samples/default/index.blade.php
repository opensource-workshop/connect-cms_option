{{--
 * 表示画面テンプレート（デフォルト）
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category サンプル・プラグイン
 --}}
@extends('core.cms_frame_base')

@section("plugin_contents_$frame->id")

    @if (isset($frame) && $frame->bucket_id)
        {{-- バケツあり --}}

        {{-- 新規登録 --}}
        @can('posts.create',[[null, 'bbses', $buckets]])
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
                    <a href="{{URL::to('/')}}/plugin/samples/show/{{$page->id}}/{{$frame_id}}/{{$post_item->id}}#frame-{{$frame->id}}">
                        {{$post_item->title}}
                    </a>
                </dt>
                <dd class="col-9">{{$post_item->content}}</dd>
            @endforeach
        </div>

        {{-- ページング処理 --}}
        @include('plugins.common.user_paginate', ['posts' => $posts, 'frame' => $frame, 'aria_label_name' => $frame->id, 'class' => 'mt-3'])
    @else
        {{-- バケツなし --}}
        <div class="card border-danger">
            <div class="card-body">
                <p class="text-center cc_margin_bottom_0">フレームの設定画面から、使用するサンプルを選択するか、作成してください。</p>
            </div>
        </div>
    @endif
@endsection
