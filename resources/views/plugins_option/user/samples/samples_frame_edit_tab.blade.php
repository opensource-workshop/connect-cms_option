{{--
 * 編集画面tabテンプレート
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @author 牟田口 満 <mutaguchi@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category サンプル・プラグイン
 --}}
@if ($action == 'editBuckets')
    <li role="presentation" class="nav-item">
        <span class="nav-link"><span class="active">設定変更</span></span>
    </li>
@else
    <li role="presentation" class="nav-item">
        <a href="{{url('/')}}/plugin/{{$frame->plugin_name}}/editBuckets/{{$page->id}}/{{$frame->id}}#frame-{{$frame->id}}" class="nav-link">設定変更</a>
    </li>
@endif
@if ($action == 'createBuckets')
    <li role="presentation" class="nav-item">
        <span class="nav-link"><span class="active">新規作成</span></span>
    </li>
@else
    <li role="presentation" class="nav-item">
        <a href="{{url('/')}}/plugin/{{$frame->plugin_name}}/createBuckets/{{$page->id}}/{{$frame->id}}#frame-{{$frame->id}}" class="nav-link">新規作成</a>
    </li>
@endif
@if ($action == 'listBuckets')
    <li role="presentation" class="nav-item">
        <span class="nav-link"><span class="active">バケツ選択</span></span>
    </li>
@else
    <li role="presentation" class="nav-item">
        <a href="{{url('/')}}/plugin/{{$frame->plugin_name}}/listBuckets/{{$page->id}}/{{$frame->id}}#frame-{{$frame->id}}" class="nav-link">バケツ選択</a>
    </li>
@endif
@if ($action == 'editBucketsRoles')
    <li role="presentation" class="nav-item">
        <span class="nav-link"><span class="active">権限設定</span></span>
    </li>
@else
    <li role="presentation" class="nav-item">
        <a href="{{url('/')}}/plugin/{{$frame->plugin_name}}/editBucketsRoles/{{$page->id}}/{{$frame->id}}#frame-{{$frame->id}}" class="nav-link">権限設定</a>
    </li>
@endif
