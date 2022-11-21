<?php $container = 1 ?>
@extends('admin.layouts.app')
@section('head')

@endsection
@section('content')
    <div class="col-lg-12 mb10">
    </div>
    <div class="mb-3">
        @if($row->id)
            @include('Language::admin.navigation')
        @endif
    </div>
    <form class="" action="{{route('voucher.admin.store',['id'=>($row->id) ? $row->id : '-1','lang'=>request()->query('lang')])}}" method="post">
        @csrf
            <div class="row">
                <div class="col-sm-9">
                @include('Voucher::admin.content',['hide_gallery'=>true,'property_type'=>1])
               
                
                </div>
               
            </div>
            
            
            
    </form>

@endsection
@section('script.body')
    <script type="text/javascript" src="{{ asset('libs/tinymce/js/tinymce/tinymce.min.js') }}" ></script>
    <script type="text/javascript" src="{{ asset('js/condition.js?_ver='.config('app.asset_version')) }}"></script>
    <script type="text/javascript" src="{{url('module/core/js/map-engine.js?_ver='.config('app.asset_version'))}}"></script>
    {!! App\Helpers\MapEngine::scripts() !!}

@endsection
