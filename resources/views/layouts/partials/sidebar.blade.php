<div class="bg-dark text-white h-100 sidebar" style="min-width: 250px;">
    <div class="p-3 text-center">
        <div class="bg-white rounded p-2 d-inline-block">
            <img style="width:200px;height:200px"  src="{{asset('images/DCG_LOGO.png')}}" alt="DCG Logo" class="img-fluid" style="max-height: 60px; max-width: 100%;">
        </div>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('dashboard') ? 'active bg-primary' : '' }}" href="{{ route('dashboard') }}">
                <i class="fas fa-tachometer-alt me-2"></i> <span class="nav-text">Dashboard</span>
            </a>
        </li>
        @if(Auth::user()->role === 'admin')
        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('users*') ? 'active bg-primary' : '' }}" href="{{ route('users.index') }}">
                <i class="fas fa-users me-2"></i> <span class="nav-text">Users</span>
            </a>
        </li>
        @endif
        
        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('stakeholders*') ? 'active bg-primary' : '' }}" href="{{ route('stakeholders.index') }}">
                <i class="fas fa-handshake me-2"></i> <span class="nav-text">Stakeholders</span>
            </a>
        </li>
       
        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('communications/report') ? 'active bg-primary' : '' }}" href="{{ route('stakeholder-communications.report') }}">
                <i class="fas fa-chart-bar me-2"></i> <span class="nav-text">Monthly Engagements Report</span>
            </a>
        </li>
         
    </ul>
</div>