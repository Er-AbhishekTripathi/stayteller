@extends('layouts.app')
@section('head')
<link href="{{ asset('module/booking/css/checkout.css?_ver='.config('app.asset_version')) }}" rel="stylesheet">
@endsection
@section('content')
<div class="bravo-booking-page padding-content">
    <div class="container">
        <div id="bravo-checkout-page">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="form-title">{{__('Booking Submission')}}</h3>
                    <div class="booking-form">
                        @include ($service->checkout_form_file ?? 'Booking::frontend/booking/checkout-form')

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="booking-detail">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('footer')
<!-- <script src="{{ asset('module/booking/js/checkout.js') }}"></script> -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script type="text/javascript">
    var SITEURL = '{{URL::to('')}}';
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('body').on('click', '.buy_now', function(e) {
        var totalAmount = $(this).attr("data-amount");
        var product_id = $(this).attr("data-id");
        var options = {
            "key": "rzp_test_azPgcSgBVgwPkR",
            "amount": (totalAmount * 100), // 2000 paise = INR 20
            "name": "stayteller",
            "description": "Payment",
            "image": "//www.tutsmake.com/wp-content/uploads/2018/12/cropped-favicon-1024-1-180x180.png",
            "handler": function(response) {
                window.location.href = SITEURL + '/' + 'property/booked/paysuccess?payment_id=' + response.razorpay_payment_id + '&product_id=' + product_id + '&amount=' + totalAmount;
            },
            "prefill": {
                "contact": '9988665544',
                "email": 'tutsmake@gmail.com',
            },
            "theme": {
                "color": "#528FF0"
            }
        };
        var rzp1 = new Razorpay(options);
        rzp1.open();
        e.preventDefault();
    });

    // jQuery(function () {
    //     $.ajax({
    //         'url': bookingCore.url + '/booking/1/check-status',
    //         'cache': false,
    //         'type': 'GET',
    //         success: function (data) {
    //             if (data.redirect !== undefined && data.redirect) {
    //                 window.location.href = data.redirect
    //             }
    //         }
    //     });
    // })
</script>
@endsection