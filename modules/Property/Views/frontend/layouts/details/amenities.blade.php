@php
$terms_ids = $row->terms->pluck('term_id');
$attributes = \Modules\Core\Models\Terms::getTermsById($terms_ids);
@endphp
@if(!empty($terms_ids) and !empty($attributes))
<h3 class="font-bold text-2xl ">Amenities</h3>
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                @foreach($attributes as $attribute )
                    @php $translate_attribute = $attribute['parent']->translateOrOrigin(app()->getLocale()) @endphp
                        @if(empty($attribute['parent']['hide_in_single']))
                            @php $terms = $attribute['child'] ;
                            $i=0;
                            @endphp
                                @foreach($terms as $term )
                                    @php $translate_term = $term->translateOrOrigin(app()->getLocale()) @endphp
                                        <div class= " col-sm-4 amenities" >
                                            @if($translate_term->icon)
                                                <span class="{{ $translate_term->icon }}"></span>
                                                @else
                                                <span class="flaticon-tick"></span>
                                                <b style="font-size: 13px;font-weight: bolder;">{{$translate_term->name}}</b>
                                            @endif
                                        </div>
                   
                                     @php
                                        $i++;
                                 @endphp
                                @endforeach
                        @endif
                @endforeach
            </div>
        </div>
    </div>
@endif


