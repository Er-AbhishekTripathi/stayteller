@php
	$attr_request = explode("|", $key);
    if(isset($attr_request[1])) {
		$attr_id = $attr_request[1];
		$attr = \Modules\Core\Models\Attributes::where('service', 'property')->with(['terms'])->find($attr_id);
	}
	$j=0;
@endphp
@if(isset($attr))
	<li>
		<div id="accordion" class="panel-group">
			<div class="panel">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a href="#panelBodyRating{{$attr_id}}" class="accordion-toggle link" data-toggle="collapse" data-parent="#accordion" aria-expanded="true"><i class="flaticon-more"></i> {{isset($attr)?$attr->name : ""}}</a>
					</h4>
				</div>
				<div id="panelBodyRating{{$attr_id}}" class="panel-collapse  collapse show">
					<div class="panel-body row">
						@if(isset($attr))
							<div class="col-lg-12">
								<ul class="ui_kit_checkbox selectable-list float-left fn-400">
									@foreach($attr->terms as $term)
									<li  data-class = "advance_{{{$attr_id}}}"<?php  if($j >= 6){ ?> class = 'Features advance_{{$attr_id}}' <?php  } if($j >= 6){ ?> style="display: none;"<?php } ?>>
										<label class="custom-control custom-checkbox">
											<input type="checkbox" name="terms[]" value="{{$term->id}}" 
												class="custom-control-input" id="customCheck{{$term->id}}"
												@if(!empty(Request::input('terms')))
													@foreach(Request::input('terms') as $t)
														@if($t == $term->id)
															checked
														@endif
													@endforeach
												@endif
											>
											<span class="custom-control-label" for="customCheck{{$term->id}}">{{$term->name}}</span>
										</label>
									</li>
									@php  $j++; @endphp
									@endforeach
								</ul>
							</div>
							@if($j > 6)
							<div class= "text-thm fz14 hideclass showadvancesearch"  data-class = "advance_{{{$attr_id}}}" 
                    >SHOW MORE</div>
					@endif
						@endif
					</div>
				</div>
			</div>
		</div>
	</li>
@endif
