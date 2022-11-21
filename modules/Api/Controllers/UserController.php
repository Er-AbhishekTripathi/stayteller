<?php
namespace Modules\Api\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Modules\Booking\Models\Booking;

class UserController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    public function getBookingHistory(\Illuminate\Http\Request $request){

        $user_id = Auth::id();
        $query = Booking::getBookingHistory($request->input('status'), $user_id);
        $rows = [];
        foreach ($query as $item){
            $rows[] = $item;
        }
        return $this->sendSuccess([
            'rows'=> $rows,
            'total'=>$query->total(),
            'max_pages'=>$query->lastPage()
        ]);
    }
}
