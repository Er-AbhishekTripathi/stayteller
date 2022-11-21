<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{$html_class ?? ''}}">
<head>
    <meta charset="utf-8">
    {{-- <base href="{{asset('findhouse')}}/"> --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php($favicon = setting_item('site_favicon'))
    <link rel="icon" type="image/png" href="{{!empty($favicon)?get_file_url($favicon,'full'):url('images/favicon.png')}}" />
    @include('Layout::parts.seo-meta')
    <link href="{{ asset('libs/flags/css/flag-icon.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/bootstrap/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/ionicons/css/ionicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/icofont/icofont.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('findhouse/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('findhouse/css/dashbord_navitaion.css') }}">
    <link rel="stylesheet" href="{{ asset('findhouse/css/responsive.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset("libs/daterange/daterangepicker.css") }}" >
    <link rel="stylesheet" href="{{ asset('dist/frontend/module/user/css/user.css') }}">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel='stylesheet' id='google-font-css-css'  href='https://fonts.googleapis.com/css?family=Poppins%3A400%2C500%2C600' type='text/css' media='all' />
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
            }
        };
        var i18n = {
            warning:"{{__("Warning")}}",
            success:"{{__("Success")}}",
            confirm_delete:"{{__("Do you want to delete?")}}",
            confirm:"{{__("Confirm")}}",
            cancel:"{{__("Cancel")}}",
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
    <link href="{{ asset('libs/carousel-2/owl.carousel.css') }}" rel="stylesheet">
    @if(setting_item_with_lang('enable_rtl'))
        <link href="{{ asset('dist/frontend/css/rtl.css') }}" rel="stylesheet">
    @endif
</head>
<body class="user-page bgc-f7 {{$body_class ?? ''}} @if(setting_item_with_lang('enable_rtl')) is-rtl @endif">
    {!! setting_item('body_scripts') !!}
    <div class="wrapper">
        {{-- @include('Layout::parts.topbar') --}}
        {{-- <div class="preloader"></div> --}}
        @include('Layout::parts.header')
        <div class="d-block d-sm-flex justify-content-start ">
            <div class="dashboard_sidebar_menu dn-992">
                @include('User::frontend.layouts.sidebar')
            </div>
            <section class="our-dashbord dashbord bgc-f7">
                <div class="@if(empty($container)) container-fluid @else container @endif">
                    <div class="row">
                        <div class="col-sm-12 maxw100flex-992">
                            @include('Layout::parts.user.mobile-sidebar')
                            <div class="mb10">
                                <div class="breadcrumb_content style2">
                                    <h3>
                                        @foreach($breadcrumbs as $key => $breadcrumb)
                                            @if(!empty($breadcrumb['url']))
                                                <a href="{{url($breadcrumb['url'])}}">{{$breadcrumb['name']}}</a>
                                            @else
                                                {{$breadcrumb['name']}}
                                            @endif
                                            @if(isset($breadcrumbs[$key + 1]))
                                                &#187;
                                            @endif
                                        @endforeach
                                    </h3>
                                </div>
                                @include('admin.message')
                            </div>
                            <div class="my-content">
                                @yield('content')
                            </div>
                            <div class="row mt10 d-none">
                                <div class="col-lg-12">
                                    <div class="copyright-widget text-center">
                                        <p>{{ __('© 2020  Hostel Booking. Made with love.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>



    </div>
    @if(Auth::id())
        @include('Media::browser')
    @endif
    {!! setting_item('footer_scripts') !!}
    <script type="text/javascript" src="{{ asset('libs/jquery-3.5.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('findhouse/js/jquery-migrate-3.0.0.min.js') }}"></script>
    <script src="{{ asset('libs/lodash.min.js') }}"></script>
    <script src="{{ asset('libs/vue/vue.js') }}"></script>
    <script type="text/javascript" src="{{ asset('findhouse/js/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('findhouse/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('findhouse/js/jquery.mmenu.all.js') }}"></script>
    <script type="text/javascript" src="{{ asset('findhouse/js/ace-responsive-menu.js') }}"></script>
    <script type="text/javascript" src="{{ asset('findhouse/js/bootstrap-select.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('findhouse/js/snackbar.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('findhouse/js/simplebar.js') }}"></script>
    <script type="text/javascript" src="{{ asset('findhouse/js/parallax.js') }}"></script>
    <script type="text/javascript" src="{{ asset('findhouse/js/scrollto.js') }}"></script>
    <script type="text/javascript" src="{{ asset('findhouse/js/jquery-scrolltofixed-min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('findhouse/js/jquery.counterup.js') }}"></script>
    <script type="text/javascript" src="{{ asset('findhouse/js/wow.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('findhouse/js/progressbar.js') }}"></script>
    <script type="text/javascript" src="{{ asset('findhouse/js/slider.js') }}"></script>
    <script type="text/javascript" src="{{ asset('findhouse/js/timepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('findhouse/js/wow.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('findhouse/js/dashboard-script.js') }}"></script>
    <!-- Custom script for all pages -->
    <script type="text/javascript" src="{{asset('findhouse/js/script.js')}}"></script>
    @if(Auth::id())
        <script src="{{ asset('module/media/js/browser.js?_ver='.config('app.asset_version')) }}"></script>
    @endif
    <script src="{{ asset('js/functions.js') }}"></script>
    <script type="text/javascript" src="{{ asset('module/user/js/user.js') }}"></script>

    @yield('script.body')
</body>
</html>
