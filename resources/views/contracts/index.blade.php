@extends('layouts.master')

@section('title', 'All Contracts')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Contract Management</h5>
                        <div>
                            <a href="{{ route('contracts.reports.export', ['type' => 'all', 'format' => 'excel']) }}" class="btn btn-success me-2">
                                <i class="fas fa-download me-1"></i> Export All Contracts
                            </a>
                            <a href="{{ route('contracts.reports') }}" class="btn btn-info me-2">
                                <i class="fas fa-chart-bar me-1"></i> Reports
                            </a>
                            <a href="{{ route('contracts.create') }}" class="btn btn-light">
                                <i class="fas fa-plus me-1"></i> New Contract
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('contracts.index') }}" class="d-flex align-items-end gap-3">
                                <div class="flex-grow-1">
                                    <label for="department_id" class="form-label">Filter by Department</label>
                                    <select name="department_id" id="department_id" class="form-select">
                                        <option value="">All Departments</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter me-1"></i>Filter
                                    </button>
                                </div>
                                @if(request('department_id'))
                                <div>
                                    <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Clear
                                    </a>
                                </div>
                                @endif
                            </form>
                        </div>
                    </div>

                    @if(request('department_id'))
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Showing contracts for: <strong>{{ $departments->find(request('department_id'))->name ?? 'Unknown Department' }}</strong>
                        ({{ $contracts->total() }} contract{{ $contracts->total() !== 1 ? 's' : '' }} found)
                    </div>
                    @endif

                    @if(request('department_id'))
                    <!-- Show contracts for selected department -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Contract ID</th>
                                    <th>Supplier Name</th>
                                    <th>Contract Title</th>
                                    <th>Start Date</th>
                                    <th>Expiry Date</th>
                                    <th>Value</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contracts as $contract)
                                <tr>
                                    <td>{{ $contracts->firstItem() + $loop->index }}</td>
                                    <td>{{ $contract->contract_id }}</td>
                                    <td>{{ $contract->supplier_name }}</td>
                                    <td>{{ $contract->contract_title }}</td>
                                    <td>{{ $contract->start_date->format('M d, Y') }}</td>
                                    <td>{{ $contract->expiry_date->format('M d, Y') }}</td>
                                    <td>
                                        @if($contract->contract_value)
                                            ${{ number_format($contract->contract_value, 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $contract->status === 'active' ? 'success' : ($contract->status === 'expiring' ? 'warning' : ($contract->status === 'expired' ? 'danger' : 'secondary')) }}">
                                            {{ ucfirst($contract->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('contracts.show', $contract) }}" class="btn btn-outline-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($contract->document_path)
                                                <a href="{{ route('contracts.download', $contract) }}" class="btn btn-outline-success" title="Download Document">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                            <form action="{{ route('contracts.destroy', $contract) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this contract?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No contracts found for this department</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @else
                    <!-- Show departments with contract count -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Department Name</th>
                                    <th>Total Contracts</th>
                                    <th>Active Contracts</th>
                                    <th>Expiring Contracts</th>
                                    <th>Expired Contracts</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departments->filter(function($dept) { return $dept->contracts_count > 0; }) as $department)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $department->name }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $department->contracts_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $department->active_contracts_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $department->expiring_contracts_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">{{ $department->expired_contracts_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('contracts.index', ['department_id' => $department->id]) }}" class="btn btn-outline-info" title="View All Contracts">
                                                <i class="fas fa-eye"></i> View Contracts
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No departments with contracts found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @endif

                    <!-- Pagination -->
                    @if(request('department_id') && $contracts->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <p class="text-muted mb-0">
                                Showing {{ $contracts->firstItem() }} to {{ $contracts->lastItem() }} of {{ $contracts->total() }} contracts
                            </p>
                        </div>
                        <div>
                            {{ $contracts->links() }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection