<div class="bg-dark text-white h-100 sidebar" style="min-width: 250px;">
    <div class="p-3 text-center">
        <div class="bg-white rounded p-2 d-inline-block">
            <img style="width:100px;height:100px" src="{{asset('public/images/DCG_LOGO.png')}}" alt="DCG Logo" class="img-fluid" style="max-height: 60px; max-width: 100%;">
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
        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'user')
        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('dashboard') ? 'active bg-primary' : '' }}" href="{{ route('dashboard') }}">
                <i class="fas fa-tachometer-alt me-2"></i> <span class="nav-text">Dashboard</span>
            </a>
        </li>
        @endif
        @if(Auth::user()->role === 'admin')
        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('users*') ? 'active bg-primary' : '' }}" href="{{ route('users.index') }}">
                <i class="fas fa-users me-2"></i> <span class="nav-text">Users</span>
            </a>
        </li>
        @endif

        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'user')
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
        @endif
        @if(Auth::user()->role === 'admin')
        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('contracts*') ? 'active bg-primary' : '' }}" href="{{ route('contracts.dashboard') }}">
                <i class="fas fa-file-contract me-2"></i> <span class="nav-text">Contract Management</span>
            </a>
        </li>
        @endif
        @endif

        @if(Auth::user()->role === 'contract_creator')
        <!-- Contract Creator Menu Section -->
         <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('contracts/dashboard') ? 'active bg-primary' : '' }}" href="{{ route('contracts.dashboard') }}">
                <i class="fas fa-chart-pie me-2"></i> <span class="nav-text">Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('contracts') && !Request::is('contracts/create') && !Request::is('contracts/reports*') ? 'active bg-primary' : '' }}" href="{{ route('contracts.index') }}">
                <i class="fas fa-list me-2"></i> <span class="nav-text">All Contracts</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('contracts/create') ? 'active bg-primary' : '' }}" href="{{ route('contracts.create') }}">
                <i class="fas fa-plus-circle me-2"></i> <span class="nav-text">Create New Contract</span>
            </a>
        </li>

      

        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('contracts/reports*') ? 'active bg-primary' : '' }}" href="{{ route('contracts.reports') }}">
                <i class="fas fa-chart-bar me-2"></i> <span class="nav-text">Monthly Reports</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('departments*') ? 'active bg-primary' : '' }}" href="{{ route('departments.index') }}">
                <i class="fas fa-building me-2"></i> <span class="nav-text">Manage Departments</span>
            </a>
        </li>

        <!-- Divider -->
        <li class="nav-item mt-3 mb-2">
            <hr class="text-white-50">
            <small class="text-white-50 px-3">Contract Tools</small>
        </li>



        <li class="nav-item">
            <a class="nav-link text-white" href="{{ route('contracts.reports.export', ['type' => 'all', 'format' => 'excel']) }}">
                <i class="fas fa-download me-2"></i> <span class="nav-text">Export All Contracts</span>
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

@if(Auth::user()->role === 'contract_creator')
<script>
    function generateContractId() {
        // Show loading state
        const button = event.target.closest('a');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> <span class="nav-text">Generating...</span>';

        fetch('{{ route("contracts.generate-id") }}')
            .then(response => response.json())
            .then(data => {
                if (data.contract_id) {
                    // Copy to clipboard if supported
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(data.contract_id).then(() => {
                            alert('Contract ID generated: ' + data.contract_id + '\n\nCopied to clipboard!');
                        }).catch(() => {
                            alert('Contract ID generated: ' + data.contract_id);
                        });
                    } else {
                        alert('Contract ID generated: ' + data.contract_id);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error generating contract ID. Please try again.');
            })
            .finally(() => {
                // Restore button state
                button.innerHTML = originalText;
            });
    }
</script>
@endif