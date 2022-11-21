<?php
namespace Modules\Room\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\AdminController;
use Modules\Room\Models\Room;
use Modules\Room\Models\Availability;
use Modules\Review\Models\Review;
use Modules\Property\Models\PropertyTranslation;
use Modules\Property\Models\PropertyCategory;
use Modules\Location\Models\Location;
use Modules\Core\Models\Attributes;
use Modules\Property\Models\Property;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RoomController extends AdminController
{
    protected $propertyTranslationClass;
    protected $propertyCategoryClass;
    protected $locationClass;
    protected $attributesClass;
    protected $propertyClass;
    protected $roomClass;
    
    public function __construct()
    {
       
        $this->setActiveMenu('admin/module/rooms');
        parent::__construct();
        $this->propertyTranslationClass = PropertyTranslation::class;
        $this->propertyCategoryClass = PropertyCategory::class;
        $this->locationClass = Location::class;
        $this->attributesClass = Attributes::class;
        $this->propertyClass = Property::class;
        $this->roomClass      = Room :: class;
    }

     /********************* PROPERTY ********************/  
     private $dayLabels = array("Mon","Tue","Wed","Thu","Fri","Sat","Sun");
     
     private $currentYear=0;
      
     private $currentMonth=0;
      
     private $currentDay=0;
      
     private $currentDate=null;
      
     private $daysInMonth=0;
      
     private $naviHref= null;

    public function index(Request $request)
    {
      
        $this->checkPermission("rooms_manage_others");
        $this->checkPermission('property_view');
        $user_id = Auth::id();
        $rows = $this->roomClass::query()->select("bravo_rooms.*","bravo_properties.*","bravo_rooms.id as roomid")
                                                        ->leftJoin('bravo_properties', function ($join)  {
                                                      $join->on('bravo_properties.id', '=', 'bravo_rooms.property_id');
                                                     });

      

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

      
       
        return view('Room::admin.index', $data);
    
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

    public function bulkEdit(Request $request)
    {
        $this->checkPermission("review_manage_others");
        $ids = $request->input('ids');
        $action = $request->input('action');
        if (empty($ids) or !is_array($ids)) {
            return redirect()->back()->with('error', __('No items selected!'));
        }
        if (empty($action)) {
            return redirect()->back()->with('error', __('Please select an action!'));
        }
        $allServices = get_bookable_services();
        if ($action == "delete") {
            foreach ($ids as $id) {
                $review = Review::where('id', $id)->first();
                if(!empty($review)){
                    $review->delete();
                    $review->save();
                    $module_class = $allServices[$review->object_model] ?? false;
                    if(!empty($module_class)){
                        $model_serivce = $module_class::withTrashed()->find($review->object_id);
                        if(!empty($model_serivce)){
                            $model_serivce->update_service_rate();
                        }
                    }
                }
            }
        } else {
            foreach ($ids as $id) {
                $review = Review::where('id', $id)->first();
                $review->status = $action;
                $review->save();
                $module_class = $allServices[$review->object_model] ?? false;
                if(!empty($module_class)){
                    $model_serivce = $module_class::withTrashed()->find($review->object_id);
                    if(!empty($model_serivce)){
                        $model_serivce->update_service_rate();
                    }
                }
            }
        }
        return redirect()->back()->with('success', __('Update success!'));
    }

    public function createroom(Request $request){
       
        $this->checkPermission('rooms_create');

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
       
        return view('Room::admin.create', $data);
    }
    public function store(Request $request , $id){

        //dd($request->input());
        $sharing_type = $request->sharing_type;

        switch( $request->sharing_type) {
            case('33'):
 
                $sharingtype  = 1;
 
                break;
 
            case('34'):
                 
                $sharingtype  = 2;
 
                break;
            case('35'):
                 
                    $sharingtype  = 3;
     
                    break;    
            case('36'):
                 
                    $sharingtype  = 4;
     
                    break;    
            case('37'):
                 
                    $sharingtype  = 5;
     
                    break;    
            case('38'):
                 
                    $sharingtype  = 6;
     
                    break;    
            case('39'):
                 
                    $sharingtype  = 10;
     
                    break;    
 
            default:
                $sharingtype = '0.';
        }
       
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
        $room->deposite             = $request->deposite;
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
            $room_availability->available_room      = $room->no_of_room * $sharingtype;
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

       return view('Room::admin.create', $data);
       
    }

    public function vacancyupdate (Request $request, $id){

      
        

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
       

    $data = array('html'=> $content);

    return view('Room::admin.vacancy', $data);

       
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
            $input_tag = '<button type="button" class="btn btn-primary availabiltyupdate" data-availability = "'.$availablitydatacount.'" data-room_id = "'.$id.'" data-date = "'.$this->currentDate.'" data-toggle="modal" data-target="#exampleModal" style="width: 50px;height: 25px;">
           update
          </button>' ;
        }else if($cellContent != ''){
            $availabilitycountcollection =  Availability :: where('room_id',$id)->where('start_date',$this->currentDay)->first();
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
}
