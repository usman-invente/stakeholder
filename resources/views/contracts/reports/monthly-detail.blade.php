@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-calendar-alt me-2"></i>Monthly Contract Report
                    </h1>
                    <p class="text-muted">Contracts expiring in {{ $startDate->format('F Y') }}</p>
                </div>
                <div>
                    <a href="{{ route('contracts.reports') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> Back to Reports
                    </a>
                    <button class="btn btn-success" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Contracts Expiring in {{ $startDate->format('F Y') }} ({{ $contracts->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    @if($contracts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Contract ID</th>
                                        <th>Title</th>
                                        <th>Supplier</th>
                                        <th>Department</th>
                                        <th>Contract Value</th>
                                        <th>Expiry Date</th>
                                        <th>Days Until Expiry</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contracts as $contract)
                                        <tr class="{{ $contract->days_until_expiry <= 7 ? 'table-danger' : ($contract->days_until_expiry <= 30 ? 'table-warning' : '') }}">
                                            <td><strong>{{ $contract->contract_id }}</strong></td>
                                            <td>{{ $contract->contract_title }}</td>
                                            <td>{{ $contract->supplier_name }}</td>
                                            <td>{{ $contract->department->name }}</td>
                                            <td>
                                                @if($contract->contract_value)
                                                    £{{ number_format($contract->contract_value, 2) }}
                                                @else
                                                    <span class="text-muted">Not specified</span>
                                                @endif
                                            </td>
                                            <td>{{ $contract->expiry_date->format('d/m/Y') }}</td>
                                            <td>
                                                @if($contract->days_until_expiry > 0)
                                                    <span class="badge bg-{{ $contract->days_until_expiry <= 7 ? 'danger' : ($contract->days_until_expiry <= 30 ? 'warning' : 'success') }}">
                                                        {{ $contract->days_until_expiry }} days
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        {{ abs($contract->days_until_expiry) }} days overdue
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $contract->status == 'active' ? 'success' : ($contract->status == 'expiring' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($contract->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('contracts.show', $contract->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('contracts.edit', $contract->id) }}" class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary Statistics -->
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title text-success">{{ $contracts->where('days_until_expiry', '>', 30)->count() }}</h5>
                                        <p class="card-text">Safe (>30 days)</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title text-warning">{{ $contracts->whereBetween('days_until_expiry', [8, 30])->count() }}</h5>
                                        <p class="card-text">Warning (8-30 days)</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title text-danger">{{ $contracts->where('days_until_expiry', '<=', 7)->where('days_until_expiry', '>=', 0)->count() }}</h5>
                                        <p class="card-text">Critical (≤7 days)</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title text-dark">£{{ number_format($contracts->sum('contract_value'), 2) }}</h5>
                                        <p class="card-text">Total Value</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5>No Contracts Expiring</h5>
                            <p class="text-muted">No contracts are due to expire in {{ $startDate->format('F Y') }}.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    @media print {
        .btn, .card-header, nav, .sidebar {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endsection