@extends('layouts.master')

@section('title', 'Manage Departments')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Manage Departments</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Add New Department Form -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Add New Department</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('departments.store') }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="name" class="form-label">Department Name *</label>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                           id="name" name="name" value="{{ old('name') }}" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="description" class="form-label">Description</label>
                                                    <input type="text" class="form-control @error('description') is-invalid @enderror" 
                                                           id="description" name="description" value="{{ old('description') }}">
                                                    @error('description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i> Add Department
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Departments List -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Department Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Contracts Count</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departments as $index => $department)
                                <tr id="department-{{ $department->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <span class="department-name">{{ $department->name }}</span>
                                        <div class="edit-form d-none">
                                            <input type="text" class="form-control form-control-sm" value="{{ $department->name }}" id="edit-name-{{ $department->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <span class="department-description">{{ $department->description ?? 'N/A' }}</span>
                                        <div class="edit-form d-none">
                                            <input type="text" class="form-control form-control-sm" value="{{ $department->description }}" id="edit-description-{{ $department->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm toggle-status-btn {{ $department->is_active ? 'btn-success' : 'btn-secondary' }}" 
                                                data-department-id="{{ $department->id }}" 
                                                data-status="{{ $department->is_active ? '1' : '0' }}">
                                            {{ $department->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $department->contracts_count }} contracts</span>
                                    </td>
                                    <td>{{ $department->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm normal-actions">
                                            <a href="{{ route('contracts.index', ['department_id' => $department->id]) }}" class="btn btn-outline-info" title="View Contracts">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-primary edit-btn" data-department-id="{{ $department->id }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @if($department->contracts_count == 0)
                                                <button type="button" class="btn btn-outline-danger delete-btn" 
                                                        data-department-id="{{ $department->id }}" 
                                                        data-department-name="{{ $department->name }}" 
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-outline-secondary" disabled title="Cannot delete - has contracts">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            @endif
                                        </div>
                                        <div class="btn-group btn-group-sm edit-actions d-none">
                                            <button type="button" class="btn btn-success save-btn" data-department-id="{{ $department->id }}" title="Save">
                                                <i class="fas fa-save"></i>
                                            </button>
                                            <button type="button" class="btn btn-secondary cancel-btn" data-department-id="{{ $department->id }}" title="Cancel">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @if($departments->isEmpty())
                                <tr>
                                    <td colspan="7" class="text-center">No departments found</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('contracts.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Contract Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the department "<strong id="deleteDepartmentName"></strong>"?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Department</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Edit department inline
        $('.edit-btn').on('click', function() {
            const departmentId = $(this).data('department-id');
            const row = $('#department-' + departmentId);
            
            // Hide normal view, show edit forms
            row.find('.department-name, .department-description').addClass('d-none');
            row.find('.edit-form').removeClass('d-none');
            row.find('.normal-actions').addClass('d-none');
            row.find('.edit-actions').removeClass('d-none');
        });
        
        // Cancel edit
        $('.cancel-btn').on('click', function() {
            const departmentId = $(this).data('department-id');
            const row = $('#department-' + departmentId);
            
            // Show normal view, hide edit forms
            row.find('.department-name, .department-description').removeClass('d-none');
            row.find('.edit-form').addClass('d-none');
            row.find('.normal-actions').removeClass('d-none');
            row.find('.edit-actions').addClass('d-none');
        });
        
        // Save edit
        $('.save-btn').on('click', function() {
            const departmentId = $(this).data('department-id');
            const name = $('#edit-name-' + departmentId).val();
            const description = $('#edit-description-' + departmentId).val();
            
            // AJAX request to update department
            $.ajax({
                url: '{{ url("departments") }}/' + departmentId,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    name: name,
                    description: description
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    console.log(xhr);
                    alert('Error updating department. Please try again.');
                }
            });
        });
        
        // Toggle status
        $('.toggle-status-btn').on('click', function() {
            const departmentId = $(this).data('department-id');
            const $btn = $(this);
            
            $.ajax({
                url: '{{ url("departments") }}/' + departmentId + '/toggle',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    console.log(xhr);
                    alert('Error updating status. Please try again.');
                }
            });
        });
        
        // Delete department
        $('.delete-btn').on('click', function() {
            const departmentId = $(this).data('department-id');
            const departmentName = $(this).data('department-name');
            
            $('#deleteDepartmentName').text(departmentName);
            $('#deleteForm').attr('action', '{{ url("departments") }}/' + departmentId);
            $('#deleteModal').modal('show');
        });
    });
</script>
@endpush