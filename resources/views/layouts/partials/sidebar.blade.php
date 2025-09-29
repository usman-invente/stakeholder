<div class="bg-dark text-white h-100 sidebar" style="min-width: 250px;">
    <div class="p-3 text-center">
        <div class="bg-white rounded p-2 d-inline-block">
            <img style="width:200px;height:200px"  src="{{asset('public/images/DCG_LOGO.png')}}" alt="DCG Logo" class="img-fluid" style="max-height: 60px; max-width: 100%;">
        </div>
    </div>
    <ul class="nav flex-column">
        @if(Auth::user()->role === 'receptionist')
            <li class="nav-item">
                <a class="nav-link text-white {{ Request::is('dashboard') ? 'active bg-primary' : '' }}" href="{{ route('dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i> <span class="nav-text">Receptionist Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ Request::is('receptionist') ? 'active bg-primary' : '' }}" href="{{ route('receptionist.view') }}">
                    <i class="fas fa-desktop me-2"></i> <span class="nav-text">Visitor Monitor</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ Request::is('visitors*') ? 'active bg-primary' : '' }}" href="{{ route('visitor.register') }}" target="_blank">
                    <i class="fas fa-user-plus me-2"></i> <span class="nav-text">New Visitor Registration</span>
                </a>
            </li>
            
        @else
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
            
            <li class="nav-item">
                <a class="nav-link text-white {{ Request::is('contracts*') ? 'active bg-primary' : '' }}" href="{{ route('contracts.dashboard') }}">
                    <i class="fas fa-file-contract me-2"></i> <span class="nav-text">Contract Management</span>
                </a>
            </li>
        @endif
    </ul>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qr-code-modal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrCodeModalLabel">Visitor Registration QR Code</h5>
                <button type="button" class="btn-close" onclick="document.getElementById('qr-code-modal').classList.remove('show'); document.getElementById('qr-code-modal').style.display = 'none';" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p>Scan this QR code to access the visitor registration form:</p>
                <div id="qrcode-container" class="my-4">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('visitor.register')) }}" alt="Registration QR Code" class="img-fluid">
                </div>
                <p class="small text-muted">Registration URL: {{ route('visitor.register') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="window.print();">Print QR Code</button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('qr-code-modal').classList.remove('show'); document.getElementById('qr-code-modal').style.display = 'none';">Close</button>
            </div>
        </div>
    </div>
</div>