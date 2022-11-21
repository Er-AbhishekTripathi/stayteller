<?php
namespace Modules\Property\Controllers;

use ICal\ICal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Modules\Booking\Models\Booking;
use Modules\FrontendController;
use Modules\Property\Models\Property;
use Modules\Property\Models\PropertyDate;

class AvailabilityController extends FrontendController{

    protected $propertyClass;
    /**
     * @var PropertyDate
     */
    protected $propertyDateClass;

    /**
     * @var Booking
     */
    protected $bookingClass;

    protected $indexView = 'Property::frontend.user.availability';

    public function __construct()
    {
        parent::__construct();
        $this->propertyClass = Property::class;
        $this->propertyDateClass = PropertyDate::class;
        $this->bookingClass = Booking::class;
    }
    public function callAction($method, $parameters)
    {
        if(!Property::isEnable())
        {
            return redirect('/');
        }
        return parent::callAction($method, $parameters); // TODO: Change the autogenerated stub
    }

    public function index(Request $request){
        $this->checkPermission('property_create');

        $q = $this->propertyClass::query();

        if($request->query('s')){
            $q->where('title','like','%'.$request->query('s').'%');
        }

        if(!$this->hasPermission('property_manage_others')){
            $q->where('create_user',$this->currentUser()->id);
        }

        $q->orderBy('bravo_properties.id','desc');

        $rows = $q->paginate(15);

        $current_month = strtotime(date('Y-m-01',time()));

        if($request->query('month')){
            $date = date_create_from_format('m-Y',$request->query('month'));
            if(!$date){
                $current_month = time();
            }else{
                $current_month = $date->getTimestamp();
            }
        }
        $breadcrumbs = [
            [
                'name' => __('Properties'),
                'url'  => route('property.vendor.index')
            ],
            [
                'name'  => __('Availability'),
                'class' => 'active'
            ],
        ];
        $page_title = __('Properties Availability');

        return view($this->indexView,compact('rows','breadcrumbs','current_month','page_title','request'));
    }

    public function loadDates(Request $request){

        $request->validate([
            'id'=>'required',
            'start'=>'required',
            'end'=>'required',
        ]);

        $property = $this->propertyClass::find($request->query('id'));
        if(empty($property)){
            return $this->sendError(__('Property not found'));
        }
        $is_single = $request->query('for_single');

        $query = $this->propertyDateClass::query();
        $query->where('target_id',$request->query('id'));
        $query->where('start_date','>=',date('Y-m-d H:i:s',strtotime($request->query('start'))));
        $query->where('end_date','<=',date('Y-m-d H:i:s',strtotime($request->query('end'))));

        $rows =  $query->take(40)->get();
        $allDates = [];

        for($i = strtotime($request->query('start')); $i <= strtotime($request->query('end')); $i+= DAY_IN_SECONDS)
        {
            $date = [
                'id'=>rand(0,999),
                'active'=>0,
                'price'=>(!empty($property->sale_price) and $property->sale_price > 0 and $property->sale_price < $property->price) ? $property->sale_price : $property->price,
                'is_instant'=>$property->is_instant,
                'is_default'=>true,
                'textColor'=>'#2791fe'
            ];
            if(!$is_single){
	            $date['price_html'] = format_money_main($date['price']);
            }else{
	            $date['price_html'] = format_money($date['price']);

            }
            $date['title'] = $date['event']  = $date['price_html'];
            $date['start'] = $date['end'] = date('Y-m-d',$i);

            if($property->default_state){
                $date['active'] = 1;
            }else{
                $date['title'] = $date['event'] = __('Blocked');
                $date['backgroundColor'] = 'orange';
                $date['borderColor'] = '#fe2727';
                $date['classNames'] = ['blocked-event'];
                $date['textColor'] = '#fe2727';
            }
            $allDates[date('Y-m-d',$i)] = $date;
        }
        if(!empty($rows))
        {
            foreach ($rows as $row)
            {
                $row->start = date('Y-m-d',strtotime($row->start_date));
                $row->end = date('Y-m-d',strtotime($row->start_date));
                $row->textColor = '#2791fe';
                $price = $row->price;
                if(empty($price)){
                    $price = (!empty($property->sale_price) and $property->sale_price > 0 and $property->sale_price < $property->price) ? $property->sale_price : $property->price;
                }
	            if(!$is_single){
		            $row->title = $row->event = format_money_main($price);
	            }else{
		            $row->title = $row->event = format_money($price);

	            }
                $row->price = $price;

                if(!$row->active)
                {
                    $row->title = $row->event = __('Blocked');
                    $row->backgroundColor = '#fe2727';
                    $row->classNames = ['blocked-event'];
                    $row->textColor = '#fe2727';
                    $row->active = 0;
                }else{
                    $row->classNames = ['active-event'];
                    $row->active = 1;
                    if($row->is_instant){
                        $row->title = '<i class="fa fa-bolt"></i> '.$row->title;
                    }
                }

                $allDates[date('Y-m-d',strtotime($row->start_date))] = $row->toArray();

            }
        }

        $bookings = $this->bookingClass::getBookingInRanges($property->id,$property->type,$request->query('start'),$request->query('end'));
        if(!empty($bookings))
        {
            foreach ($bookings as $booking){
                for($i = strtotime($booking->start_date); $i <= strtotime($booking->end_date); $i+= DAY_IN_SECONDS){
                    if(isset($allDates[date('Y-m-d',$i)])){
                        $allDates[date('Y-m-d',$i)]['active'] = 0;
                        $allDates[date('Y-m-d',$i)]['event'] = __('Full Book');
                        $allDates[date('Y-m-d',$i)]['title'] = __('Full Book');
                        $allDates[date('Y-m-d',$i)]['classNames'] = ['full-book-event'];
                    }
                }
            }
        }

	    // if(!empty($property->ical_import_url)){

		//     $startDate = $request->query('start');
		//     $endDate = $request->query('end');
		//     $timezone = setting_item('site_timezone',config('app.timezone'));
		//     try {
		// 	    $icalevents   =  new Ical($property->ical_import_url,[
		// 		    'defaultTimeZone'=>$timezone
		// 	    ]);
		// 	    $eventRange  = $icalevents->eventsFromRange($startDate,$endDate);
		// 	    if(!empty($eventRange)){
		// 		    foreach ($eventRange as $item=>$value){
		// 			    if(!empty($eventStart = $value->dtstart_array[2]) and !empty($eventEnd = $value->dtend_array[2])){
		// 				    for($i = $eventStart; $i <= $eventEnd; $i+= DAY_IN_SECONDS){
		// 					    if(isset($allDates[date('Y-m-d',$i)])){
		// 						    $allDates[date('Y-m-d',$i)]['active'] = 0;
		// 						    $allDates[date('Y-m-d',$i)]['event'] = __('Full Book');
		// 						    $allDates[date('Y-m-d',$i)]['title'] = __('Full Book');
		// 						    $allDates[date('Y-m-d',$i)]['classNames'] = ['full-book-event'];
		// 					    }
		// 				    }
		// 			    }
		// 		    }
		// 	    }
		//     }catch (\Exception $exception){
		// 	    return $this->sendError($exception->getMessage());

		//     }

	    // }


        $data = array_values($allDates);

        return response()->json($data);
    }

    public function store(Request $request){

        $request->validate([
            'target_id'=>'required',
            'start_date'=>'required',
            'end_date'=>'required'
        ]);

        $property = $this->propertyClass::find($request->input('target_id'));
        $target_id = $request->input('target_id');

        if(empty($property)){
            return $this->sendError(__('Property not found'));
        }

        if(!$this->hasPermission('property_manage_others')){
            if($property->create_user != Auth::id()){
                return $this->sendError("You do not have permission to access it");
            }
        }

        $postData = $request->input();
        for($i = strtotime($request->input('start_date')); $i <= strtotime($request->input('end_date')); $i+= DAY_IN_SECONDS)
        {
            $date = $this->propertyDateClass::where('start_date',date('Y-m-d',$i))->where('target_id',$target_id)->first();

            if(empty($date)){
                $date = new $this->propertyDateClass();
                $date->target_id = $target_id;
            }
            $postData['start_date'] = date('Y-m-d H:i:s',$i);
            $postData['end_date'] = date('Y-m-d H:i:s',$i);


            $date->fillByAttr([
                'start_date','end_date','price',
//                'max_guests','min_guests',
                'is_instant','active',
//                'enable_person','person_types'
            ],$postData);

            $date->save();
        }

        return $this->sendSuccess([],__("Update Success"));

    }
}
