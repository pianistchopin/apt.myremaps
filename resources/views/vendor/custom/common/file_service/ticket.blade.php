
@extends('backpack::layout')

@section('header')
	<section class="content-header">
	  	<h1>
        	<span class="text-capitalize">Contact Us</span>
	  	</h1>
		<ol class="breadcrumb">
		    <li>
		    	<a class="text-capitalize" href="{{ url(config('backpack.base.route_prefix'), 'dashboard') }}">
			    	{{ config('backpack.base.route_prefix') }}
			    </a>
		    </li>
		    <li>
		    	<a href="{{ url($crud->route) }}" class="text-capitalize">
		    		{{ $crud->entity_name_plural }}
		    	</a>
		    </li>
		    <li class="active">
			    {{ trans('backpack::crud.add') }}
			</li>
		</ol>
	</section>
@endsection

@section('content')
	@if ($crud->hasAccess('list'))
		<a href="{{ url($crud->route) }}" class="hidden-print">
			<i class="fa fa-angle-double-left"></i> 
			{{ trans('backpack::crud.back_to_all') }} 
			<span>{{ $crud->entity_name_plural }}</span>
		</a>
		<br><br>
	@endif
	<div class="row">
		<div class="col-md-12">
			@include('crud::inc.grouped_errors')
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 col-xs-12">
			<!-- Default box -->
	        <div class="box">
	            <div class="box-body row display-flex-wrap" style="display: flex; flex-wrap: wrap;">
	              	<form method="post"
	                    action="{{ backpack_url('file-service/'.$fileService->id.'/store-ticket') }}"
	                    @if ($crud->hasUploadFields('create'))
	                    enctype="multipart/form-data"
	                    @endif
	                    >
	                  	{!! csrf_field() !!}
	                 	<div class="form-group col-md-12 required {{ $errors->has('question_type') ? ' has-error' : '' }}">
	                     	<input type="hidden" name="question_type" value="File service" />
	                     	<input type="hidden" name="file_servcie_id" value="{{ $fileService->id }}" />
	                	</div>
		                <div class="form-group col-md-12 required {{ $errors->has('message') ? ' has-error' : '' }}">
		                    <label>Message</label>
		                    <textarea name="message" placeholder="Type Message ..." class="form-control" cols="70" rows="4"></textarea>
		                    @if ($errors->has('message'))
		                        <span class="help-block">
		                            <strong>{{ $errors->first('message') }}</strong>
		                        </span>
		                    @endif
		                </div>
		                <div class="hidden ">
		                  <input name="uploaded_file" value="" class="form-control" type="hidden">
		              </div>
		                <div class="form-group col-md-12">
		                    <label>File</label>
		                    <input type="file" name="document" />
		                </div>
	                   	<div class="form-group col-md-12">
	                  		<span class="input-group-btn">
	                        	<button type="submit" class="btn btn-success btn-flat">Send</button>
	                   		</span>
	                  	</div>
	                </form>
	            </div>
	        </div>
		</div>
	    <div class="col-md-6 col-xs-12">
	    	<div class="row">
	    		<div class="col-md-12">
	    			<div class="box">
					    <div class="box-header with-border">
					      	<h3 class="box-title">File service information</h3>
					    </div>
				    	<div class="box-body display-flex-wrap" style="display: flex; flex-wrap: wrap;">
				    		<div class="table-responsive" style="width:100%">
								<table class="table table-striped">
						            <tr>
					                    <th>No.</th>
					                    <td>{{ $fileService->displayable_id }}</td>
					                </tr>
					                <tr>
					                    <th>Status</th>
					                    <td>{{ $fileService->status }}</td>
					                </tr>
					                <tr>
					                    <th>Date submitted</th>
					                    <td>{{ $fileService->created_at }}</td>
					                </tr>
					                <tr>
					                    <th>Tuning type</th>
					                    <td>{{ $fileService->tuningType->label }}</td>
					                </tr>
					                <tr>
					                    <th>Tuning options</th>
					                    <td>{{ $fileService->tuningTypeOptions()->pluck('label')->implode(',') }}</td>
					                </tr>
					                <tr>
					                    <th>Credits</th>
					                    @php
					                    	$tuningTypeCredits = $fileService->tuningType->credits;
					                    	$tuningTypeOptionsCredits = $fileService->tuningTypeOptions()->sum('credits');
					                    	$credits = ($tuningTypeCredits+$tuningTypeOptionsCredits);
					                    @endphp
					                    <td>{{ number_format($credits, 2) }}</td>
					                </tr>
					                <tr>
					                    <th>Original file</th>
					                    <td><a href="{{ backpack_url('file-service/'.$fileService->id.'/download-orginal') }}">download</a></td>
					                </tr>
					                @if((($fileService->status == 'Completed') || ($fileService->status == 'Waiting')) && ($fileService->modified_file != ""))
						                <tr>
						                    <th>Modified file</th>
						                    <td>
						                    	<a href="{{ backpack_url('file-service/'.$fileService->id.'/download-modified') }}">download</a>
						                    	@if($fileService->status == 'Waiting')
						                    		&nbsp;&nbsp;<a href="{{ backpack_url('file-service/'.$fileService->id.'/delete-modified') }}">delete</a>
						                    	@endif
						                    </td>
						                </tr>
					                @endif
						        </table>
						    </div>
				    	</div>
				  	</div>
	    		</div>
	    	</div>
	    	<div class="row">
	    		<div class="col-md-12">
	    			<div class="box">
					    <div class="box-header with-border">
					      	<h3 class="box-title">Car information</h3>
					    </div>
				    	<div class="box-body display-flex-wrap" style="display: flex; flex-wrap: wrap;">
				    		<div class="table-responsive" style="width:100%">
								<table class="table table-striped">
						            <tr>
					                    <th>Car</th>
					                    <td>{{ $fileService->car }}</td>
					                </tr>
					                <tr>
					                    <th>Engine</th>
					                    <td>{{ $fileService->engine }}</td>
					                </tr>
					                <tr>
					                    <th>ECU</th>
					                    <td>{{ $fileService->ecu }}</td>
					                </tr>
					                <tr>
					                    <th>Engine HP</th>
					                    <td>{{ $fileService->engine_hp }}</td>
					                </tr>
					                <tr>
					                    <th>Year</th>
					                    <td>{{ $fileService->year }}</td>
					                </tr>
					                <tr>
					                    <th>Gearbox</th>
					                    <td>{{ $fileService->gearbox }}</td>
					                </tr>
					                <tr>
					                    <th>License plate</th>
					                    <td>{{ $fileService->license_plate }}</td>
					                </tr>
					                <tr>
					                    <th>VIN</th>
					                    <td>{{ $fileService->vin }}</td>
					                </tr>
					                <tr>
					                    <th>Note to engineer</th>
					                    <td>{{ $fileService->note_to_engineer }}</td>
					                </tr>
						        </table>
						    </div>
				    	</div>
				  	</div>
	    		</div>
	    	</div>
		</div> 
	</div>
	@section('scripts')
		<script>
		    $("#question").click(function(){
		       if($(this).val()=="File service"){
		           $("#file-service").show();
		           $("#subject").hide();
		       }else{
		           $("#file-service").hide();
		           $("#subject").show();
		       }
		    });
		    $(document).ready(function(){
		        if($(this).val()=="File service"){
		           $("#file-service").show();
		           $("#subject").hide();
		       }else{
		           $("#file-service").hide();
		           $("#subject").show();
		       }
		    });
		</script>
	    <script src="{{ asset('vendor/adminlte/bower_components/bootstrap-fileinput-master/js/fileinput.min.js') }}"></script>
		<script>
	       $(document).ready(function(){
	            $("input[type=file]").fileinput({
	            	uploadUrl: "{{ backpack_url('upload-ticket-file') }}",
	            	uploadAsync: true,
	                showRemove: false,
	                showCancel: false,
	                showPreview: false,
	                layoutTemplates: {footer: ''},
	            }).on('change', function(event) {
	            	$('.fileinput-upload-button').hide();
				    $('.fileinput-upload-button').click();
				}).on('fileuploaded', function(event, data) {
					if(data.response.status === true){
						$("input[name=uploaded_file]").val(data.response.file);
						$('#saveActions .btn.btn-danger').attr('enabled', 'enabled');
					}else{
						$('#saveActions .btn.btn-danger').attr('disabled', 'disabled');
						$('.kv-upload-progress . progress').html('<div class="progress-bar bg-success progress-bar-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%;">Error</div>');
					}
			    });
	        });
	    </script>
	@stop
@endsection
