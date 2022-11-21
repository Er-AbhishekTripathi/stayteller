<?php
namespace Modules\Review\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Review\Models\Review;
use Validator;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct()
    {
    }

    public function addReview(Request $request)
    {
        if (!Auth::id()) {
            return redirect()->to(url()->previous() . '#review-form')->with('error', __('Please login'));
        }
        $service_type = $request->input('review_service_type');
        $service_id = $request->input('review_service_id');
        $allServices = get_reviewable_services();
        $allServicesBooking = get_bookable_services();

        if (empty($allServices[$service_type])) {
            return redirect()->to(url()->previous() . '#review-form')->with('error', __('Service type not found'));
        }
        $module_class = $allServices[$service_type];
        $module = $module_class::find($service_id);

        if(empty($module)){
            return redirect()->to(url()->previous() . '#review-form')->with('error', __('Service not found'));
        }

        $reviewEnable = $module->getReviewEnable();
        if (!$reviewEnable) {
            return redirect()->to(url()->previous() . '#review-form')->with('error', __('Review not enable'));
        }
        $reviewEnableAfterBooking = $module->check_enable_review_after_booking();
        if (!empty($allServicesBooking[$service_type])) {
            if (!$reviewEnableAfterBooking) {
                return redirect()->to(url()->previous() . '#review-form')->with('error', __('You need booking before write a review'));
            } else {
                if (!$module->check_allow_review_after_making_completed_booking()) {
                    return redirect()->to(url()->previous() . '#review-form')->with('error', __('You can review after making completed booking'));
                }
            }
        }
        if ($module->create_user == Auth::id()) {
            return redirect()->to(url()->previous() . '#review-form')->with('error', __('You cannot review your service'));
        }
        
        if ($module_class == 'App\User' && $module->id == Auth::id()) {
            return redirect()->to(url()->previous() . '#review-form')->with('error', __('You cannot review your service'));            
        }

        $rules = [
            'review_title'   => 'required',
            'review_content' => 'required|min:10'
        ];
        $messages = [
            'review_title.required'   => __('Review Title is required field'),
            'review_content.required' => __('Review Content is required field'),
            'review_content.min'      => __('Review Content has at least 10 character'),
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->to(url()->previous() . '#review-form')->withErrors($validator->errors());
        }

        $review_rate = $request->input('review_rate');
        $review = new Review([
            "object_id"    => $service_id,
            "object_model" => $service_type,
            "title"        => $request->input('review_title'),
            "content"      => $request->input('review_content'),
            "rate_number"  => $review_rate ?? 0,
            "author_ip"    => $request->ip(),
            "status"       => !$module->getReviewApproved() ? "approved" : "pending",
            'vendor_id'     =>$module->create_user
        ]);

        if ($review->save()) {
            $msg = __('Review success!');
            if ($module->getReviewApproved()) {
                $msg = __("Review success! Please wait for admin approved!");
            }
            return redirect()->to(url()->previous() . '#bravo-reviews')->with('success', $msg);
        }
        return redirect()->to(url()->previous() . '#review-form')->with('error', __('Review error!'));
    }
}
