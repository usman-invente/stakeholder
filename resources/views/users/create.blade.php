@extends('layouts.master')

@section('title', 'Create User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Create New User</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                        value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                        value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Roles <span class="text-danger">*</span></label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input @error('roles') is-invalid @enderror" type="checkbox" name="roles[]" value="user" id="role_user" {{ in_array('user', old('roles', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role_user">
                                                    <i class="fas fa-user me-1"></i> User
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input @error('roles') is-invalid @enderror" type="checkbox" name="roles[]" value="admin" id="role_admin" {{ in_array('admin', old('roles', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role_admin">
                                                    <i class="fas fa-user-shield me-1"></i> Admin
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input @error('roles') is-invalid @enderror" type="checkbox" name="roles[]" value="receptionist" id="role_receptionist" {{ in_array('receptionist', old('roles', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role_receptionist">
                                                    <i class="fas fa-concierge-bell me-1"></i> Receptionist
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input @error('roles') is-invalid @enderror" type="checkbox" name="roles[]" value="contract_creator" id="role_contract_creator" {{ in_array('contract_creator', old('roles', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role_contract_creator">
                                                    <i class="fas fa-file-contract me-1"></i> Contract Creator
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('roles')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Select one or more roles for this user.</small>
                                </div>
                            </div>
                        </div>
                        
                        

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('stakeholderSearch');
    const stakeholderItems = document.querySelectorAll('.stakeholder-item');
    const selectAllBtn = document.getElementById('selectAll');
    const deselectAllBtn = document.getElementById('deselectAll');
    const checkboxes = document.querySelectorAll('.stakeholder-checkbox');
    const selectedCountEl = document.getElementById('selectedCount');
    
    // Function to update the selected count
    function updateSelectedCount() {
        const selectedCount = document.querySelectorAll('.stakeholder-checkbox:checked').length;
        selectedCountEl.textContent = selectedCount;
    }
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        stakeholderItems.forEach(item => {
            const checkbox = item.querySelector('.stakeholder-checkbox');
            const name = checkbox.getAttribute('data-name');
            const org = checkbox.getAttribute('data-org');
            
            if (searchTerm === '' || 
                name.includes(searchTerm) || 
                org.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    // Select all visible stakeholders
    selectAllBtn.addEventListener('click', function() {
        stakeholderItems.forEach(item => {
            if (item.style.display !== 'none') {
                const checkbox = item.querySelector('.stakeholder-checkbox');
                checkbox.checked = true;
            }
        });
        updateSelectedCount();
    });
    
    // Deselect all stakeholders
    deselectAllBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSelectedCount();
    });
    
    // Update count when any checkbox changes
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    // Initial count update
    updateSelectedCount();
});
</script>
@endpush
