@php
 $propertycategory = Modules\Property\Models\PropertyCategory :: where("status", "publish")->get();
@endphp
<!-- Feature Properties -->
<section id="feature-property" class="feature-property-home6">
    <div class="container">
        <div class="row">
            @foreach($propertycategory as $category)
            <div class="col-lg-12">
                <div class="main-title mb40">

                    @if($title)
                        <h2>{{$category->name}}</h2>
                    @endif
                    <p>
                        @if($desc)
                            {{clean($desc)}}.
                    @endif

                    <a class="float-right" href="{{ url("/property?filter=&layout=&type=&service_name=&location_id=0&category_id=$category->id") }}">{{__('View All')}} <span
                                    class="flaticon-next"></span></a>
                            </p>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="feature_property_home6_slider">
                    @foreach($rows as $row)
                   
                    @if(isset($row->category->name) && $row->category->name == $category->name)
                        @include('Property::frontend.layouts.search.loop-gird-overlay')
                        @endif
                    @endforeach

                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>



