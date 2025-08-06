<div class="bg-dark text-white" style="width: 250px; min-height: 100vh;">
    <div class="p-3">
        <h4>Stakeholder</h4>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('dashboard') ? 'active bg-primary' : '' }}" href="{{ route('dashboard') }}">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('users*') ? 'active bg-primary' : '' }}" href="{{ route('users.index') }}">
                <i class="fas fa-users me-2"></i> Users
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('stakeholders*') ? 'active bg-primary' : '' }}" href="{{ route('stakeholders.index') }}">
                <i class="fas fa-handshake me-2"></i> Stakeholders
            </a>
        </li>
    </ul>
</div>

<script>
document.getElementById('sidebar-toggle').addEventListener('click', function() {
    document.querySelector('.bg-dark').classList.toggle('d-none');
});
</script>
