<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
@if($user->is_admin)
	<li>
		<a href="{{ backpack_url('dashboard') }}">
			<i class="fa fa-dashboard"></i> <span>{{ trans('backpack::base.dashboard') }}</span>
		</a>
	</li>
	<li>
		<a href="{{ backpack_url('customer') }}">
			<i class="fa fa-fw fa-users"></i> <span>Customers</span>
		</a>
	</li>
	<li>
		<a href="{{ backpack_url('file-service') }}">
			<i class="fa fa-download"></i> <span>File services</span>
		</a>
	</li>
	<li>
		<a href="{{ backpack_url('tickets') }}">
			<i class="fa fa-comments"></i> <span>Support ticket's</span>
			@if($tickets_count)
			<span class="pull-right-container">
              <small class="label pull-right bg-blue"><i class="fa fa-envelope"></i></small>
            </span>
			@endif	
		</a>
	</li>
	<li>
		<a href="{{ backpack_url('order') }}">
			<i class="fa fa-list"></i> <span>Orders</span>
		</a> 
	</li>

	<li>
		<a href="{{ backpack_url('transaction') }}">
			<i class="fa fa-fw fa-table"></i> <span>Transactions</span>
		</a>
	</li>

	<li>
		<a href="{{ backpack_url('email-template') }}">
			<i class="fa fa-copy"></i> <span>Email Templates</span>
		</a>
	</li>

	<li>
		<a href="{{ backpack_url('tuning-credit') }}">
			<i class="fa fa-list"></i> <span>Tuning credit prices</span>
		</a>
	</li>
	<li>
		<a href="{{ backpack_url('tuning-type') }}">
			<i class="fa fa-list"></i> <span>Tuning types</span>
		</a>
	</li>
	@if($user->is_master)
		<li>
			<a href="{{ backpack_url('company') }}">
				<i class="fa fa-list"></i> <span>Manage Companies</span>
			</a>
		</li>
                
		<li>
			<a href="{{ backpack_url('package') }}">
				<i class="fa fa-list"></i> <span>Packages</span>
			</a>
		</li>
        <li>
			<a href="{{ backpack_url('slidermanager') }}">
				<i class='fa fa-list'></i> <span>SliderManager</span>
			</a>
		</li>
	@else
		<li>
			<a href="{{ backpack_url('subscription') }}">
				<i class="fa fa-list"></i> <span>My Subscriptions</span>
			</a>
		</li>
	@endif
	<li>
		<a href="{{ backpack_url('company-setting') }}">
			<i class="fa fa-cog"></i> <span>Company Settings</span>
		</a>
	</li>
@else
	<li>
		<a href="{{ backpack_url('buy-credits') }}">
			Tuning credits
			<h3 style="margin:5px 0px; font-size:3rem; font-weight:bold">{{ $user->user_tuning_credits }}</h3>
			<button class="btn btn-danger">Buy credits &nbsp;<i class="fa fa-arrow-right"></i></button>
		</a>
	</li>
	<li>
		<a href="{{ backpack_url('dashboard') }}">
			<i class="fa fa-dashboard"></i> <span>{{ trans('backpack::base.dashboard') }}</span>
		</a>
	</li>
	<li>
		<a href="{{ backpack_url('file-service') }}">
			<i class="fa fa-download"></i> <span>File services</span>
		</a>
	</li>
        <li>
		<a href="{{ backpack_url('tickets') }}">
			<i class="fa fa-comments"></i> <span>Support ticket's</span>
			@if($tickets_count)
			<span class="pull-right-container">
              <small class="label pull-right bg-blue"><i class="fa fa-envelope"></i></small>
            </span>
			@endif	
		</a>
	</li>
	<li>
		<a href="{{ backpack_url('buy-credits') }}">
			<i class="fa fa-plus"></i> <span>Buy tuning credits</span>
		</a>
	</li>
	<li>
		<a href="{{ backpack_url('order') }}">
			<i class="fa fa-list"></i> <span>Orders</span>
		</a>
	</li>
	<li>
		<a href="{{ backpack_url('transaction') }}">
			<i class="fa fa-fw fa-table"></i> <span>Transactions</span>
		</a>
	</li>
@endif
