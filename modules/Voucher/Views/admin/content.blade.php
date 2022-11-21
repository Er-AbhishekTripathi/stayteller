
<?php  $propertycollection = \Modules\Property\Models\Property::all(); ?>
@include('admin.message')


<div class="panel">
    <div class="panel-title"><strong>{{__("Voucher Content")}}</strong></div>
    <div class="panel-body">
        <div class="form-group">
            <input type = "hidden" name = 'id' value = "{{isset($editrow->id) ?  $editrow->id : ''}}">
            <label>{{__("Select Property")}}</label>
            <div class="">
                                        <select name="property_id" class="form-control">
                                            <option value="">{{__("-- Please Select --")}}</option>
                                            <?php
                                          
                                                foreach ($propertycollection as $propertydata) {
                                                    if(isset($editrow)){
                                                        if($editrow->property_id == $propertydata->id){
                                                            $selected =  'selected';
                                                        }else{
                                                            $selected =  '' ;
                                                        }
                                                    }else{
                                                        $selected =  '' ;
                                                    }
                                                    printf("<option value='%s' %s>%s</option>", $propertydata->id, $selected,  ' ' . $propertydata->title);
                                                  
                                                }
                                           
                                            ?>
                                        </select>
                                    </div>
        </div>
    </div>
</div>
@if(is_default_lang())
<div class="panel">
    <div class="panel-title"><strong>{{__("Voucher Info")}}</strong></div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{__("Voucher Code")}}</label>
                    <input type="text" value="{{(isset($editrow)) ? $editrow->code : ''}}" placeholder="{{__('Example: Voucher Code')}}" name="code" class="form-control" min="0">
                </div>
            </div>
            
           
        </div>
    </div>
</div>
@endif
@if(is_default_lang())


<div class="panel">
    <div class="panel-title"><strong>{{__("details")}}</strong></div>
    <div class="panel-body">
        <div class="row">
      
            <div class="col-md-4">
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" value = "{{$editrow->start_date ?? ''}}"   name="start_date" class="form-control">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" value = "{{$editrow->end_date ?? ''}}"   name="end_date" class="form-control">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Amount</label>
                    <input type="number" value = "{{$editrow->amount ?? ''}}"   name="amount" class="form-control">
                </div>
            </div>


            
           
        </div>
    </div>
    <div class="panel">
                        
                        <div class="panel-body">
                            <div class="my_profile_setting_input text-center">
                               
                                <button type="submit" class="btn btn2 btn-success">{{isset($editrow) ? __('Update') :  __('Save') }}</button>
                            </div>
                        </div>
                    </div>
</div>
@endif

@section('script.body')

@endsection

