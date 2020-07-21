<?php

namespace App\PluginsOption\User\Covid19japan;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use File;
use DB;
use Session;
use Storage;

use App\Models\Common\Buckets;
use App\Models\Common\Frame;
use App\ModelsOption\User\Covid19japan\Covid19japanClusters;
use App\ModelsOption\User\Covid19japan\Covid19japanPatient;

use App\PluginsOption\User\UserPluginOptionBase;

/**
 * covid19japan.com データ活用プラグイン
 *
 * covid19japan.com のデータを表示するプラグイン。
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category covid19japan.com データ活用プラグイン
 * @package Contoroller
 */
class Covid19japanPlugin extends UserPluginOptionBase
{

    /**
     *  関数定義（コアから呼び出す）
     */
    public function getPublicFunctions()
    {
        // 標準関数以外で画面などから呼ばれる関数の定義
        $functions = array();
        $functions['get']  = [
            'search',
            'setting',
        ];
        $functions['post'] = [
            'search',
            'pullData',
        ];
        return $functions;
    }

    /**
     *  権限定義
     */
    public function declareRole()
    {
        // 権限チェックテーブル
        $role_ckeck_table = array();
        $role_ckeck_table["setting"] = array('role_arrangement');
        $role_ckeck_table["pullData"] = array('role_arrangement');
        return $role_ckeck_table;
    }

    /* オブジェクト変数 */

    /* 画面アクション関数 */

    /**
     *  絞り込みアクション
     */
    public function search($request, $page_id, $frame_id)
    {
        return $this->index($request, $page_id, $frame_id);
    }

    /**
     *  データ初期表示関数
     *  コアがページ表示の際に呼び出す関数
     */
    public function index($request, $page_id, $frame_id)
    {
        // データ取得
//        $patients = null;
//        $patients = Covid19japanPatient::get();

        // 全クラスター
        $clusters = array();
        $cluster_records = Covid19japanClusters::select('patientId', 'dateAnnounced', 'detectedPrefecture', 'knownClusterOne')
                                               ->join('covid19japan_patients', 'covid19japan_patients.id', '=', 'covid19japan_clusters.patient_id')
                                               ->orderBy('dateAnnounced', 'DESC')
                                               ->get();
        foreach ($cluster_records as $cluster_record) {
            $clusters[$cluster_record->knownClusterOne][$cluster_record->detectedPrefecture][$cluster_record->dateAnnounced][] = $cluster_record->patientId;

//            $clusters[$cluster_record->knownClusterOne]['items'][$cluster_record->dateAnnounced]['items'][$cluster_record->detectedPrefecture][] = $cluster_record->patientId;

            // カウント
//            if (!array_key_exists('count', $clusters[$cluster_record->knownClusterOne])) {
//                $clusters[$cluster_record->knownClusterOne]['count'] = 0;
//            }
//            $clusters[$cluster_record->knownClusterOne]['count'] = $clusters[$cluster_record->knownClusterOne]['count'] + 1;

        }
//Log::debug($clusters);


/*
    -- 直近1ヶ月のクラスター 件数の集計
    SELECT count(covid19japan_clusters.id) AS count, MAX(dateAnnounced) AS max_dateAnnounced, knownClusterOne
    FROM  covid19japan_clusters
          INNER JOIN covid19japan_patients ON covid19japan_patients.id = covid19japan_clusters.patient_id
    WHERE dateAnnounced > '2020-06-19'
    GROUP BY knownClusterOne
    ORDER BY max_dateAnnounced DESC, count DESC

    -- 直近1ヶ月のクラスター

    クラスター場所（合計件数） - 日付 - 都道府県（件数）の表示

    SELECT dateAnnounced, detectedPrefecture, knownClusterOne
    FROM  covid19japan_clusters
          INNER JOIN covid19japan_patients ON covid19japan_patients.id = covid19japan_clusters.patient_id
    WHERE dateAnnounced > '2020-06-19'
    ORDER BY dateAnnounced DESC





    SELECT count(id) AS count, detectedPrefecture, knownCluster
    FROM covid19japan_patients
    WHERE knownCluster != ''
    GROUP BY detectedPrefecture, knownCluster
    ORDER BY detectedPrefecture, count DESC

    SELECT count(id) AS count, MIN(detectedPrefecture), knownCluster
    FROM covid19japan_patients
    WHERE knownCluster != ''
    GROUP BY knownCluster
    ORDER BY count DESC

    SELECT count(id) AS count
    FROM covid19japan_patients
    WHERE knownCluster != ''
*/



        // 表示テンプレートを呼び出す。
        return $this->view(
            'index', [
            'clusters' => $clusters,
            ]
        );
    }

    /**
     *  設定画面
     */
    public function setting($request, $page_id, $frame_id)
    {

        // 表示テンプレートを呼び出す。
        return $this->view(
            'setting', [
//            'covid'         => $covid,
            ]
        );
    }

    /**
     *  データ取り込み処理
     */
    public function pullData($request, $page_id, $frame_id)
    {
        set_time_limit(3600);

        // Covid19Japan から最新ファイル名を取得
        $latest_filename = $this->getData("latest.json");

        // Covid19Japan からデータ取得
        $patients_json = $this->getData($latest_filename);
        $patients = json_decode($patients_json);

        // データベースに設定
        foreach ($patients as $patient) {
            // patientId が -1 のものは省く
            if ($patient->patientId == "-1") {
                continue;
            }

            // patient データ取得。なければ新しいインスタンスを生成
            $patient_model = Covid19japanPatient::firstOrNew(['patientId' => $patient->patientId]);

            // データセット
            $patient_model->patientId               = $this->getProperty($patient, 'patientId');
            $patient_model->confirmedPatient        = $this->getProperty($patient, 'confirmedPatient', false);
            $patient_model->dateAnnounced           = $this->getProperty($patient, 'dateAnnounced');
            $patient_model->ageBracket              = $this->getProperty($patient, 'ageBracket', 0);
            $patient_model->gender                  = $this->getProperty($patient, 'gender');
            $patient_model->residence               = $this->getProperty($patient, 'residence');
            $patient_model->detectedCityTown        = $this->getProperty($patient, 'detectedCityTown');
            $patient_model->detectedPrefecture      = $this->getProperty($patient, 'detectedPrefecture');
            $patient_model->patientStatus           = $this->getProperty($patient, 'patientStatus');
            $patient_model->mhlwPatientNumber       = $this->getProperty($patient, 'mhlwPatientNumber');
            $patient_model->prefecturePatientNumber = $this->getProperty($patient, 'prefecturePatientNumber');
            $patient_model->prefectureSourceURL     = $this->getProperty($patient, 'prefectureSourceURL');
            $patient_model->sourceURL               = $this->getProperty($patient, 'sourceURL');
            $patient_model->notes                   = $this->getProperty($patient, 'notes');
            $patient_model->knownCluster            = $this->getProperty($patient, 'knownCluster');

            // クラスタ情報に変化があったら、クラスタ情報の生成 or 作り直し
            $is_dirty_knownCluster = $patient_model->isDirtyKnownCluster();

            // サロゲートキーを取得するために、データ保存
            $patient_model->save();

            // クラスタ情報
            if ($is_dirty_knownCluster) {
                // クラスターテーブルはDelete＆Insert
                // （クラスター文字列を分割するテーブルのため、元データに明確なキーを持たないため、update ができない）
                Covid19japanClusters::where('patient_id', $patient_model->id)->delete();

                // クラスター情報分割
                $known_clusters = explode(',', $patient_model->knownCluster);

                if (!empty($known_clusters)) {
                    foreach ($known_clusters as $known_cluster) {
                        Covid19japanClusters::create([
                            'patient_id'      => $patient_model->id,
                            'knownClusterOne' => $known_cluster,
                        ]);
                   }
                }
            }
        }

        // 設定画面を呼び出す。
        return $this->setting($request, $page_id, $frame_id);
    }

    /**
     *  データ取り込み
     */
    private function getData($filename)
    {
        // データのベースURL
        $base_url = "http://cms.localhost/debug/covid19japan/";

        // データURL
        $request_url = $base_url . $filename;

        // Github からデータ取得（HTTP レスポンスが gzip 圧縮されている）
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        //リクエストヘッダ出力設定
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        // 最新ファイル名の取得実行
        $http_data = curl_exec($ch);

        // HTTPヘッダ取得
        $http_header = curl_getinfo($ch);
        if (empty($http_header) || !array_key_exists('http_code', $http_header) || $http_header['http_code'] != 200) {
            // データが取得できなかったため、スルー。
            echo "データ取得エラー発生<br />\n";
            echo "<pre>";
            print_r($http_header);
            echo "</pre>";
        }

        return $http_data;
    }

    /**
     *  全体でのクラスター取得
     */
    private function getCluster()
    {
        if (property_exists($obj, $property_name)) {
            return $obj->$property_name;
        }
        return $default;
    }

    /**
     *  オブジェクトのプロパティ取得
     */
    private function getProperty($obj, $property_name, $default = '')
    {
        if (property_exists($obj, $property_name)) {
            return $obj->$property_name;
        }
        return $default;
    }
}
