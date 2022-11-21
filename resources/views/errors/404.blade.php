@extends('layouts.app')
@section('title',__('Page not found'))
@section('code',404)
@section('content')
	<div id="bc_content-wrapper">
		<section class="our-error">
			<div class="container">
				<div class="row">
					<div class="col-lg-10 offset-lg-1 text-center">
						<div class="error_page footer_apps_widget">
							<img class="img-fluid" src="{{get_file_url(setting_item("error_404_banner"),"full")}}" alt="error.png">
							<div class="erro_code"><h1>{{ setting_item_with_lang('error_404_title') }}</h1></div>
							<p>{{ setting_item_with_lang('error_404_desc') }}</p>
						</div>
						<a class="btn btn_error btn-thm" href="/">{{__('Back To Home')}}</a>
					</div>
				</div>
			</div>
		</section>
	</div>
@endsection

@section('footer')

@endsection
