@extends('layouts.app')
@section('head')
@endsection
@section('content')
    <style type="text/css">
        .cursor-pointer {
            cursor: pointer;
        }

        .bg-info.bg-lighten {
            background-color: #e5f4f7 !important;
        }

        .card-footer {
            background-color: #fff;
        }

        .btn-outline-secondary {
            color: #6c757d !important;
        }

        .btn-outline-secondary:hover {
            color: #fff !important;
        }

        .roomAvailabilityCalendar {
            position: relative !important;
        }

        .roomAvailabilityCalendar-actionContainer-left,
        .roomAvailabilityCalendar-actionContainer-right {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
        }

        .roomAvailabilityCalendar-actionContainer-left {
            left: -5px;
        }

        .roomAvailabilityCalendar-actionContainer-right {
            right: -5px;
        }

        .roomAvailabilityCalendar-date:hover {
            background-color: #80808026;
        }

        .col-1-7 {
            flex: 14%;
            max-width: 14%;
            position: relative;
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
        }
        .col-1-7.roomAvailabilityCalendar-date.text-center.activeRoomdata {
    background: #fafafa;
    border: 1px solid #e1e1e1;
    border-radius: 8px;
}
        .hideclass.text-thm.fz14 {
    text-decoration: none;
    -webkit-font-smoothing: antialiased;
    font-weight: 400;
}
.fz14 {
    font-size: 11px;
    font-weight: bold;
    cursor: pointer;
}

.hideclass.text-thm.fz14 {
    text-decoration: none;
    -webkit-font-smoothing: antialiased;
    font-weight: bold;
    font-size: 10px;
}
    

.topnav {
  overflow: hidden;
  background-color: #f1f1f1;
  border: 3px solid #ff5a5f;
}

.topnav a {
  float: left;
  display: block;
  color: black;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  font-size: 17px;
  border-bottom: 3px solid transparent;
}

.topnav a:hover {
  border-bottom: 3px solid red;
}

.topnav a.active {
  border-bottom: 3px solid red;
}



    </style>

    <section class="listing-title-area p-0">
        @include('Property::frontend.layouts.details.gallery_property')
    </section>

    <!-- Agent Single Grid View -->
    <section class="our-agent-single bgc-f7 pb30-991 bg-white">
        <div class="container">
       
            @include('Agencies::frontend.detail.search_mobile')
            <div class="row">
                <div class="col-sm-6">
                    <div class="d-flex flex-column">
                        @if(!empty($row['content']))
                                <div class="text-danger mb30 room_header mb-1">
                                    <div class="float-left">{{ $translation->title ?? '' }}</div>
                                    <div class="float-right">
                                        <span class= "room_price">{{ $row['display_price'] ? $row['display_price'] : '' }}/{{__("Month")}}</span>
                                        @if($row->is_sold)
                                            <span class="badge badge-danger is_sold_out">{{__("Sold Out")}}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="gpara second_para white_goverlay mt10 mb10">{!! clean(Str::words($translation->content,50)) !!}</div>
                                <div class="collapse" id="collapseExample">
                                    <div class="card card-body border-0 p-0">
                                        <div class="mb10">{!! clean($translation->content) !!}</div>
                                    </div>
                                </div>
                                <p class="overlay_close">
                                    <a class="text-thm fz14 " data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample" onclick="showLess(this)">
                                    SHOW MORE
                                    </a>
                                </p>
                        @endif
                    </div>
                </div>
                <div class="col-sm-4">
                   
                                @include('Property::frontend.layouts.details.amenities')   
                               
            </div>

        </div>
    </section>

    <section class="our-agent-single bgc-f7 pb30-991 bg-info bg-lighten">
        <div class="container">
            <div >
           
            <nav class="topnav">
                                <button><a href="#roomdetails" class="scrollTo active">Room Details</a></button>
                                <button><a href="#location" class="scrollTo">Location</a></button>
                                @if (!empty($row['video']))<button><a href="#video" class="scrollTo">Property Video</a></button>@endif
                                <button><a href="#policy" class="scrollTo">Property Policy</a></button>
                                <button><a href="#review" class="scrollTo">Reviews</a></button>
                            </nav>
            </div>
           

            <div class="row" id = "roomdetails">
                <div class="col-sm-8 mt50">
                    <div class="listing_single_description style2 bg-transparent p-0 border-0">
                        @if(!empty($row['content']))
                            <?php
                            $roomsCount = 0;
                            if(count($rooms) > 0){

                            foreach($rooms as $roomdatainfo) {
                                $i =0;
                                $j=0;
                                $show=array();
                                $selected=array();

                            ?>
                             @php
            $totalbed = Modules\Room\Models\Availability:: where('start_date',date('Y-m-d'))->where('room_id',$roomdatainfo->id)->first();
          
            @endphp

                            <div class="row @if($roomsCount > 0) mt-3 @endif">
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm cursor-pointer roomContainer">
                                        <div class="card-body">
                                            <div class="row">
                                               
                                                    <div class="col-md-6 col-sm-6 com-lg-6">
                                                    <h4 class="font-weight-bold">{{$roomdatainfo->name}} </h4>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6 com-lg-6 ">
                                                    <h4 class="font-weight-bold float-right">Total Bed :{{$totalbed->available_room ?? 'Sold Out'}}</h4>
                                                    </div>

                                                    
                                                
                                            </div>

                                            <?php

                                            foreach ($attributes as $attribute) {
                                                if($attribute->room_Property ==1) {
                                                    foreach($attribute->terms as $term) {
                                                        $roominfoarr = json_decode($roomdatainfo->room_info);

                                                        foreach($roominfoarr as $roomdata =>$val) {
                                                            $strdatareplace = str_replace("-", "_", $attribute->slug);
                                                            if(isset($roominfoarr[$i]->$strdatareplace) && ($roominfoarr[$i]->$strdatareplace == $term->id )){
                                                                $selected[] ='<div class="col-sm-6"><strong>'.$attribute->name.'</strong></div><div class="col-sm-6"> '.$term->name.'</div>' ;
                                                            }
                                                        }
                                                    }

                                                    $i++;
                                                }

                                                if($attribute->features_enable == 1) {
                                                    foreach($attribute->terms as $term) {
                                                        $roominfoarr = json_decode($roomdatainfo->amenities_details);
                                                        foreach($roominfoarr as $roomdata =>$val){
                                                            $strdatareplace = str_replace("-", "_", $attribute->slug);
                                                           
                                                            if(isset($roominfoarr[$j]->$strdatareplace) && ($roominfoarr[$j]->$strdatareplace != '' )){
                                                                $checked = 'checked';
                                                                if($roominfoarr[$j]->$strdatareplace != []){
                                                                    $show[] ='<div class="col-sm-6"><strong>'.$attribute->name.' </strong> </div><div class="col-sm-6">'.$roominfoarr[$j]->$strdatareplace.'</div>';
                                                                }else{
                                                                    $show[] =$attribute->name;
                                                                }
                                                               
                                                                $style = 'display: block;';
                                                            
                                                            }else{
                                                               
                                                                $show[] =$attribute->name.'-'.$j;
                                                            }
                                                        
                                                        }
                                                    }

                                                    $j++;
                                                }
                                            }

                                            $facilities = array_unique($show);
                                            $roomoption = array_unique($selected);

                                            ?>

                                            <div class="row">
                                                <div class="col-sm-5">
                                                    <div class="row">
                                                        
                                                        
                                                   
                                                    @foreach($roomoption as $roomdata)
                                                    {!! $roomdata !!}
                                                    @endforeach
                                                    </div>
                                                </div>
                                                <div class="col-sm-5">
                                                        <div class="row">
                                                    @foreach($facilities as $fact)
                                                    {!! $fact !!}
                                                    @endforeach
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <ul class="list-inline-item mb-0">
                                                        <li><b>{{ __('Rent') }} :</b></li>
                                                        @if($roomdatainfo->refundable == '1')  <li><p>{{ __('Deposit') }} :</p></li> @endif
                                                    </ul>
                                                    <ul class="list-inline-item mb-0">
                                                        <li><b><span>{{ $roomdatainfo->price_per_month ? $roomdatainfo->price_per_month : __('None') }}</span></b></li>
                                                        @if($roomdatainfo->refundable == '1')  <li><p><span>{{$row['deposit'] ? $row['deposit'] : 0 }}</span></p></li> @endif
                                                        <li>
                                                       
                                                        <li>
                                                    </ul>
                                                    
                                                    @if($roomdatainfo->refundable == '1') 
                                                    <b>Refundable</b>
                                                @endif
                                                    <input type="number" id="tentacles" name="tentacles" value = "1" min="1" max="12">
                                                    <?php 
                                                    
                                                    $user = auth()->user();
                                                    $roles = isset($user) ? $user->getRoleNames() : '' ;
                                                    
                                                    ?>
                                                    @if(isset($roles[0]) && (trim($roles[0]) != 'Owner'))
                                                    @if($totalbed != '' )
                                                   
                                                            <a class="btn btn-danger bravo-button-book-mobile text-white " data-roomid = "{{$roomdatainfo->id}}" data-propertyid = "{{$roomdatainfo->property_id}}">{{__("Book Now")}}</a>
                                                        @else
                                                            <a class="btn btn-block btn-thm" data-toggle="modal" data-target="#enquiry_form_modal">{{__("Sold Out")}}</a>
                                                        @endif
                                                        @else
                                                        
                                                        @if($totalbed != '' && $roles == ''  )
                                                            <a class="btn btn-danger bravo-button-book-mobile text-white " data-roomid = "{{$roomdatainfo->id}}" data-propertyid = "{{$roomdatainfo->property_id}}">{{__("Book Now")}}</a>
                                                        @else
                                                            <a class="btn btn-block btn-thm" data-toggle="modal" data-target="#enquiry_form_modal">{{__("Sold Out")}}</a>
                                                        @endif
                                                        @endif
                                                </div>
                                               
                                                <span class="text-xs flex items-center font-semibold text-orange cursor-pointer btnBookRoom" data-roomid = "{{$roomdatainfo->id}}" data-propertyid = "{{$roomdatainfo->property_id}}">Availability calendar<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 ml-1"><path d="M18 15l-6-6-6 6"></path></svg></span>
                                            </div>

                                        </div><!-- card-body -->
                                        <div class="card-footer d-none">
                                            <div class="row">
                                                <div class="col-12">
                                                    <b class="text-danger">Availability</b>
                                                    <a href="javascript:void(0);" class="btn btn-link py-0 text-secondary float-right btnCancelRoomBooking">{{__("Cancel")}}</a>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 roomAvailabilityCalendar"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                    
                            <?php

                                $roomsCount++;
                            }

                        }else{ ?>
                        <div class="col-sm-4 mt50">
                  
                    
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                <div class="flex flex-col items-center justify-center w-72">
                                    <img src="https://book.zostel.com/static/media/gray-zobu.018014d9.svg" alt="zobu" class="h-40">
                                    <span class="mt-4 font-medium text-lg text-subtitle">All rooms booked</span>
                                </div> 
                                </div>
                            </div>
                        </div>
                </div>
            </div>


                       <?php  }

                            ?>
                               
                        @endif
                    </div>
                </div>
                <div class="col-sm-4 mt50">
                   <!-- <h3 class="font-bold text-2xl">Summary</h3>
                    <div class="text-sm font-bold">1 night <span class="text-text">starting from</span> Wed 19 Oct, 2022</div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                <div class="flex flex-col items-center justify-center w-72">
                                    <img src="https://book.zostel.com/static/media/gray-zobu.018014d9.svg" alt="zobu" class="h-40">
                                    <span class="mt-4 font-medium text-lg text-subtitle">No room selected</span>
                                </div> 
                                </div>
                            </div>
                        </div>
                </div>-->
            </div>
           
        </div>
    </section>

    <section class="our-agent-single bgc-f7 pb30-991">
        <div class="container">

            <div class="row">

            <div class="col-lg-12 hiperlink" id = "location">
                <div class="shop_single_tab_content style2 mt30">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="description-tab" data-toggle="tab" href="#description" role="tab" aria-controls="description" aria-selected="true">Location</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent2">
                        <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                        @if(!empty($row->location->name))
                                        @php $location =  $row->location->translateOrOrigin(app()->getLocale());
                                    
                                        @endphp
                                    @endif
                                    <div class="row">
                                    <div class ="col-md-4 col-sm-4 col-lg-4">
                                        <div class="application_statics ">
                                            <h4 class="mb30">{{ __("Locate Us") }} <small class="application_statics_location float-right">{{ !empty($location->name) ? $location->name : '' }}</small></h4>
                                            <div class="property_video p0">
                                            <span class="font-medium text-text text-sm whitespace-pre-line"><strong>Address:</strong><br><div class="whitespace-pre-line html-renderer-div"><p>{{$row->address}}</p></div></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-8 hiperlink">
                                    <div class="h400" id="map-canvas"></div>
                                                
                                    </div>
                                    </div>
                        </div>
                    </div>
                </div>
            </div>



                
                        @include('Property::frontend.layouts.details.property_video')
                        <div class="col-lg-12 hiperlink" id = "review" >
                            @include('Agencies::frontend.detail.review', ['review_service_id' => $row['id'], 'review_service_type' => 'property'])
                        </div>
                        <div class="col-lg-12 hiperlink" id = "policy">
                            <div class="shop_single_tab_content style2 mt30">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="description-tab" data-toggle="tab" href="#description" role="tab" aria-controls="description" aria-selected="true">Property Policy</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent2">
                                    <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                                    <section class="" id="policy1"><section class="max-w-screen-xl w-full mx-auto p-4"><div class="whitespace-pre-line html-renderer-div"><p>- Guests are required to pay a 21% advance at the time of booking itself.</p><p>- Our standard check-in time is 1 PM and the standard check-out time is 11 AM.</p><p>- We only accept a government ID as valid identification proof. No local IDs shall be accepted at the time of check-in.</p><p>- Guests are not permitted to bring outsiders inside the home premises.</p><p>- We believe in self-help and do not provide luggage assistance or room services.</p><p>- Usage of alcohol and drugs is strictly banned inside and around the property.</p><p>- Quiet Hours are from 10 PM to 6 AM. Do not play loud music or cause nuisance, as the place is located in a village area. Please respect neighbours and culture around.</p><p>- Right to admission reserved.</p></div></section></section>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @include('Property::frontend.layouts.details.property-related')
                    </div>
                </div>

              
            </div>
        </div>
    </section>
@endsection
@section('footer')
    <link href="{{ asset('libs/fotorama/fotorama.css') }}" rel="stylesheet">
    <script src="{{ asset('libs/fotorama/fotorama.js') }}"></script>
    {!! App\Helpers\MapEngine::scripts() !!}
    <script>
        function showLess(event) {  
           
           
   if (event.innerText == 'SHOW MORE') {

        event.innerText = "SHOW LESS";
    } else if (event.innerText == "SHOW LESS") {
        event.innerText = "SHOW MORE";
    }
}

function showamenities(event) { 
    var dataclass= event.getAttribute('data-class');
    
  if (event.innerText == 'SHOW MORE') {
   
   $('.'+dataclass).css("display", "block");
       event.innerText = "SHOW LESS";
   } else if (event.innerText == "SHOW LESS") {

    $('.'+dataclass).css("display", "none");
       event.innerText = "SHOW MORE";
   }
}

         var ajaxReady = 1;
        function addDaysToDateObj(dateObj, days) {
            return new Date(new Date(dateObj.getFullYear(), dateObj.getMonth(), dateObj.getDate()).setDate(new Date(dateObj.getFullYear(), dateObj.getMonth(), dateObj.getDate()).getDate() + days));
        }

        function dateStrToObj(dateStr) {
            let dateArray = dateStr.split('-');
            return new Date(dateArray[0], dateArray[1] - 1, dateArray[2]);
        }

        function dateObjToStr(dateObj, humanReadable = false) {
            if (humanReadable) {
                return `${dateObj.getDate()}-${dateObj.getMonth() + 1}-${dateObj.getFullYear()}`;
            }

            return `${dateObj.getFullYear()}-${dateObj.getMonth() + 1}-${dateObj.getDate()}`;
        }

        // Temporary function - remove when implementing actual data
        function generateRandomRoomAvailabilityData(btnb) {
            let n=90;
           let roomid = btnb.data('roomid');
            let propertyid = btnb.data('propertyid');
            
            $.ajax({
                    url: "{{route('property.availabilty')}}",
                    data: {
                        roomid: roomid,
                        propertyid: propertyid,
                        _token: "{{csrf_token()}}",
                    },
                    dataType: 'json',
                    type: 'post',
                    beforeSend: function (xhr) {
                        ajaxReady = 0;
                    },
                    success: function (res) {

                        renderRoomAvailabilityCalendar(btnb.closest('.roomContainer').find('.roomAvailabilityCalendar'),res);
                       
                        
    
                    },
                    error:function () {
                        ajaxReady = 1;
                    }
                })

           
        }

        /**
         * @param availableDates - should be Array of Objects( date: 'YYYY-MM-DD', fare: AMOUNT )
         * */
        function renderRoomAvailabilityCalendar(calendarContainer, availableDates, startDate = false) {

            const daysInWeek = [
                'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'
            ];
            const months = [
                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
            ];
            let roomContainer = calendarContainer.closest('.roomContainer');

            if (availableDates && Array.isArray(availableDates) && availableDates.length > 0) {
                let availableDatesHTML = '';
                let prevWeekAvailable = false;
                let minDate = new Date();

                availableDates.forEach(function(availableDate, index) {
                    let dateObj = dateStrToObj(availableDate['date']);

                    if (dateObj < minDate) {
                        minDate = dateObj;
                    }

                    availableDate['dateObj'] = dateObj;

                    availableDates[index] = availableDate;
                });

                if (startDate) {
                    prevWeekAvailable = startDate > minDate;
                } else {
                    // Set start date - first time
                    startDate = minDate;

                    calendarContainer.prop('availableDates', availableDates);
                }

                let daysRendered = 0;
                let endDateStr = '';

                while(daysRendered < daysInWeek.length) {
                    let dateObj = addDaysToDateObj(startDate, daysRendered);
                    let dateStr = dateObjToStr(dateObj);
                    let availableDate = null;

                    availableDates.forEach(function(tempAvailableDate) {
                        if (tempAvailableDate['dateObj'].getTime() == dateObj.getTime()) {
                            availableDate = tempAvailableDate;
                            return;
                        }
                    });

                    let calendarHTML =  `<div class="col-1-7 roomAvailabilityCalendar-date text-center" data-date="${dateStr}">`+
                                            `<div class="row">`+
                                                `<div class="col-12">`+
                                                    daysInWeek[dateObj.getDay()]+
                                                `</div>`+
                                            `</div>`+
                                            `<div class="row">`+
                                                `<div class="col-12">`+
                                                    `<b>` + months[dateObj.getMonth()] + ` ` + dateObj.getDate() + `</b>`+
                                                `</div>`+
                                            `</div>`+
                                            `<div class="row">`+
                                                `<div class="col-12 roomAvailablecount">`+
                                                    (availableDate ? `<span class="text-danger ">` + availableDate['fare'] + `</span>` : `-`)+
                                                `</div>`+
                                            `</div>`+
                                        `</div>`;

                    if ((daysInWeek.length - 1) == daysRendered) {
                        let nextWeekStartDate = addDaysToDateObj(dateObj, 1);
                        endDateStr = dateObjToStr(nextWeekStartDate);
                    }

                    availableDatesHTML += calendarHTML;

                    daysRendered++;
                }

                if (prevWeekAvailable) {
                    let prevWeekStartDate = addDaysToDateObj(startDate, daysInWeek.length * -1);
                    let prevWeekStartDateStr = dateObjToStr(prevWeekStartDate);

                    availableDatesHTML +=   `<div class="roomAvailabilityCalendar-actionContainer-left">`+
                                                `<a href="javascript:void(0);" class="btn btn-danger btnShowPreviousWeek" data-date="${prevWeekStartDateStr}"><i class="fa fa-arrow-left"></i></a>`+
                                            `</div>`;
                }

                availableDatesHTML +=   `<div class="roomAvailabilityCalendar-actionContainer-right">`+
                                            `<a href="javascript:void(0);" class="btn btn-danger btnShowNextWeek" data-date="${endDateStr}"><i class="fa fa-arrow-right"></i></a>`+
                                        `</div>`;

                calendarContainer.html(`<div class="row justify-content-center mt-2">${availableDatesHTML}</div>`);
            } else {
                calendarContainer.html('<span class="text-danger"><em>Cannot find available dates</em></span>');
                roomContainer.find('.btnCancelRoomBooking').addClass('d-none');
            }
        }

        jQuery(function ($) {
            new BravoMapEngine('map-canvas', {
                fitBounds: true,
                center: [{{$row->map_lat ?? "51.505"}}, {{$row->map_lng ?? "-0.09"}}],
                zoom:{{$row->map_zoom ?? "8"}},
                ready: function (engineMap) {
                    @if($row->map_lat && $row->map_lng)
                    engineMap.addMarker([{{$row->map_lat}}, {{$row->map_lng}}], {
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
                    engineMap.searchBox($('.bravo_searchbox'),function (dataLatLng) {
                        engineMap.clearMarkers();
                        engineMap.addMarker(dataLatLng, {
                            icon_options: {}
                        });
                        $("input[name=map_lat]").attr("value", dataLatLng[0]);
                        $("input[name=map_lng]").attr("value", dataLatLng[1]);
                    });
                }
            });

            $(document).on('click', '.btnBookRoom', function(e) {
                e.preventDefault();
                e.stopPropagation();

                let roomContainer = $(this).closest('.roomContainer');
               

                roomContainer.find('.card-footer').removeClass('d-none');
                roomContainer.find('.btnCancelRoomBooking').removeClass('d-none');
                $(this).addClass('d-none');


                generateRandomRoomAvailabilityData($(this));

                
            });

            $(document).on('click', '.btnCancelRoomBooking', function(e) {
                e.preventDefault();
                e.stopPropagation();

                let roomContainer = $(this).closest('.roomContainer');
                roomContainer.find('.card-footer').addClass('d-none');
                roomContainer.find('.btnBookRoom').removeClass('d-none');
                $(this).addClass('d-none');
            });

            $(document).on('click', '.roomContainer .roomAvailabilityCalendar .btnShowPreviousWeek, .roomContainer .roomAvailabilityCalendar .btnShowNextWeek', function(e) {
                e.preventDefault();
                e.stopPropagation();

                let calendarContainer = $(this).closest('.roomAvailabilityCalendar');
                let dateObj = dateStrToObj($(this).attr('data-date'));

                renderRoomAvailabilityCalendar(calendarContainer, calendarContainer.prop('availableDates'), dateObj);
            });

            $(document).on('click', '.roomContainer .roomAvailabilityCalendar .roomAvailabilityCalendar-date', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $('.roomAvailabilityCalendar-date').removeClass('activeRoomdata');
                
                let calendarContainer = $(this).closest('.roomAvailabilityCalendar');
                let dateObj = dateStrToObj($(this).attr('data-date'));
                let dateAvailability = null;
                
                let roomid = $(this).closest('.roomContainer').find('.btnBookRoom').attr('data-roomid');
                let propertyid =$(this).closest('.roomContainer').find('.btnBookRoom').attr('data-propertyid');
                var date = new Date(dateObj);
                var formatsate = date.toLocaleDateString();
                var date = new Date(formatsate).toDateString("yyyy-MM-dd");
                (calendarContainer.prop('availableDates') || []).forEach(function(tempDateAvailability) {
                    if (tempDateAvailability['dateObj'].getTime() == dateObj.getTime()) {
                        dateAvailability = tempDateAvailability;
                        return;
                    }
                });

                if (dateAvailability) {
                
                    $(this).addClass('activeRoomdata');
                    $(this).attr("data-availablecount", dateAvailability.fare);
                    $(this).attr("data-roomid", roomid);
                    $(this).attr("data-property", propertyid);
                   
                    
                  
                    
                } else {
                    alert(`Sorry, It is not available on ${dateObjToStr(dateObj, true)}`);
                }
            });
        });
        $(document).on('click', '.bravo-button-book-mobile', function(e) {
            var bookingdate = $('.activeRoomdata').attr('data-date');
            var availablecount = $('.activeRoomdata').attr('data-availablecount');
            var roomid = $('.activeRoomdata').attr('data-roomid');
            var property = $('.activeRoomdata').attr('data-property');
            if (availablecount == null){
                alert('Please Select dete');
                }
                                            
            else if(availablecount == '-'){
                alert('Please Select dete');
            }else{
                $(this).addClass('bookNow');

                var loginid =  '<?php echo Auth::id() ?>' ;
                    if(!(loginid) ){
                       // jQuery('.btn ').click();
                       alert('Please login to book room')
                        }
                    else{
                        window.location.href = 'booked/'+roomid+'/'+property+'/'+bookingdate;
                    }
                
               

                
            }
           

            
           
        })

       

        $('.scrollTo').click(function(){
            var hrefid = $(this).attr( 'href' );

            
           
            $('html, body').animate({
                scrollTop: $($(this).attr('href') ).offset().top
            }, 500);

            return false;
        });


        
    </script>

    
@endsection
