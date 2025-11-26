<a href="javascript:" class="d-flex align-items-center text-reset dropdown-toggle"  data-toggle="dropdown">
@auth
{{ translate(Auth::user()->name)}} <i class="la la-user la-2x"></i>
    @else
    Sign in <i class="la la-user la-2x"></i>
    @endauth
</a>
<div class="dropdown-menu">
    
    @auth
                    @if(isAdmin())
                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">{{ translate('My Panel')}}</a>
                    
                    @else
                    <a class="dropdown-item" href="{{ route('dashboard') }}">{{ translate('My Panel')}}</a>
                    <a class="dropdown-item" href="{{ route('referral_link.index') }}">{{ translate('Referral Link')}}</a>
                    @endif
                    <a class="dropdown-item" href="{{ route('logout') }}">{{ translate('Logout')}}</a>
                    
                    @else
                    <a class="dropdown-item" href="{{ route('user.login') }}">{{ translate('Login')}}</a>
                    <a class="dropdown-item" href="{{ route('user.registration') }}">{{ translate('Registration')}}</a>
                    @endauth
    
  </div>