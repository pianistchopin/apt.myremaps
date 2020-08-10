@extends('backpack::auth.layout')

@section('content')
<style>
.content{display:flex; width:100%; flex-wrap:wrap;min-height:calc(100vh - 82px); align-items:center;}
.custom-row{display:flex; flex-wrap:wrap; width:100%; margin-left:-15px; margin-right:-15px;}
.half-col{flex:0 0 50%; max-width:50%; padding-left:15px; padding-right:15px;}
.login-col.ml-auto{margin:0 0 0 auto;}
.login-col.mr-auto{margin:0 auto 0 0;}
.login-col{height:100%;}
.login-col .box{height:100%;}
@media only screen and (max-width:767px){
.half-col{flex:0 0 100%; max-width:100%;}
.login-col.ml-auto{margin:auto;}
.login-col.mr-auto{margin:auto;}
.login-col{height:auto;}
.login-col .box{height:auto;}
}
</style>
    <div class="custom-row">
		<div class="half-col">
			<div class="login-col ml-auto">
            <div class="box box-default">
                <div class="box-header with-border">
                    @if(\File::exists(public_path('uploads/logo/' . $company->logo)))
                        <div class="logo-admin">
                            <img src="{{ asset('uploads/logo/' . $company->logo) }}" width="340px">
                        </div>
                    @endif
                    
                </div>
                <div class="box-body">
                    <div class="box-title login-title">Customer {{ __('Login') }}</div>
                    @if(session('status') && session('message'))
                        @if(session('status') == 'success')
                            <div class="alert alert-success">
                                <p>{{ session('message') }}</p>
                            </div>
                        @else
                            <div class="alert alert-danger">
                                <p>{{ session('message') }}</p>
                            </div>
                        @endif
                    @endif
                    <form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                        @csrf
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label class="control-label">{{ __('E-Mail Address') }}</label>
                            <input type="text" class="form-control" name="email" value="{{ old('email') }}">
                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label class="control-label">{{ __('Password') }}</label>
                            <input type="password" class="form-control" name="password">
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> 
                                    {{ __('Remember Me') }}
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-danger">
                                {{ __('Login') }}
                            </button>
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
		</div>
		
		<div class="half-col">
			<div class="login-col mr-auto">
				<div class="box box-default " style="">
					<div class="box-header with-border">
						
					</div>
					<div class="box-body reg-box">
						<div class="box-title login-title">Don't have an account yet?</div>
						<p><strong>As a registered user you can:</strong></p>
						
						<ul>
							<li>Buy credits using PayPal </li>
							<li>Upload tuningfiles and receive the modified files in return</li>
							<li>Modified files are of high quality, safe and Dyno-tested</li>
							<li>Every tuning file is custom made to fit your car, with the best perfomance results</li>
							<li>Fast and Secure. Your connection is secured using SSL Encryption</li>
							<li>Your modified library is stored in the cloud for future use</li>
							
						</ul>
						
							<div class="form-group">
								
								<a class="btn btn-danger" href="{{ route('users_registers') }}">
									{{ __('Register') }}
								</a>
							</div>
					</div>
				</div>
			</div>
		</div>
    </div>
@endsection
