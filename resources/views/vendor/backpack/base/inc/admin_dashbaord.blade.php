@if($user->subscription_ended_string != NULL)
   @if(!$user->hasActiveSubscription())
        <div class="row">
            <div class="col-md-6 col-xs-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Subscription</h3>
                    </div>
                    <div class="box-body display-flex-wrap" style="display: flex; flex-wrap: wrap;">
                        <p>
                            Please activate your subscription.Click on the button below and select your subscription. Once completed your panel will be fully activated.
                        </p>
                        
                    </div>
                    <div class="box-footer">
                        <a href="{{ route('subscription.packages') }}" class="btn btn-danger">Choose Packages</a>
                    </div>
                </div>
            </div>
        </div>
   @else
        <div class="alert alert-warning">
            <p>
                {!! $user->subscription_ended_string !!}
            </p>
        </div>
  @endif
@endif



<div class="row">
    <div class="col-lg-4 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3>{{ $openFileServices }}</h3>
                <p>OPEN FILE SERVICES</p>
            </div>
            <div class="icon">
                <i class="fa fa-copy"></i>
            </div>
            <a href="{{ url('admin/file-service?status=O') }}" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-4 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-green">
            <div class="inner">
                <h3>{{ $waitingFileServices }}</h3>
                <p>WAITING FILE SERVICES</p>
            </div>
            <div class="icon">
                <i class="fa fa-copy"></i>
            </div>
            <a href="{{ url('admin/file-service?status=W') }}" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-4 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3>{{ $complatedFileServices }}</h3>
                <p>COMPLETED FILE SERVICES</p>
            </div>
            <div class="icon">
                <i class="fa fa-copy"></i>
            </div>
            <a href="{{ url('admin/file-service?status=C') }}" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

@if($user->company->id == 1)
	
	@if($user->company->is_public == 1)
		@php 
			$accountType = 'Public'; 
			$changeAccountType = 'Private'; 
		@endphp
	@else
		@php 
			$accountType = 'Private'; 
			$changeAccountType = 'Public'; 
		@endphp
	@endif
	
	<div class="row">
		<div class="col-md-6 col-xs-12">
			<div class="box">
				<div class="box-header with-border">
					<div class="box-header with-border">
						<h3 class="box-title">Account Type</h3>
					</div>
					
					<div class="box-body display-flex-wrap" style="display: flex; flex-wrap: wrap;">
						<p> <b>Your Account Type : </b> @php echo $accountType @endphp</p>
					</div>
					
					<div class="box-body display-flex-wrap" style="display: flex; flex-wrap: wrap;">
						<a href="company/@php echo $user->company->id @endphp/company-account-type" class="btn btn-danger">Want to  @php echo $changeAccountType @endphp ? </a>
					</div> 
				</div>
			</div>
		</div>		
	</div>
@endif

<div class="row">
    <div class="col-lg-12">
        <h3 class="box-title">Recent orders</h3>
        <div class="table-responsive" style="width:100%">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Order no.</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    @if($orders->count() > 0)
                        @foreach($orders as $order)
                            <tr>
                                <td>{{ $order->displayable_id }}</td>
                                <td>{{ $order->created_at }}</td>
                                <td>{{ $order->customer }}</td>
                                <td>{{ $order->amount_with_sign }}</td>
                                <td>{{ $order->status }}</td>
                                <td>
                                    <a href="{{ url('admin/order/'.$order->id.'/invoice') }}" class="btn btn-xs btn-default"><i class="fa fa-btn fa-file"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6">No file services created by you yet!</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <a href="{{ backpack_url('order') }}" class="btn btn-danger">View all orders <i class="fa fa-arrow-right"></i></a>
    </div>
</div>