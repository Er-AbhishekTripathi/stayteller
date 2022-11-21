@extends ('layouts.app')
@section ('content')
@php 
//dd($row);
//$row->template_id = 6;
@endphp
    @if($row->template_id)
        <div class="page-template-content @if(!empty($row->body_width)and $row->body_width == 'max1600') maxw1600 m0a @endif">
            {!! $row->getProcessedContent() !!}
        </div>
    @else
        <div class="container " style="padding-top: 40px;padding-bottom: 40px;">
            <h1>{{$translation->title}}</h1>
            <div class="blog-content">
                {!! $translation->content !!}
            </div>
        </div>
    @endif
@endsection

@section('footer')
<script type="text/javascript">
        function showlink(event) {  
            var dataclass= event.getAttribute('data-url');
            window.location.href =event.getAttribute('data-url');
           
       
        }
</script>
@endsection




