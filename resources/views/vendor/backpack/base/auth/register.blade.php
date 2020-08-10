@extends('backpack::auth.layout')

@section('content')
    <div class="row">
        <div class="login-col">
            <div class="box box-default">
                <div class="box-header with-border">
                    <div class="logo-admin">
                        <img src="{{ asset('images/logo.png') }}">
                    </div>
                    <div class="box-title">{{ trans('backpack::base.register') }}</div>
                </div>
                <div class="box-body">
                    <form role="form" method="POST" action="{{ route('backpack.auth.register') }}">
                        {!! csrf_field() !!}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="control-label">{{ trans('backpack::base.name') }}</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group{{ $errors->has(backpack_authentication_column()) ? ' has-error' : '' }}">
                            <label class="control-label">
                                {{ config('backpack.base.authentication_column_name') }}
                            </label>
                            <input type="{{ backpack_authentication_column()=='email'?'email':'text'}}" class="form-control" name="{{ backpack_authentication_column() }}" value="{{ old(backpack_authentication_column()) }}">
                            @if ($errors->has(backpack_authentication_column()))
                                <span class="help-block">
                                    <strong>{{ $errors->first(backpack_authentication_column()) }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label class="control-label">{{ trans('backpack::base.password') }}</label>
                            <input type="password" class="form-control" name="password">
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label class="control-label">
                                {{ trans('backpack::base.confirm_password') }}
                            </label>
                            <input type="password" class="form-control" name="password_confirmation">
                            @if ($errors->has('password_confirmation'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-btn fa-user"></i> {{ trans('backpack::base.register') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
