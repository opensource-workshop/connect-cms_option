{{--
 * 表示画面テンプレート。
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category DroneStudyプラグイン
 --}}
@extends('core.cms_frame_base')

@section("plugin_contents_$frame->id")

    <form action="{{url('/')}}/plugin/dronestudies/savexml/{{$page->id}}/{{$frame_id}}#frame-{{$frame->id}}" method="POST" name="form_dronestudies" class="">
        {{csrf_field()}}
        <input type="hidden" name="dronestudy_id" value="{{$dronestudy->id}}">

        <div class="form-group">
            <label class="control-label">タイトル</label><br />
            <input type="text" name="title" value="{{old('title', $dronestudy_content->title)}}" class="form-control">
        </div>

        <div class="form-group">
            <label class="control-label">コード <label class="badge badge-danger">必須</label></label><br />
            <textarea id="txt-editor" class="form-control" rows="10" name="code_text" style="font-family:'ＭＳ ゴシック', 'MS Gothic', 'Osaka－等幅', Osaka-mono, monospace;">{!!old('xml_text', $dronestudy_content->xml_text)!!}</textarea>
            @if ($errors && $errors->has('xml_text')) <div class="text-danger">{{$errors->first('xml_text')}}</div> @endif
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-8 mx-auto">
                    <div class="text-center">
                        <button type="submit" class="btn btn-success mr-3"><i class="far fa-save"></i> 保存のみ</button>
                        <button type="button" class="btn btn-primary mr-3" onclick="javascript:submit_codestudies_run();"><i class="fas fa-check"></i> 保存と実行</button>
                        <button type="button" class="btn btn-secondary" onclick="location.href='{{URL::to($page->permanent_link)}}#frame-{{$frame->id}}'"><i class="fas fa-times"></i> キャンセル</button>
                    </div>
                </div>
                <div class="col-sm-2">
                    @if (!empty($dronestudy_content->id))
                        <a data-toggle="collapse" href="#collapse{{$dronestudy_content->id}}">
                            <span class="btn btn-danger"><i class="fas fa-trash-alt"></i> <span class="hidden-xs">削除</span></span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    <div id="collapse{{$dronestudy_content->id}}" class="collapse mt-3">
        <div class="card border-danger mb-3">
            <div class="card-body">
                <span class="text-danger">プログラムを削除します。<br>元に戻すことはできないため、よく確認して実行してください。</span>

                <div class="text-center">
                    {{-- 削除ボタン --}}
                    <form action="{{url('/')}}/plugin/dronestudies/deletecode/{{$page->id}}/{{$frame_id}}/{{$dronestudy_content->id}}#frame-{{$frame->id}}" method="POST">
                        {{csrf_field()}}
                        <button type="submit" class="btn btn-danger" onclick="javascript:return confirm('プログラムを削除します。\nよろしいですか？')"><i class="fas fa-check"></i> 本当に削除する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-info">
        <div class="card-header">保存済みプログラム</div>
        <div class="card-body">
            <ol>
            @foreach($dronestudy_contents as $content)
                @if($content->title)
                    <li><a href="{{URL::to('/')}}/plugin/dronestudies/index/{{$page->id}}/{{$frame_id}}/{{$content->id}}#frame-{{$frame->id}}">{{$content->title}}</a></li>
                @else
                    <li><a href="{{URL::to('/')}}/plugin/dronestudies/index/{{$page->id}}/{{$frame_id}}/{{$content->id}}#frame-{{$frame->id}}">無題</a></li>
                @endif
            @endforeach
            </ol>
        </div>
    </div>
@endsection
