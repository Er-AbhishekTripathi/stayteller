<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{$html_class ?? ''}}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php event(new \Modules\Layout\Events\LayoutBeginHead()); @endphp
    @php
        $favicon = setting_item('site_favicon');
    @endphp
    @if($favicon)
        @php
            $file = (new \Modules\Media\Models\MediaFile())->findById($favicon);
        @endphp
        @if(!empty($file))
            <link rel="icon" type="{{$file['file_type']}}" href="{{asset('uploads/'.$file['file_path'])}}" />
        @else:
            <link rel="icon" type="image/png" href="{{url('images/favicon.png')}}" />
        @endif
    @endif

    @include('Layout::parts.seo-meta')
    <link href="{{ asset('libs/flags/css/flag-icon.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('findhouse/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('findhouse/css/style.css')}}">
    <!-- Responsive stylesheet -->
    <link rel="stylesheet" href="{{asset('findhouse/css/responsive.css')}}">
    <link rel="stylesheet" href="{{asset('dist/frontend/css/frontend.css')}}">
<!-- Title -->
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    {!! \App\Helpers\Assets::css() !!}
    {!! \App\Helpers\Assets::js() !!}
    <script>
        var bookingCore = {
            url:'{{url( app_get_locale() )}}',
            url_root:'{{ url('') }}',
            booking_decimals:{{(int)get_current_currency('currency_no_decimal',2)}},
            thousand_separator:'{{get_current_currency('currency_thousand')}}',
            decimal_separator:'{{get_current_currency('currency_decimal')}}',
            currency_position:'{{get_current_currency('currency_format')}}',
            currency_symbol:'{{currency_symbol()}}',
			currency_rate:'{{get_current_currency('rate',1)}}',
            date_format:'{{get_moment_date_format()}}',
            map_provider:'{{setting_item('map_provider')}}',
            map_gmap_key:'{{setting_item('map_gmap_key')}}',
            routes:{
                login:'{{route('auth.login')}}',
                register:'{{route('auth.register')}}',
            },
            currentUser:{{(int)Auth::id()}},
            rtl: {{ setting_item_with_lang('enable_rtl') ? "true" : "false" }}
        };
        var i18n = {
            warning:"{{__("Warning")}}",
            success:"{{__("Success")}}",
        };
        var daterangepickerLocale = {
            "applyLabel": "{{__('Apply')}}",
            "cancelLabel": "{{__('Cancel')}}",
            "fromLabel": "{{__('From')}}",
            "toLabel": "{{__('To')}}",
            "customRangeLabel": "{{__('Custom')}}",
            "weekLabel": "{{__('W')}}",
            "first_day_of_week": {{ setting_item("site_first_day_of_the_weekin_calendar","1") }},
            "daysOfWeek": [
                "{{__('Su')}}",
                "{{__('Mo')}}",
                "{{__('Tu')}}",
                "{{__('We')}}",
                "{{__('Th')}}",
                "{{__('Fr')}}",
                "{{__('Sa')}}"
            ],
            "monthNames": [
                "{{__('January')}}",
                "{{__('February')}}",
                "{{__('March')}}",
                "{{__('April')}}",
                "{{__('May')}}",
                "{{__('June')}}",
                "{{__('July')}}",
                "{{__('August')}}",
                "{{__('September')}}",
                "{{__('October')}}",
                "{{__('November')}}",
                "{{__('December')}}"
            ],
        };
    </script>
    <!-- Styles -->
    @yield('head')
    {{--Custom Style--}}
     <link href="{{ route('core.style.customCss') }}" rel="stylesheet">
    {{-- <link href="{{ asset('libs/carousel-2/owl.carousel.css') }}" rel="stylesheet"> --}}
    @if(setting_item_with_lang('enable_rtl'))
        <link href="{{ asset('dist/frontend/css/rtl.css') }}" rel="stylesheet">
    @endif
    <style type="text/css">
        .ace-responsive-menu li:not(.add_listing):hover > a {
            text-decoration: underline !important;
            color: #6f0025 !important;
        }

        header.header-nav.menu_style_home_one.style2 a.navbar_brand {
            margin-top: 6px !important;
        }

        header.header-nav.menu_style_home_one.style2 .ace-responsive-menu li a {
            padding: 18px 10px 18px 10px !important;
        }

        .login-item img, .avatar {
            height: auto !important;
        }

        header.header-nav.menu_style_home_one ul.ace-responsive-menu li.add_listing {
            height: 32px !important;
        }

        header.header-nav.menu_style_home_one ul.ace-responsive-menu li.add_listing a {
            top: unset !important;
        }

        .home1-advnc-search ul li:first-child .form-control,
        .home1-advnc-search .search_option_two .dropdown.bootstrap-select>.dropdown-toggle,
        .home1-advnc-search .small_dropdown2 .btn,
        .home1-advnc-search ul li .search_option_button button {
            height: 32px !important;
        }

        .home1-advnc-search .search_option_two .dropdown.bootstrap-select>.dropdown-toggle {
            line-height: 15px !important;
        }

        .home1-advnc-search .small_dropdown2 .btn {
            line-height: 1.3 !important;
        }

        .home1-advnc-search ul li .search_option_button button {
            width: 100px !important;
        }

        .home1-advnc-search .search_option_two .dropdown-menu,
        .home1-advnc-search .small_dropdown2 .dd_content2 {
            top: 32px !important;
        }

        .home_adv_srch_opt {
            margin-top: 10px !important;
            margin-bottom: 10px !important;
            padding: 5px !important;
        }

        .header_top.home6 {
            position: sticky;
            top: 67px;
            z-index: 999;
        }

        .home1-advnc-search .small_dropdown2 .dd_content2 {
            transform: translate3d(0px, 32px, 0px) !important;
        }
    </style>

    {!! setting_item('head_scripts') !!}
    {!! setting_item_with_lang_raw('head_scripts') !!}

    @php event(new \Modules\Layout\Events\LayoutEndHead()); @endphp
</head>
<body class="frontend-page {{$body_class ?? ''}} @if(setting_item_with_lang('enable_rtl')) is-rtl @endif">
    @php event(new \Modules\Layout\Events\LayoutBeginBody()); @endphp

    {!! setting_item('body_scripts') !!}
    {!! setting_item_with_lang_raw('body_scripts') !!}
    <div class="wrapper  mt-0 pt-0">
        {{-- @include('Layout::parts.topbar') --}}
        @include('Layout::parts.header')
        @yield('content')
        @include('Layout::parts.footer')
    </div>
    {!! setting_item('footer_scripts') !!}
    {!! setting_item_with_lang_raw('footer_scripts') !!}
    @php event(new \Modules\Layout\Events\LayoutEndBody()); @endphp
     <!-- Fotorama -->
</body>
</html>
