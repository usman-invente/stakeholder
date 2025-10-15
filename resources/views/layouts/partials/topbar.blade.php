<nav class="navbar navbar-expand-md navbar-light bg-light border-bottom sticky-top">
    <div class="container-fluid">
        <div class="d-flex align-items-center">
            <span class="navbar-brand m-0">DCG</span>
        </div>
        
        <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav d-md-none">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>
                @if(Auth::user()->hasRole('admin'))
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                        <i class="fas fa-users me-2"></i> Users
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('stakeholders*') ? 'active' : '' }}" href="{{ route('stakeholders.index') }}">
                        <i class="fas fa-handshake me-2"></i> Stakeholders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('communications/report') ? 'active' : '' }}" href="{{ route('stakeholder-communications.report') }}">
                        <i class="fas fa-chart-bar me-2"></i> Monthly Engagements Report
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
            </ul>
            
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user me-1"></i> <span>{{ Auth::user()->name ?? 'User' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="navbarDropdown" style="min-width: 200px;">
                        
                        @if(Auth::user()->hasRole('admin'))
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('settings.index') }}">
                                    <i class="fas fa-cogs me-2"></i> System Settings
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('users.index') }}">
                                    <i class="fas fa-users me-2"></i> Manage Users
                                </a>
                            </li>
                        @endif
                        
                        @if(Auth::user()->hasAnyRole(['admin', 'user']))
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('stakeholders.index') }}">
                                    <i class="fas fa-handshake me-2"></i> Manage Stakeholders
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->hasAnyRole(['admin', 'user']))
                        <li>
                                <a class="dropdown-item py-2" href="{{ route('stakeholder-communications.report') }}">
                                    <i class="fas fa-chart-bar me-2"></i> Monthly Report
                                </a>
                        </li>
                        @endif
                        <li><hr class="dropdown-divider my-2"></li>
                        
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i> {{ __('Logout') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
