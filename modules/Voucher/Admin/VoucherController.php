<?php
namespace Modules\Voucher\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Modules\AdminController;
use Modules\Voucher\Models\Voucher;


class VoucherController extends AdminController
{
    protected $voucher;
    public function __construct()
    {
        $this->setActiveMenu('admin/module/voucher');
        parent::__construct();
        $this->voucher = Voucher::class;
    }

    public function index(Request $request)
    {
       
        $this->checkPermission("voucher_manage");
        $model = Voucher::query();
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
                ['name'  => __('Voucher'),
                 'class' => 'active'
                ],
            ]
        ];
        return view('Voucher::admin.index', $data);
    }

    public function bulkEdit(Request $request)
    {
        $this->checkPermission("voucher_manage");
        $ids = $request->input('ids');
        $action = $request->input('action');
        if (empty($ids) or !is_array($ids)) {
            return redirect()->back()->with('error', __('No items selected!'));
        }
        if (empty($action)) {
            return redirect()->back()->with('error', __('Please select an action!'));
        }
      
       
        if ($action == "delete") {
            foreach ($ids as $id) {
                $voucher = Voucher::where('id', $id)->first();
                if(!empty($voucher)){
                    $voucher->delete();
                    $voucher->save();
                   
                  
                }
            }
        } else {
            foreach ($ids as $id) {
                $voucher = Voucher::where('id', $id)->first();
                $voucher->status = $action;
                $voucher->save();
                
            }
        }
        return redirect()->back()->with('success', __('Update success!'));
    }

     public function create(Request $request)
    {
        $this->checkPermission('voucher_create');
        $row = new $this->voucher();
         $row->fill([
            'status' => 'publish'
         ]);
        $data = [
            'row'            => $row,
           
            'breadcrumbs'    => [
                [
                    'name' => __('Voucher'),
                    'url'  => 'admin/module/voucher'
                ],
                [
                    'name'  => __('Add Voucher'),
                    'class' => 'active'
                ],
            ],
            'page_title'     => __("Add new Voucher")
        ];
        return view('Voucher::admin.create', $data);
    }
    public function store(Request $request , $id){
        $id = $request->input('id');
        if ($id) {
            $voucher = Voucher::find($id);
            if (empty($voucher)) {
                return redirect()->back()->with('error', __('Voucher not found!'));
            }
        }else{
            $voucher                       = new Voucher();

        }
        $voucher->property_id          = $request->property_id;
        $voucher->code                 = $request->code;
        $voucher->start_date            = $request->start_date;
        $voucher->end_date              = $request->end_date;
        $voucher->object_model           = 'property';
        $voucher->amount                = $request->amount;
        $voucher->status                = 'publish';
        $voucher->create_user          = Auth::id();
        $voucher->update_user          =  Auth::id();
        $voucher->save();

        return back()->with('success', ($id and $id>0) ? __('Voucher updated'):__("Voucher created"));
    }
}
