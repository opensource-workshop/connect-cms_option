{{--
 * 表示画面テンプレート（デフォルト）
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category フォトアルバムビューア プラグイン
 --}}
@extends('core.cms_frame_base')

@section("plugin_contents_$frame->id")

<style type="text/css">
<!--
.flex-container {
    display: flex;
    margin: 0;
    padding: 0;
    margin-right: -15px;
    margin-left: -15px;
}

.flex-item {
    flex-basis: 100%;
}

.image-wrap{
    position: relative;
    overflow: hidden;
    padding-top: 60%;
    margin: 0;
}

.image-wrap img {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position :center top;
}
-->
</style>

@foreach ($photoalbum_contents2 as $photoalbum_content2)
<div class="flex-container">
    @foreach ($photoalbum_content2 as $photoalbum_content)
        <div class="flex-item">
            <div class="image-wrap">
                <a href="{{url('/')}}/plugin/photoalbums/changeDirectory/{{$link_page->page_id}}/{{$link_page->id}}/{{$photoalbum_content->parent_id}}/#frame-{{$link_page->id}}">
                @if (empty($photoalbum_content->parent_name))
                    <img src="{{url('/')}}/file/{{$photoalbum_content->upload_id}}?size=small" class="img-fluid border border-dark" title="{{$photoalbum_content->name}}">
                @else
                    <img src="{{url('/')}}/file/{{$photoalbum_content->upload_id}}?size=small" class="img-fluid border border-dark" title="{{$photoalbum_content->parent_name}} - {{$photoalbum_content->name}}">
                @endif
                </a>
            </div>
        </div>
    @endforeach
</div>
@endforeach


@endsection
