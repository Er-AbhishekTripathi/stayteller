@php
    $actives = \App\Currency::getActiveCurrency();
    $current = \App\Currency::getCurrent('currency_main');
@endphp
{{--Multi Language--}}
@if(!empty($actives) and count($actives) > 1)
    <li class="dropdown">
        @foreach($actives as $currency)
            @if($current == $currency['currency_main'])
                <a href="#" data-toggle="dropdown" class="is_login">
                    {{strtoupper($currency['currency_main'])}}
                    <i class="fa fa-angle-down d-none"></i>
                </a>
            @endif
        @endforeach
        <ul class="{{!empty($mobile)?"":"dropdown-menu"}} text-left width-auto">
            @foreach($actives as $currency)
                @if($current != $currency['currency_main'])
                    <li>
                        <a href="{{get_currency_switcher_url($currency['currency_main'])}}" class="is_login">
                            {{strtoupper($currency['currency_main'])}}
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </li>
@endif
{{--End Multi language--}}