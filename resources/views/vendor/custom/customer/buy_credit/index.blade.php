@extends('backpack::layout')

@section('header')
	<section class="content-header">
	  <h1>
        <span class="text-capitalize">Buy tuning credits</span>
	  </h1>
	  <ol class="breadcrumb">
	    <li><a href="{{ url(config('backpack.base.route_prefix'), 'dashboard') }}">{{ trans('backpack::crud.admin') }}</a></li>
	    <li class="active">Buy tuning credits</li>
	  </ol>
	</section>
@endsection

@section('content')
@php
	$isPayAble = FALSE;
	$isVatCalculation = FALSE;
	if(($company->vat_number != null) && ($company->vat_percentage != null)){
		$isVatCalculation = TRUE;
	}
@endphp
<div class="row">
	<div class="col-md-12">
		<form method="POST" action="{{ route('pay.with.paypal') }}">
		  	@csrf
		  	<div class="box">
		  		@if($tuningCreditGroup)
		  			<input type="hidden" name="tuning_credit_group_id" value="{{ $tuningCreditGroup->id }}">
		  			<input type="hidden" name="vat_number" value="{{ $company->vat_number }}">
		  			<input type="hidden" name="vat_percentage" value="{{ ($company->vat_number != null)?$company->vat_percentage:'' }}">
		  			<input type="hidden" name="item_name" value="Tuning credit">
		  			<input type="hidden" name="item_description" value="">
		  			<input type="hidden" name="item_amount" value="">
		  			<input type="hidden" name="total_amount" value="">
		  			<input type="hidden" name="item_tax" value="">
		  			<input type="hidden" name="item_tax_percentage" value="{{ $company->vat_percentage }}">

			    	<div class="box-body row display-flex-wrap" style="display: flex; flex-wrap: wrap;">
			    		<div class="col-md-6 table-responsive">
			    			<table class="table table-striped">
			    				<thead>
			    					<tr>
					    				<th>&nbsp;</th>
					    				<th>Description</th>
					    				<th>From</th>
					    				<th>For</th>
					    				<th>&nbsp;</th>
					    			</tr>
			    				</thead>
			    				<tbody>
			    					@if($groupCreditTires->count() > 0)
			    						@foreach($groupCreditTires as $groupCreditTire)
					    					<tr>
								    			<td>
								    				<input type="radio" name="item_credits" value="{{ $groupCreditTire->amount }}" {{ ($loop->first)?'checked="checked"':'' }} data-item-amount="{{ $groupCreditTire->pivot->for_credit }}" data-item-description="purchase {{ $groupCreditTire->amount }} tuning credit">
								    			</td>
								    			<td>{{ $groupCreditTire->amount }} credits</td>
								    			<td>
								    				{{ config('site.currency_sign') }}
								    				{{ 
						    							number_format($groupCreditTire->pivot->from_credit, 2) 
						    						}}
								    			</td>
								    			<td>
								    				{{ config('site.currency_sign') }}
								    				{{ 
						    							number_format($groupCreditTire->pivot->for_credit, 2) 
						    						}}
								    			</td>
								    			<td>
								    				@if($groupCreditTire->pivot->from_credit > $groupCreditTire->pivot->for_credit)
								    					Save {{ config('site.currency_sign').' '.number_format(($groupCreditTire->pivot->from_credit - $groupCreditTire->pivot->for_credit), 2) }}
								    				@endif
								    			</td>
								    		</tr>
								    		@php
								    			$isPayAble = TRUE;
								    		@endphp
							    		@endforeach
						    		@endif

						    		@if($isVatCalculation == TRUE)
							    		<tr>
							    			<td>&nbsp;</td>
							    			<td>&nbsp;</td>
							    			<td>VAT (<span class="vat_percentage">0</span>)%</td>
							    			<td>&nbsp;</td>
							    			<td>
							    				{{ config('site.currency_sign') }}
							    				<span class="vat_amount">0.00</span>
							    			</td>
							    		</tr>
						    		@endif

						    		<tr>
					    				<th>&nbsp;</th>
					    				<th>&nbsp;</th>
					    				<th>Order total</th>
					    				<th>&nbsp;</th>
					    				<th>
					    					{{ config('site.currency_sign') }} 
					    					<span class="payable-amount"></span>
					    				</th>
					    			</tr>
			    				</tbody>
				    		</table>
				    		<h4>Payment Method</h4>
				    		<div class="form-group">
				    			<img src="{{ asset('images/paypal.png') }}">
				            </div>
			    		</div>
			    	</div><!-- /.box-body -->
				    <div class="box-footer">
		                <div id="saveActions" class="form-group">
						    <div class="btn-group">
						        <button type="submit" class="btn btn-danger" {{ ($isPayAble == FALSE)?'disabled=disabled':'' }}>
						            <span>Buy</span>
						        </button>
						    </div>
						    <a href="{{ url('customer/dashboard') }}" class="btn btn-default"><span class="fa fa-ban"></span> &nbsp;Cancel</a>
						</div>
			    	</div><!-- /.box-footer-->
		    	@else
			  		{{ __('customer.no_credit_group_of_user') }}
			  	@endif
		  	</div><!-- /.box -->
		</form>
	</div>
</div>

@endsection

@push('after_scripts')
	<script type="text/javascript">
		$(document).ready(function(){
			var element, itemDescription, itemAmount, totalAmount, vatPercentage;
			vatPercentage = 0.00;

			@if($isVatCalculation == TRUE)
				vatPercentage = '{{ $company->vat_percentage }}';
			@endif
			vatPercentage = parseFloat(vatPercentage);
			element = $('input[name=item_credits]:checked');
			itemDescription = element.attr('data-item-description');
			itemAmount = element.attr('data-item-amount');
			itemAmount = parseFloat(itemAmount);
			vatAmount = (itemAmount*vatPercentage/100);
			totalAmount = (itemAmount+vatAmount);
	        $('input[name=item_description]').val(itemDescription);
	        $('input[name=item_amount]').val(itemAmount.toFixed(2));
	        $('input[name=item_tax]').val(vatAmount.toFixed(2));
	        $('input[name=total_amount]').val(totalAmount.toFixed(2));
			$('.payable-amount').text(totalAmount.toFixed(2));
			@if($isVatCalculation == TRUE)
				$('.vat_percentage').text(vatPercentage);
				$('.vat_amount').text(vatAmount.toFixed(2));
			@endif
			$('input[name=item_credits]').on('click', function(){
				element = $(this);
	            if(element.prop("checked") == true){
	            	itemDescription = element.attr('data-item-description');
	            	itemAmount = element.attr('data-item-amount');
	            	itemAmount = parseFloat(itemAmount);
	            	vatAmount = (itemAmount*vatPercentage/100);
	            	totalAmount = (itemAmount+vatAmount);
			        $('input[name=item_description]').val(itemDescription);
			        $('input[name=item_amount]').val(itemAmount.toFixed(2));
			        $('input[name=item_tax]').val(vatAmount.toFixed(2));
			        $('input[name=total_amount]').val(totalAmount.toFixed(2));
					$('.payable-amount').text(totalAmount.toFixed(2));
					@if($isVatCalculation == TRUE)
						$('.vat_percentage').text(vatPercentage);
						$('.vat_amount').text(vatAmount.toFixed(2));
					@endif
	            }
			});
		});
	</script>
@endpush
