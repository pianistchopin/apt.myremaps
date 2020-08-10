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
            <a href="{{ url('customer/file-service?status=O') }}" class="small-box-footer">
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
            <a href="{{ url('customer/file-service?status=W') }}" class="small-box-footer">
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
            <a href="{{ url('customer/file-service?status=C') }}" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <h3 class="box-title">Recent file services</h3>
        <div class="table-responsive" style="width:100%">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Car</th>
                        <th>Created</th>
                        <th>Status</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    @if($fileServices->count() > 0)
                        @foreach($fileServices as $fileService)
                            <tr>
                                <td>{{ $fileService->displayable_id }}</td>
                                <td>{{ $fileService->car }}</td>
                                <td>{{ $fileService->created_at }}</td>
                                <td>{{ $fileService->status }}</td>
                                <td>
                                    @if($fileService->status == 'Completed')
                                        <a href="{{ url('customer/file-service/'.$fileService->id.'/download-modified') }}" title="Download modified file">
                                            <i class="fa fa-btn fa-download"></i>
                                        </a>
                                    @endif
                                    &nbsp;&nbsp;
                                    <a href="{{ url('customer/file-service/'.$fileService->id.'/download-orginal') }}" title="Download orginal file">
                                        <i class="fa fa-btn fa-download"></i>
                                    </a>

                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5">No file services created by you yet!</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <a href="{{ url('customer/file-service') }}" class="btn btn-danger">View all file services <i class="fa fa-arrow-right"></i></a>
    </div>
</div>
<br/>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="row">
                <div class="col-md-6">
                    <div class="box-header with-border">
                        <h3 class="box-title">Company information</h3>
                    </div>
                    <div class="box-body display-flex-wrap">
                        {{ $company->name }}
                        <br>{{ $company->address_line_1 }}
                    </div>
                    <div class="box-header with-border">
                        <h3 class="box-title">Financial</h3>
                    </div>
                    <div class="box-body display-flex-wrap">
                        VAT number:  {{ $company->vat_number }}

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="box-header with-border">
                        <h3 class="box-title">Email addresses</h3>
                    </div>
                    <div class="box-body display-flex-wrap">
                        <table class="table table-striped">
                            <tr>
                                <th>Main</th>
                                <td><a href="mailto:{{ $company->main_email_address }}">{{ $company->main_email_address }}</a></td>
                            </tr>
                            <tr>
                                <th>Support</th>
                                <td><a href="mailto:{{ $company->support_email_address }}">{{ $company->support_email_address }}</a></td>
                            </tr>
                            <tr>
                                <th>Billing</th>
                                <td><a href="mailto:{{ $company->billing_email_address }}">{{ $company->billing_email_address }}</a></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

			<div class="row">
				<div class="col-md-12">
					<div class="box">
						<div class="row">
							<div class="col-md-6">
								<div class="box-header with-border">
									<h3 class="box-title">Give Rating to Company</h3>
									<br/> <b> ( Overall Company Rating : {{$company->rating}} )</b>
								</div>
								<div class="box-body display-flex-wrap">

										{{ Form::open(array('url' => 'customer/add-rating')) }}
										@php
											//$disabled ='';

											if(isset($customerRating->rating)){
												//$disabled = 'disabled="disabled"';
												$ratings = $customerRating->rating;
											}else{
												$ratings = $company->rating;
											}
										@endphp
										<div class="form-group">
											@if(isset($customerRating->rating))
												<label>You gave  Rating</label>
												{{Form::hidden('id', $customerRating->id, ['class' => 'form-control'])}}
											@endif
											{{Form::select('rating', ['1'=>1,'2'=>2,'3'=>3,'4'=>4,'5'=>5], $ratings, ['class' => 'form-control'])}}
										</div>

										<div class="form-group">
											{{ Form::submit('Submit',['class'=>'btn btn-success']) }}
										</div>

										{{ Form::close() }}


								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

            <div class="row">
                <div class="col-md-12">
                    <div class="box-header with-border">
                        <h3 class="box-title">Notes</h3>
                    </div>
                    <div class="box-body display-flex-wrap">
                        <p>{{ $company->customer_note }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
