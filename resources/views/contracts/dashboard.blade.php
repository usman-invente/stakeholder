@extends('layouts.master')

@section('title', 'Contract Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Contract Management Dashboard</h5>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-info text-white h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $stats['total_contracts'] }}</h4>
                                            <p class="mb-0">Total Contracts</p>
                                        </div>
                                        <div>
                                            <i class="fas fa-file-contract fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $stats['active_contracts'] }}</h4>
                                            <p class="mb-0">Active Contracts</p>
                                        </div>
                                        <div>
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-warning text-white h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $stats['expiring_contracts'] }}</h4>
                                            <p class="mb-0">Expiring Soon</p>
                                        </div>
                                        <div>
                                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-danger text-white h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $stats['expired_contracts'] }}</h4>
                                            <p class="mb-0">Expired</p>
                                        </div>
                                        <div>
                                            <i class="fas fa-times-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Storage Space Card -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-hdd me-2"></i>Document Storage Space</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="progress mb-2" style="height: 25px;">
                                                <div class="progress-bar bg-primary" role="progressbar" 
                                                     style="width: {{ ($stats['storage_used'] / $stats['storage_limit']) * 100 }}%" 
                                                     aria-valuenow="{{ $stats['storage_used'] }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="{{ $stats['storage_limit'] }}">
                                                    {{ number_format(($stats['storage_used'] / $stats['storage_limit']) * 100, 1) }}%
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-end">
                                                <strong>{{ $stats['storage_used'] }} MB</strong> / {{ $stats['storage_limit'] }} MB<br>
                                                <small class="text-muted">{{ $stats['storage_remaining'] }} MB remaining</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-4">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><i class="fas fa-plus fa-fw"></i> New Contract</h5>
                                    <p class="card-text flex-grow-1">Add a new contract to the system with all required details.</p>
                                    <a href="{{ route('contracts.create') }}" class="btn btn-light mt-3">Create Contract</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card bg-info text-white h-100">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><i class="fas fa-list fa-fw"></i> View All Contracts</h5>
                                    <p class="card-text flex-grow-1">Browse and manage all contracts in the system.</p>
                                    <a href="{{ route('contracts.index') }}" class="btn btn-light mt-3">View Contracts</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card bg-warning text-white h-100">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><i class="fas fa-building fa-fw"></i> Manage Departments</h5>
                                    <p class="card-text flex-grow-1">Add or modify departments for contract allocation.</p>
                                    <a href="{{ route('departments.index') }}" class="btn btn-light mt-3">Manage Departments</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Contracts Table -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Recent Contracts</h5>
                                        <div>
                                            <a href="{{ route('contracts.index') }}" class="btn btn-light btn-sm">
                                                <i class="fas fa-eye me-1"></i> View All
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Contract ID</th>
                                                    <th>Supplier Name</th>
                                                    <th>Contract Title</th>
                                                    <th>Department</th>
                                                    <th>Start Date</th>
                                                    <th>Expiry Date</th>
                                                    <th>Status</th>
                                                    <th>Contract Owner</th>
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
                                                    <td>{{ $contract->department->name ?? 'N/A' }}</td>
                                                    <td>{{ $contract->start_date->format('M d, Y') }}</td>
                                                    <td>{{ $contract->expiry_date->format('M d, Y') }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $contract->status === 'active' ? 'success' : ($contract->status === 'expiring' ? 'warning' : ($contract->status === 'expired' ? 'danger' : 'secondary')) }}">
                                                            {{ ucfirst($contract->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $contract->contract_owner }}</td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('contracts.show', $contract) }}" class="btn btn-outline-info" title="View">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-outline-primary" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
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
                                                    <td colspan="10" class="text-center">No contracts found</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Pagination -->
                                    @if($contracts->hasPages())
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
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Add any dashboard-specific JavaScript here
    });
</script>
@endsection