<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container-fluid">
        <button id="sidebar-toggle" class="btn" type="button">
            <i class="fas fa-bars"></i>
        </button>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user"></i> {{ Auth::user()->name ?? 'User' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="navbarDropdown">
                      
                        
                        @if(Auth::user()->role === 'admin')
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('settings.index') }}">
                                    <i class="fas fa-cogs me-2"></i> System Settings
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
