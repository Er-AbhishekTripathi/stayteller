<?php
namespace Modules\Room\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Room\Models\Room;
use Validator;
use Illuminate\Support\Facades\Auth;
use Modules\Property\Models\Property;
use Modules\Room\Models\Availability;
use Modules\Review\Models\Review;
use Modules\Property\Models\PropertyTranslation;
use Modules\Property\Models\PropertyCategory;
use Modules\Location\Models\Location;
use Modules\Core\Models\Attributes;
use DateTime;
use DatePeriod;
use DateInterval;



class RoomController extends Controller
{
    protected $roomClass;
    protected $propertyClass;
    protected $propertyTranslationClass;
    protected $propertyCategoryClass;
    protected $locationClass;
    protected $attributesClass;

    public function __construct()
    {
        $this->roomClass      = Room :: class;
        $this->propertyClass = Property::class;
        $this->propertyTranslationClass = PropertyTranslation::class;
        $this->propertyCategoryClass = PropertyCategory::class;
        $this->locationClass = Location::class;
        $this->attributesClass = Attributes::class;
    }
    private $dayLabels = array("Mon","Tue","Wed","Thu","Fri","Sat","Sun");
     
    private $currentYear=0;
     
    private $currentMonth=0;
     
    private $currentDay=0;
     
    private $currentDate=null;
     
    private $daysInMonth=0;
     
    private $naviHref= null;

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

    
    public function create(Request $request){
       
         $this->checkPermission('rooms_create');
 
         $row = new $this->propertyClass();
         $data = [
             'row'           => $row,
             'translation' => new $this->propertyTranslationClass(),
             'property_category'    => $this->propertyCategoryClass::where('status', 'publish')->get()->toTree(),
             'property_location' => $this->locationClass::where("status","publish")->get()->toTree(),
             'attributes'    => $this->attributesClass::where('service', 'property')->get(),
             'breadcrumbs'        => [
                 [
                     'name' => __('Manage Properties'),
                     'url'  => route('property.vendor.index')
                 ],
                 [
                     'name'  => __('Create'),
                     'class' => 'active'
                 ],
             ],
             'page_title'         => __("Create Properties"),
         ];
         return view('Room::create', $data);
     }


     public function index(Request $request)
     {
     
         //$this->checkPermission("rooms_manage_others");
         //$this->checkPermission('property_view');
         $user_id = Auth::id();
         $rows = $this->roomClass::query()->select("bravo_rooms.*","bravo_properties.*","bravo_rooms.id as roomid");
         if (!empty($search = $request->input("s"))) {
            $rows->where(function ($query) use ($search) {
                $query->where('bravo_rooms.name', 'LIKE', '%'.$search.'%');
            });
        } 
        $rows->leftJoin('bravo_properties', function ($join)  {
            $join->on('bravo_properties.id', '=', 'bravo_rooms.property_id');
        })->where('bravo_properties.create_user', '=',Auth::id());
        $rows->orderBy('bravo_properties.id','desc');
       
        
 
         $data = [
             'rows' => $rows->paginate(5),
             'breadcrumbs'        => [
                 [
                     'name' => __('Manage Properties'),
                     'url'  => route('property.vendor.index')
                 ],
                 [
                     'name'  => __('All'),
                     'class' => 'active'
                 ],
             ],
             'page_title'         => __("Manage Properties"),
         ];
 
       
        
         return view('Room::front.index', $data);
     
         /*$model = Room::query();
         $model->orderBy('id', 'desc');
         if (!empty($author = $request->input('customer_id'))) {
             $model->where('create_user', $author);
         }
        
         $allServices = get_reviewable_services();
         $allServicesKeys = array_keys($allServices);
 
         if (!empty($search_name = $request->input('s'))) {
             $search_name = "%".$search_name."%";
             $model->whereRaw(" ( title LIKE ? OR author_ip LIKE ? OR content LIKE ? ) ",[$search_name,$search_name,$search_name]);
             $model->orderBy('title', 'asc');
         }
         if (!empty($status = $request->input('status'))) {
             $model->where('status', $status);
         }
         if (!empty($service_type = $request->input('service'))) {
             $model->where('object_model', $service_type);
         }
         if (!empty($service_id = $request->input('service_id'))) {
             $model->where('object_id', $service_id);
         }
         if (!empty($object_model = $request->input('object_model')) and in_array($object_model,$allServicesKeys)) {
             $model->where('object_model', $object_model );
         }
         $model->whereIn('object_model', $allServicesKeys );
         $data = [
             'rows'        => $model->paginate(10),
             'breadcrumbs' => [
                 ['name'  => __('Room'),
                  'class' => 'active'
                 ],
             ]
         ];
         return view('Room::admin.index', $data);*/
     }


     
    public function createroom(Request $request){
       
        //$this->checkPermission('rooms_create');

        $row =new $this->propertyClass();
      
        $data = [
            'row'           =>$row,
            'translation' => new $this->propertyTranslationClass(),
            'property_category'    => $this->propertyCategoryClass::where('status', 'publish')->get()->toTree(),
            'property_location' => $this->locationClass::where("status","publish")->get()->toTree(),
            'attributes'    => $this->attributesClass::where('service', 'property')->get(),
            'roomtype'      => $this->attributesClass::where('service', 'property')->where('name' ,'=','Room Type')->get(),
            
            'breadcrumbs'        => [
                [
                    'name' => __('Manage Room'),
                    'url'  => route('room.admin.index')
                ],
                [
                    'name'  => __('Create'),
                    'class' => 'active'
                ],
            ],
            'page_title'         => __("Create Room"),
        ];
       
        return view('Room::front.create', $data);
    }


    public function store(Request $request , $id){

        
       
        $attributecollection  = $this->attributesClass::where('service', 'property')->get();
        $attributedata = array();
       // dd($request->input());
        foreach($attributecollection as $attribute){
            $strdatareplace = str_replace("-", "_", $attribute->slug);
           
            if($attribute->room_Property == 1){
                $attributedata[] =array($strdatareplace => $request->$strdatareplace,
            );
           
            }
            if($attribute->features_enable == 1){
                 $choice = str_replace("-", "_", $attribute->slug.'_choice');
                $feature[] = array($strdatareplace => isset($request->$choice) ? implode(',',$request->$choice) : array());
            }
        }
        $id = $request->input('id');
       
        if ($id) {
            $room = Room::find($id);
            if (empty($room)) {
                return redirect()->back()->with('error', __('Room not found!'));
            }
        }else{
            $room                       = new Room();

        }
       
       
        $room->property_id          = $request->property_id;
        $room->name                 = $request->name;
        $room->room_info            = json_encode($attributedata);
        $room->amenities_details    = json_encode($feature);
        $room->no_of_room           = $request->no_of_room;
        $room->price_per_month      = $request->price_per_month;
        
        $room->refundable             = $request->refundable;
        $room->create_user          = Auth::id();
        $room->update_user          =  Auth::id();
        $room->save();

    if($id == 0){
        
        $n= 90;
        $i = 1;
        $date = date(date('d-m-Y'));
        $room_availability                      = new Availability();
        while($i <= $n) {
            $add_days =  $i++;
            $ppdate = date('Y-m-d',strtotime($date.' +'.$add_days.'days'));
            $room_availability->room_id             = $room->id;
            $room_availability->available_room      = $room->no_of_room;
            $room_availability->start_date          = $ppdate;
            $room_availability->save();
        }
    }
        

        return back()->with('success', ($id and $id>0) ? __('Room updated'):__("Room created"));


    }

    public function edit(Request $request, $id){
      
        $findrow = $this->roomClass::find($id);
        if (empty($findrow)) {
            return redirect()->back()->with('error', __('Room not found!'));
        }
        //$translation = $findrow->translateOrOrigin($request->query('lang'));
       // $this->checkPermission('property_manage_attributes');

       $row =new $this->propertyClass();
      
       $data = [
           'row'           =>$row,
           'translation' => new $this->propertyTranslationClass(),
           'property_category'    => $this->propertyCategoryClass::where('status', 'publish')->get()->toTree(),
           'property_location' => $this->locationClass::where("status","publish")->get()->toTree(),
           'attributes'    => $this->attributesClass::where('service', 'property')->get(),
           'roomtype'      => $this->attributesClass::where('service', 'property')->where('name' ,'=','Room Type')->get(),
           'editrow'       => $findrow,
           
           'breadcrumbs'        => [
               [
                   'name' => __('Manage Room'),
                   'url'  => route('room.admin.index')
               ],
               [
                   'name'  => __('Create'),
                   'url'  => 'admin/module/room/create'
               ],
               [
                'name'  => __('Room: :name', ['name' => $findrow->name]),
                'class' => 'active'
            ]
           ],
           'page_title'         => __("Edit Room"),
       ];

       return view('Room::front.create', $data);
       
    }

    public function vacancyupdate (Request $request, $id){

        $rows = $this->roomClass::query()->select("bravo_rooms.*","bravo_properties.*","bravo_rooms.id as roomid")
        ->leftJoin('bravo_properties', function ($join)  {
      $join->on('bravo_properties.id', '=', 'bravo_rooms.property_id');
     })->where('bravo_rooms.id', '=',$id)->get();
        

        $_GET['year'] =   (isset($_GET['year'])) ? $_GET['year'] : date('Y');
        $_GET['month']  = isset($_GET['month']) ? $_GET['month'] :date('m');

        $year  = null;
         
        $month = null;
         
        if(null==$year&&isset($_GET['year'])){
 
            $year = $_GET['year'];
         
        }else if(null==$year){
 
            $year = date("Y",time());  
         
        }          
         
        if(null==$month&&isset($_GET['month'])){
 
            $month = $_GET['month'];
         
        }else if(null==$month){
 
            $month = date("m",time());
         
        }                  
         
        $this->currentYear=$year;
         
        $this->currentMonth=$month;
         
        $this->daysInMonth=$this->_daysInMonth($month,$year);  
         
        $content='<div id="calendar">'.
                        '<div class="box">'.
                        $this->_createNavi().
                        '</div>'.
                        '<div class="box-content">'.
                                '<ul class="label">'.$this->_createLabels().'</ul>';   
                                $content.='<div class="clear"></div>';     
                                $content.='<ul class="dates">';    
                                 
                                $weeksInMonth = $this->_weeksInMonth($month,$year);
                                // Create weeks in a month
                                for( $i=0; $i<$weeksInMonth; $i++ ){
                                     
                                    //Create days in a week
                                    for($j=1;$j<=7;$j++){
                                        $content.=$this->_showDay($i*7+$j,$id);
                                    }
                                }
                                 
                                $content.='</ul>';
                                 
                                $content.='<div class="clear"></div>';     
             
                        $content.='</div>';
                 
        $content.='</div>';
       

    $data = array('html'=> $content,
                  'room_id' => $id,
                  'rows'    => $rows);

    return view('Room::front.vacancy', $data);

       
    }

    private function _showDay($cellNumber,$id){
         
        if($this->currentDay==0){
             
            $firstDayOfTheWeek = date('N',strtotime($this->currentYear.'-'.$this->currentMonth.'-01'));
                     
            if(intval($cellNumber) == intval($firstDayOfTheWeek)){
                 
                $this->currentDay=1;
                 
            }
        }
         
        if( ($this->currentDay!=0)&&($this->currentDay<=$this->daysInMonth) ){
             
            $this->currentDate = date('Y-m-d',strtotime($this->currentYear.'-'.$this->currentMonth.'-'.($this->currentDay)));
            
             
            $cellContent = $this->currentDay;

             
            $this->currentDay++;   
             
        }else{
             
            $this->currentDate =null;
 
            $cellContent=null;
        }
        
        if($cellContent != ''  && date('Y-m-d') <= $this->currentDate){
            
            $availablitydatacount = 0;
            if($id != ''){

                $availabilitycountcollection =  Availability :: where('room_id',$id)->where('start_date',$this->currentDate)->first();
               
                $availabledate = date('Y-m-d',strtotime($this->currentYear.'-'.$this->currentMonth.'-'.($this->currentDay)));
             
                $availablitydatacount =  isset($availabilitycountcollection) ? $availabilitycountcollection->available_room : 0;
            }
            $input_tag = '<span>-'.$availablitydatacount.'</span><br><span class=" btn btn-link fz14 availabiltyupdate fa fa-edit" data-availability = "'.$availablitydatacount.'" data-room_id = "'.$id.'" data-date = "'.$this->currentDate.'" data-toggle="modal" data-target="#exampleModal"  style="font-size: 11px; font-weight: bold; cursor: pointer; color: red;" >
            update
          </span>' ;
        }else if($cellContent != ''){
            $availabilitycountcollection =  Availability :: where('room_id',$id)->where('start_date',$this->currentDate)->first();
            $availabledate = date('Y-m-d',strtotime($this->currentYear.'-'.$this->currentMonth.'-'.($this->currentDay)));
             
            $availablitydatacount =  isset($availabilitycountcollection) ? $availabilitycountcollection->available_room : 0;
            $input_tag   = '-<span>'.$availablitydatacount.'</span>';
        }else{
            $input_tag   = '';
        }
           
         
        return '<li id="li-'.$this->currentDate.'" class="'.($cellNumber%7==1?' start ':($cellNumber%7==0?' end ':' ')).
                ($cellContent==null?'mask':'').'">'.$cellContent.$input_tag.'</li>';
    }
     
    /**
    * create navigation
    */
    private function _createNavi(){
         
        $nextMonth = $this->currentMonth==12?1:intval($this->currentMonth)+1;
         
        $nextYear = $this->currentMonth==12?intval($this->currentYear)+1:$this->currentYear;
         
        $preMonth = $this->currentMonth==1?12:intval($this->currentMonth)-1;
         
        $preYear = $this->currentMonth==1?intval($this->currentYear)-1:$this->currentYear;
         
        return
            '<div class="header">'.
                '<a class="prev" href="'.$this->naviHref.'?month='.sprintf('%02d',$preMonth).'&year='.$preYear.'">Prev</a>'.
                    '<span class="title">'.date('Y M',strtotime($this->currentYear.'-'.$this->currentMonth.'-1')).'</span>'.
                '<a class="next" href="'.$this->naviHref.'?month='.sprintf("%02d", $nextMonth).'&year='.$nextYear.'">Next</a>'.
            '</div>';
    }
         
    /**
    * create calendar week labels
    */
    private function _createLabels(){  
                 
        $content='';
         
        foreach($this->dayLabels as $index=>$label){
             
            $content.='<li class="'.($label==6?'end title':'start title').' title">'.$label.'</li>';
 
        }
         
        return $content;
    }
     
     
     
    /**
    * calculate number of weeks in a particular month
    */
    private function _weeksInMonth($month=null,$year=null){
         
        if( null==($year) ) {
            $year =  date("Y",time()); 
        }
         
        if(null==($month)) {
            $month = date("m",time());
        }
         
        // find number of days in this month
        $daysInMonths = $this->_daysInMonth($month,$year);
         
        $numOfweeks = ($daysInMonths%7==0?0:1) + intval($daysInMonths/7);
         
        $monthEndingDay= date('N',strtotime($year.'-'.$month.'-'.$daysInMonths));
         
        $monthStartDay = date('N',strtotime($year.'-'.$month.'-01'));
         
        if($monthEndingDay<$monthStartDay){
             
            $numOfweeks++;
         
        }
         
        return $numOfweeks;
    }
 
    /**
    * calculate number of days in a particular month
    */
    private function _daysInMonth($month=null,$year=null){
         
        if(null==($year))
            $year =  date("Y",time()); 
 
        if(null==($month))
            $month = date("m",time());
             
        return date('t',strtotime($year.'-'.$month.'-01'));
    }

    public function availabilityUpdate(Request $request){
        $roomid = $request->input('roomid');
        $date  = $request->input('date');
        $count = $request->input('room_count');

       

        $availabilitycount =  Availability :: where('room_id',$roomid)->where('start_date',$date)->first();
        

        try{
            if($availabilitycount == ''){
                $room_availability                      = new Availability();
                    $room_availability->room_id             = $roomid;
                    $room_availability->available_room      = $count;
                    $room_availability->start_date          = $date;
                    $room_availability->save();
                
            }else{
                Availability::where('id',$availabilitycount->id)->update(['available_room'=>$count]);
            }
       
        $result =  array('status' =>1,
        'message' => 'Room Count Update Successfully');
        }
        catch(\Exception $e){
            $result =  array('status' =>0,
            'message' => $e->getMessage());
            Log::warning('roomavailabilty: '.$e->getMessage());
        }
       

       return $result;
    }

    public function availabiltybulkupdate(Request $request){
        //dd($request->input());
        $earlier = new DateTime("2010-07-06");
        $later = new DateTime("2010-07-09");
        $room_id = $request->roomid;
        $dates= $this->date_range($request->start_date, $request->end_date, "+1 day", "Y-m-d",$room_id,$request->availabilty);
        $result =  array('status' =>1,
        'message' => 'Room Count Update Successfully');
        return $result;
       

      
       


    }
    public function date_range($first, $last, $step = '+1 day', $output_format = 'Y-m-d',$room_id,$count ) {
        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);
    
        while( $current <= $last ) {
            $finddate =  date($output_format, $current);
          
            $availabilitycount =  Availability :: where('room_id',$room_id)->where('start_date',$finddate)->first();
        

            if($availabilitycount == ''){
                $room_availability                      = new Availability();
                    $room_availability->room_id             = $room_id;
                    $room_availability->available_room      = $count;
                    $room_availability->start_date          = $finddate;
                    $room_availability->save();
                
            }else{
                Availability::where('id',$availabilitycount->id)->update(['available_room'=>$count]);
            }

            $dates[] = date($output_format, $current);
            $current = strtotime($step, $current);
        }
    
        return $dates;

    }
   
 



   
}
