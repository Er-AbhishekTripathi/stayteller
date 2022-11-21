@php
$terms_ids = $row->terms->pluck('term_id');
$attributes = \Modules\Core\Models\Terms::getTermsById($terms_ids);
@endphp
@if(!empty($terms_ids) and !empty($attributes))
<h3 class="font-bold text-2xl ">Amenities</h3>
    @foreach($attributes as $attribute )
        @php $translate_attribute = $attribute['parent']->translateOrOrigin(app()->getLocale()) @endphp
        @if(empty($attribute['parent']['hide_in_single']))
      
       
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
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
                  
                </div>
            </div>
        </div>
       
        @endif
    @endforeach
@endif



@if(!empty($terms_ids) and !empty($attributes))
    @foreach($attributes as $attribute )
        @php $translate_attribute = $attribute['parent']->translateOrOrigin(app()->getLocale()) @endphp
        @if(empty($attribute['parent']['hide_in_single']))
        @if($translate_attribute->name == 'Features')
        <h3 class="font-bold text-2xl mt30 ">{{ $translate_attribute->name }}</h3>
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    @php $terms = $attribute['child'];
                    $j=0;
                    @endphp
                   
                    @foreach($terms as $term )
                  
                    @php $translate_term = $term->translateOrOrigin(app()->getLocale()) @endphp
                    <div class= <?php if($j <= 2){ echo " col-sm-6 "; } if($j >= 2){ ?> " col-sm-6 Features" <?php  } if($j >= 2){ ?> style="display: none;"<?php } ?>>
                        @if($translate_term->icon)
                        <span class="{{ $translate_term->icon }}"></span>
                        @else
                        <span class="flaticon-tick"></span>
                        <b>{{$translate_term->name}}</b>
                        @endif
                    </div>
                    @php
                    $j++;
                    @endphp
                   
                    @endforeach
                    <div class= "text-thm fz14 hideclass" data-class = "Features"  onclick="showamenities(this)"
                    >SHOW MORE</div>
                </div>
            </div>
        </div>
        @endif
        @endif
    @endforeach
@endif