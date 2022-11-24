<?php $container = 1 ?>
@extends('layouts.user')
@section('head')
<style>
    form .error {
        color: #ff0000;
    }
</style>
@endsection
@section('content')
<div class="col-lg-12 mb10">
</div>
<div class="mb-3">
    @if($row->id)
    @include('Language::admin.navigation')
    @endif
</div>
<form name="propertyaction"
    action="{{route('property.vendor.store',['id'=>($row->id) ? $row->id : '-1','lang'=>request()->query('lang')])}}"
    method="post">
    @csrf
    <div class="row">
        <div class="col-sm-9">
            @include('Property::admin.property.content',['hide_gallery'=>true,'property_type'=>1])
            @include('Property::admin.property.location')

            <div class="panel">
                <div class="panel-title"><strong>{{__('Room Info')}}</strong></div>
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{__("Room Name")}}</label>
                            <input type="text" value="{{(isset($editrow)) ? $editrow->name : ''}}"
                                placeholder="{{__(' Room Name')}}" name="room_name[]" class="form-control" >
                        </div>
                    </div>
                    @php
                    $i =0;
                    @endphp
                    @foreach ($attributes as $attribute)
                    @php
                    $firstattr = $strdatareplace = str_replace("-", "_", $attribute->slug);
                    @endphp
                    @if($attribute->room_Property ==1)
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{$attribute->name}}</label>
                            <select name='{{str_replace("-", "_", $attribute->slug)}}[]' class="form-control">
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
                    @endif

                    @php
                    $i++;
                    @endphp
                    @endforeach

                </div>
            </div>
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

                    <label><input type="checkbox" value = "{{$attribute->name}}"  name='{{str_replace("-", "_", $attribute->slug)}}[]' data-value = '{{str_replace("-", "_", $attribute->slug)}}' data-id = "{{$attribute->id}}" data-attributes = "{{$attribute->name}}"data-show = "{{$attribute->features_choice}}" class="form-control amenities_details"{{$checked}}>{{$attribute->name}}
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
    <div class="panel-title"><strong>{{__("Pricing details")}}</strong></div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>No Of Rooms</label>
                    <input type="text" value="{{$editrow->no_of_room ?? ''}}" name="no_of_room[]" class="form-control">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Price Per Month</label>
                    <input type="text" value="{{$editrow->price_per_month ?? ''}}" name="price_per_month[]"
                        class="form-control">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Deposit</label>
                    
                    <input type="number" value="{{$editrow->no_of_room ?? ''}}" name="deposits[]" class="form-control">
                      
                     
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Deposit</label>
                    <div class="form-group">
                        <input id="switch-onColor" type="checkbox"  value="1" name="refundable[]" @if(isset($editrow))
                            {{($editrow->refundable == '1') ? 'checked':''}} @endif>
                        <label>Refundable </label>
                       
                    </div>
                </div>
            </div>
        </div>
     </div>
    </div>  
    
    <div class="panel">
        <div class="panel-body">
            <div id='TextBoxesGroup'>
                <div id="TextBoxDiv1">
                    <label>Textbox #1 : </label><input type='textbox' id='textbox1'>
                </div>
            </div>
            <input type='button' value='Add Button' id='addButton'>
<input type='button' value='Remove Button' id='removeButton'>
        </div>
    </div>
</div>
        
        

        <div class="col-sm-3">
            <div class="panel">
                <div class="panel-title"><strong>{{__('Publish')}}</strong></div>
                <div class="panel-body">
                    <div class="my_profile_setting_input text-center">
                        <button type="submit" class="btn btn2 ">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
            @if(is_default_lang())

            <div class="panel" style="display: none;">
                <div class="panel-title"><strong>{{__("Property type")}}</strong></div>
                <div class="panel-body">
                    <div class="form-group">
                        <div>
                            <label class="cursor-pointer">
                                <input type="radio" name="property_type" id="property_type_buy" value="1"
                                    @if(old('property_type',$row->property_type ?? 0) == 1) checked @endif>
                                {{__("For buy") }}
                            </label>
                        </div>
                        <div>
                            <label class="cursor-pointer">
                                <input type="radio" name="property_type" id="property_type_rent" value="2"
                                    @if(old('property_type',$row->property_type ?? 0) == 2) checked @else checked
                                @endif>
                                {{__("For rent")}}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="panel" style="display: none;">
                <div class="panel-title"><strong>{{__('Sold')}}</strong></div>
                <div class="panel-body">
                    <div class="form-switch">
                        <div>
                            <label>
                                <input type="checkbox" name="is_sold" value="1" @if($row->is_sold) checked @endif>
                                {{__("Sold Out")}}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel" style="display: none;">
                <div class="panel-title"><strong>{{__("Availability")}}</strong></div>
                <div class="panel-body">
                    <div class="form-group">
                        <label>{{__('Property Featured')}}</label>
                        <br>
                        <label>
                            <input type="checkbox" name="is_featured" @if($row->is_featured) checked @endif value="1">
                            {{__("Enable featured")}}
                        </label>
                    </div>
                </div>
            </div>
            <div class="panel panel-image">
                <div class="panel-title"><strong>{{__('Property Image')}}</strong></div>
                <div class="panel-body">
                    {!! \Modules\Media\Helpers\FileHelper::fieldUpload('image_id',$row->image_id) !!}
                </div>
            </div>
            @include('Property::admin.property.attributes')
        </div>
    </div>



</form>

@endsection
@section('script.body')
<script type="text/javascript" src="{{ asset('libs/tinymce/js/tinymce/tinymce.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/condition.js?_ver='.config('app.asset_version')) }}"></script>
<script type="text/javascript" src="{{url('module/core/js/map-engine.js?_ver='.config('app.asset_version'))}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/additional-methods.min.js"></script>

{!! App\Helpers\MapEngine::scripts() !!}
<script type="text/javascript">

$(document).ready(function(){
    var counter = 2;
    $("#addButton").click(function () {
        $i =0;
        if(counter>10){
            alert("Only 10 textboxes allow");
            return false;
        } 
        $.ajax({
    url: "{{route('rooms.info')}}",
    data: {
       
        _token: "{{csrf_token()}}",
    },
    dataType: 'json',
    type: 'get',
    beforeSend: function(xhr) {
        ajaxReady = 0;
    },
    success: function(res) {

      
        var newTextBoxDiv = $(document.createElement('div'))
	     .attr("id", 'TextBoxDiv' + counter);
         newTextBoxDiv.after().html(res+'</div></div>');
          newTextBoxDiv.appendTo("#TextBoxesGroup");
          counter++;



    },
    error: function() {
        ajaxReady = 1;
    }
})


        var newTextBoxDiv = $(document.createElement('div'))
	     .attr("id", 'TextBoxDiv' + counter);
         newTextBoxDiv.after().html('<label>Textbox #'+ counter + ' : </label>' +
	      '');
          newTextBoxDiv.appendTo("#TextBoxesGroup");
          counter++;
     });

     $("#removeButton").click(function () {
	if(counter==1){
          alert("No more textbox to remove");
          return false;
       }   
        
	counter--;
			
        $("#TextBoxDiv" + counter).remove();
			
     });
		
     $("#getButtonValue").click(function () {
		
	var msg = '';
	for(i=1; i<counter; i++){
   	  msg += "\n Textbox #" + i + " : " + $('#textbox' + i).val();
	}
    	  alert(msg);
     });
  });
</script>
<script>

    $(function () {
        // Initialize form validation on the registration form.
        // It has the name attribute "registration"
        $("form[name='propertyaction']").validate({
            // Specify validation rules
            rules: {
                // The key name on the left side is the name attribute
                // of an input field. Validation rules are defined
                // on the right side
                title: "required",
                content: "required",
                price: "required",
                category_id: "required",
                location_id: "required",
                address: "required",
                map_lat: "required",
                map_lng: "required",
                bed: "required",
                bathroom: "required"

            },
            // Specify validation error messages
            messages: {
                title: "Please enter your Title",
                content: "Please enter your Content",
                price: "Please enter your Price",
                category_id: "Please enter your Category",
                location_id: "Please enter your Location",
                address: "Please enter your Address",
                map_lat: "Please enter your lat",
                map_lng: "Please enter your lng",
                bed: "Please enter your bed",
                bathroom: "Please enter your bathroom"

            },
            // Make sure the form is submitted to the destination defined
            // in the "action" attribute of the form when valid
            submitHandler: function (form) {
                form.submit();
            }
        });
    });




    jQuery(function ($) {
        new BravoMapEngine('map_content', {
            fitBounds: true,
            center: [{{ $row-> map_lat ?? "51.505"}}, {{ $row-> map_lng ?? "-0.09"}}],
        zoom: {{ $row-> map_zoom ?? "8"}},
        ready: function (engineMap) {
            @if ($row -> map_lat && $row -> map_lng)
                engineMap.addMarker([{{ $row-> map_lat}}, {{ $row-> map_lng}}], {
        icon_options: {}
    });
    @endif
    engineMap.on('click', function (dataLatLng) {
        engineMap.clearMarkers();
        engineMap.addMarker(dataLatLng, {
            icon_options: {}
        });
        $("input[name=map_lat]").attr("value", dataLatLng[0]);
        $("input[name=map_lng]").attr("value", dataLatLng[1]);
    });
    engineMap.on('zoom_changed', function (zoom) {
        $("input[name=map_zoom]").attr("value", zoom);
    });
    engineMap.searchBox($('.bravo_searchbox'), function (dataLatLng) {
        engineMap.clearMarkers();
        engineMap.addMarker(dataLatLng, {
            icon_options: {}
        });
        $("input[name=map_lat]").attr("value", dataLatLng[0]);
        $("input[name=map_lng]").attr("value", dataLatLng[1]);
    });
                }
            });
        })
</script>
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