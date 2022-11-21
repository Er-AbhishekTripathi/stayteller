<?php
namespace Modules\Voucher\Models;

use App\BaseModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends BaseModel
{
    use SoftDeletes;
    protected $table    = 'bravo_voucher';
    protected $fillable = [
        'code',
        'amount',
        'start_date',
        'end_date',
    ];

    /**
     * Get Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getUserInfo()
    {
        return $this->hasOne("Modules\User\Models\User", "id", 'create_user')->withTrashed();
    }

    public function getService()
    {
        $allServices = get_reviewable_services();
        $module = $allServices[$this->object_model];
        return $this->hasOne($module, "id", 'property_id');
    }

    public static function countReviewByStatus($status = false)
    {
        $count = parent::query();
        if (!empty($status)) {
            $count->where("status", $status);
        }
        return $count->count("id");
    }

    public static function countReviewByServiceID($service_id = false, $user_id = false, $status = false,$service_type = '')
    {
        if (empty($service_id))
            return false;
        $count = parent::where("object_id", $service_id);
        if (!empty($status)) {
            $count->where("status", $status);
        }

        if($service_type){
            $count->where('object_model',$service_type);
        }
        if (!empty($user_id)) {
            $count->where("create_user", $user_id);
        }
        return $count->count("id");
    }

    public function save(array $options = [])
    {
        $check = parent::save($options); // TODO: Change the autogenerated stub
        if ($check) {
            Cache::forget("voucher_" . $this->object_model . "_" . $this->object_id);
        }
        return $check;
    }

    public static function getTotalViewAndRateAvg($objectId, $objectModel)
    {
        $list_score = [
            'score_total'  => 0,
            'total_review' => 0,
        ];
        if (empty($objectId) || empty($objectModel)) {
            return $list_score;
        }

        $list_score = Cache::rememberForever('review_'.$objectModel.'_' . $objectId, function () use ($objectId, $objectModel) {
            $dataReview = self::selectRaw(" AVG(rate_number) as score_total , COUNT(id) as total_review ")->where('object_id', $objectId)->where('object_model', $objectModel)->where("status", "approved")->first();
            $score_total = !empty($dataReview->score_total) ? number_format($dataReview->score_total, 1) : 0;
            return [
                'score_total'  => $score_total,
                'total_review' => !empty($dataReview->total_review) ? $dataReview->total_review : 0,
            ];
        });
        return $list_score;
    }

}