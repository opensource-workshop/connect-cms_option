<?php

namespace App\ModelsOption\User\Covid19japan;

use Illuminate\Database\Eloquent\Model;

use App\Userable;

class Covid19japanPatient extends Model
{
    // 保存時のユーザー関連データの保持
    use Userable;

    // 更新する項目の定義
    protected $fillable = ['patientId', 'confirmedPatient', 'dateAnnounced', 'ageBracket', 'gender', 'residence', 'detectedCityTown', 'detectedPrefecture', 'patientStatus', 'mhlwPatientNumber', 'prefecturePatientNumber', 'prefectureSourceURL', 'sourceURL', 'notes', 'knownCluster'];

    /*
        knownCluster が変化したか。
    */
    public function isDirtyKnownCluster()
    {
        $attributes_knownCluster = '';
        $original_knownCluster = '';

        if (array_key_exists('knownCluster', $this->attributes)) {
            $attributes_knownCluster = $this->attributes['knownCluster'];
        }
        if (array_key_exists('knownCluster', $this->original)) {
            $original_knownCluster = $this->original['knownCluster'];
        }

        if ($attributes_knownCluster == $original_knownCluster) {
            return false;
        }
        return true;
    }
}
