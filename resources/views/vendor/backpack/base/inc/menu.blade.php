<div class="navbar-custom-menu pull-left">
    <ul class="nav navbar-nav">
        <!-- =================================================== -->
        <!-- ========== Top menu items (ordered left) ========== -->
        <!-- =================================================== -->

        <!-- <li><a href="{{ url('/') }}"><i class="fa fa-home"></i> <span>Home</span></a></li> -->

        <!-- ========== End of top menu left items ========== -->
    </ul>
</div>


<div class="navbar-custom-menu">
    <ul class="nav navbar-nav">
        <li>
        @if($user)
            <li class="dropdown user user-menu">
                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    @php
                        $avtarString = $user->full_name;
                        $avtarWords = explode(" ", $avtarString);
                        $allFirstLetters = "";
                        foreach ($avtarWords as $value) {
                            $allFirstLetters .= substr($value, 0, 1);
                        }
                    @endphp
                    <span class="user-image"><small>{{ $allFirstLetters }}</small></span>
                    <div class="right-user-data">
                        <span class="user-name hidden-xs dis-block">{{ $user->full_name }}</span>
                        <span class="company-name dis-block">{{ $company->name }}</span>
                    </div>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="{{ route('account.info') }}"><i class="fa fa-btn fa-user"></i>Edit account</a></li>
                    <li>
                        @if($user->is_admin)
                            <a href="{{ route('admin.auth.logout') }}" onclick="event.preventDefault();
                                                                   document.getElementById('logout-form').submit();">
                                <i class="fa fa-btn fa-sign-out"></i> {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('admin.auth.logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @else
                            <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                                   document.getElementById('logout-form').submit();">
                                <i class="fa fa-btn fa-sign-out"></i> {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @endif
                    </li>
                </ul>
            </li>
            
        @endif
        </li>
       <!-- ========== End of top menu right items ========== -->
    </ul>
</div>
