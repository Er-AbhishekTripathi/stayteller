@extends('layouts.app')
@section('head')
	<style type="text/css">
		.bravo-contact-block .section{
			padding: 80px 0 !important;
		}
	</style>
@endsection
@section('content')

	@include("Contact::frontend.blocks.contact.index")

@endsection

@section('footer')

@endsection
