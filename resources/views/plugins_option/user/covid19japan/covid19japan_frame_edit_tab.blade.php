{{--
 * 編集画面tabテンプレート
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category covid19japan.com データ活用プラグイン
 --}}
@if ($action == 'setting')
    <li role="presentation" class="nav-item">
        <span class="nav-link"><span class="active">設定</span></span>
    </li>
@else
    <li role="presentation" class="nav-item">
        <a href="{{url('/')}}/plugin/covid19japan/setting/{{$page->id}}/{{$frame->id}}#frame-{{$frame->id}}" class="nav-link">設定</a>
    </li>
@endif
