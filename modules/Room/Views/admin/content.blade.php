
<?php  $propertycollection = \Modules\Property\Models\Property::all(); ?>
@include('admin.message')


<div class="panel">
    <div class="panel-title"><strong>{{__("Room Content")}}</strong></div>
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
    <div class="panel-title"><strong>{{__("Room Info")}}</strong></div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{__("Room Name")}}</label>
                    <input type="text" value="{{(isset($editrow)) ? $editrow->name : ''}}" placeholder="{{__('Example: Room Name')}}" name="name" class="form-control" min="0">
                </div>
            </div>
            @php
            $i =0;
            @endphp
            @foreach ($attributes as $attribute)
            @php 
           
            $firstattr =  $strdatareplace = str_replace("-", "_", $attribute->slug);
            @endphp
            @if($attribute->room_Property ==1)
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{$attribute->name}}</label>
                    <select name='{{str_replace("-", "_", $attribute->slug)}}' class="form-control">
                        <option value="">{{__("-- Please Select --")}}</option>
                        @foreach($attribute->terms as $term)
                        <?php
                        
                        if(isset($editrow)){
                            if(isset($editrow)){
                                $roominfoarr = json_decode($editrow->room_info);
                               
                                 foreach($roominfoarr as $roomdata =>$val){
                                    $strdatareplace = str_replace("-", "_", $attribute->slug);
                                   
                                    if(isset($roominfoarr[$i]->$strdatareplace) && ($roominfoarr[$i]->$strdatareplace == $term->id )){
                                        $selected ='selected' ;
                                       
                                    }else{
                                        $selected ='';
                                    }
                                   
                                }
                            }
                        }else{
                            $selected = '';
                        }
                       
                      
                        printf("<option value='%s' %s>%s</option>", $term->id, $selected,  ' ' . $term->name);
                        ?>
                         @endforeach


                    </select>
                    
                     
                </div>
            </div>
            @php
           $i++;
           @endphp
           @endif
           @endforeach
           
        </div>
    </div>
</div>
@endif
@if(is_default_lang())
<div class="panel">
    <div class="panel-title"><strong>{{__("Amenities details")}}</strong></div>
    <div class="panel-body">
        <div class="row">
        @php
            $j =0;
            @endphp
        @foreach ($attributes as $attribute)
            @if($attribute->features_enable == 1)
            <div class="col-md-4">
                <div class="form-group">
                    <?php
                     if(isset($editrow)){
                        if(isset($editrow)){
                            $roominfoarr = json_decode($editrow->amenities_details);
                           
                           
                             foreach($roominfoarr as $roomdata =>$val){
                                $strdatareplace = str_replace("-", "_", $attribute->slug);
                               
                               
                                if(isset($roominfoarr[$j]->$strdatareplace) && ($roominfoarr[$j]->$strdatareplace != '' )){
                                    $checked = 'checked';
                                   
                                   if($roominfoarr[$j]->$strdatareplace != []){
                                    $show = explode(',',$roominfoarr[$j]->$strdatareplace);
                                   }else{
                                    $show = array();
                                   }
                                  
                                    $style = 'display: block;';
                                   
                                }else{
                                   $checked = '';
                                   $show =array();
                                   $style = 'display: none;';
                                }
                               
                            }
                        }
                    }else{
                        $checked = '';
                        $style = 'display: none;';
                        $show =array();
                    }
                    ?>

                    <label><input type="checkbox" value = "{{$attribute->name}}"  name='{{str_replace("-", "_", $attribute->slug)}}' data-value = '{{str_replace("-", "_", $attribute->slug)}}' data-id = "{{$attribute->id}}" data-attributes = "{{$attribute->name}}"data-show = "{{$attribute->features_choice}}" class="form-control amenities_details"{{$checked}}>{{$attribute->name}}
                                        </label>
                </div>
                    <div class = 'form-group show_choice {{str_replace("-", "_", $attribute->slug)}}_{{$attribute->id}}' style ="{{$style}}">
                    @foreach($attribute->terms as $term)

                   <?php
                   if(in_array($term->name,$show)){
                    $choice_checked = 'checked';
                   }else{
                    $choice_checked = '';
                   }
                   ?>
                    
                        <label><input type = "checkbox" name= '{{str_replace("-", "_", $attribute->slug)}}_choice[]' class ="amenities {{$attribute->name}}_{{$attribute->id}}" value = "{{$term->name}}"  {{$choice_checked}}>{{$term->name}}</label>
                    @endforeach    
                    </div>


            </div>
            @php
           $j++;
           @endphp
            @endif
           
            @endforeach 
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-title"><strong>{{__("Priceing details")}}</strong></div>
    <div class="panel-body">
        <div class="row">
      
            <div class="col-md-4">
                <div class="form-group">
                    <label>No Of Rooms</label>
                    <input type="text" value = "{{$editrow->no_of_room ?? ''}}"   name="no_of_room" class="form-control">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Price Per Month</label>
                    <input type="text" value = "{{$editrow->price_per_month ?? ''}}"   name="price_per_month" class="form-control">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Deposit</label>
                    <input type="text" value = "{{$editrow->deposite ?? ''}}"   name="deposite" class="form-control">
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
<script>
$( document ).ready(function() {
    $('.amenities_details').click(function() {
        var showdata = $(this).data('show');
        var dataname = $(this).val();
        var dataid   = $(this).data('id');
        var datavalue   = $(this).data('value');
        if(showdata == '1'){
            $('.'+datavalue+'_'+dataid).css("display","block");
            //alert(dataname+'_'+dataid);
        }
      
    });
});
</script>
@endsection

