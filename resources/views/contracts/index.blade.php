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
                                    <th>Value</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contracts as $index => $contract)
                                <tr>
                                    <td>{{ ($contracts->currentPage() - 1) * $contracts->perPage() + $index + 1 }}</td>
                                    <td>{{ $contract->contract_id }}</td>
                                    <td>{{ $contract->supplier_name }}</td>
                                    <td>{{ $contract->contract_title }}</td>
                                    <td>{{ $contract->department->name ?? 'N/A' }}</td>
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
                                @endforeach
                                @if($contracts->isEmpty())
                                <tr>
                                    <td colspan="10" class="text-center">No contracts found</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $contracts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection