{{--
 * 表示画面テンプレート。
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category covid19japan.com データ活用プラグイン
 --}}
@extends('core.cms_frame_base')

@section("plugin_contents_$frame->id")

@php
    $option_blade_path = 'plugins_option.user.covids.default.select_option';
@endphp

covid19japan.com データ活用プラグイン<br /><br />

<div class="accordion" id="accordionCluster">
@foreach($clusters as $knownClusterOne => $cluster)

    <div class="card">
        <div class="card-header p-1" id="heading{{$loop->iteration}}">
            <h5 class="mb-0">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse{{$loop->iteration}}" aria-expanded="false" aria-controls="collapse{{$loop->iteration}}">
                    {{key(current($cluster))}} - {{$knownClusterOne}} - {{key($cluster)}}
                </button>
            </h5>
        </div>

        <div id="collapse{{$loop->iteration}}" class="collapse" aria-labelledby="heading{{$loop->iteration}}" data-parent="#accordionCluster">
            <div class="card-body">

    <ul>
    @foreach($cluster as $pref => $cluster_dates)
        @php
            $pref_count = count($cluster_dates, true) - count($cluster_dates);
        @endphp
        <li>{{$pref}} ({{$pref_count}}件)</li>

        <ul>
        @foreach($cluster_dates as $cluster_date => $patient)
            <li>{{$cluster_date}} ({{count($patient, true)}}件)</li>
        @endforeach
        </ul>

    @endforeach
    </ul>

            </div>
        </div>
    </div>


@endforeach
</div>

@endsection
