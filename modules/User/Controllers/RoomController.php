<?php
namespace Modules\Room\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\AdminController;
use Modules\Room\Models\Room;
use Modules\Review\Models\Review;
use Modules\Property\Models\PropertyTranslation;
use Modules\Property\Models\PropertyCategory;
use Modules\Location\Models\Location;
use Modules\Core\Models\Attributes;
use Modules\Property\Models\Property;


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

    public function index(Request $request)
    {
       dd('muthu');
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
                 $choice = $attribute->slug.'_choice';
                $feature[] = array($strdatareplace => implode(',',$request->$choice));
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
}
