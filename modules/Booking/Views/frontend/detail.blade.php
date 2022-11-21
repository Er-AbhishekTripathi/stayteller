@extends('layouts.app') 
@section('head') 
    <link href="{{ asset('module/booking/css/checkout.css?_ver='.config('app.asset_version')) }}" rel="stylesheet">
@endsection
@section('content')

    <div class="bravo-booking-page padding-content" >
        <div class="container">
            <div class="row booking-success-notice">
                <div class="col-lg-8 col-md-8">
                    <div class="d-flex align-items-center">
                        <img src="{{url('images/ico_success.svg')}}" alt="Payment Success">
                        <div class="notice-success">
                            <p class="line1"><span>Rasj,</span>
                                {{__('your order was submitted successfully!')}}
                            </p>
                        <p class="line2">{{__('Booking details has been sent to:')}} <span>aa@gmail.com</span></p>
                        </div>

                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <ul class="booking-info-detail">
                        <li><span>{{__('Booking Number')}}:</span> 1212</li>
                        <li><span>{{__('Booking Date')}}:</span> fdgdfh</li>
                       
                        <li><span>{{__('Payment Method')}}:</span> fdhdfh</li>
                      
                        <li><span>{{__('Booking Status')}}:</span> fdhdfghj</li>
                    </ul>
                </div>
            </div>
            <div class="row booking-success-detail">
                <div class="col-md-8">
                    
                    <div class="text-center">
                        <a href="{{route('user.booking_history')}}" class="btn btn-primary">{{__('Booking History')}}</a>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
@endsection
@section('footer')
@endsection
