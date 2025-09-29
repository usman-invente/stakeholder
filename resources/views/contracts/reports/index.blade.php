@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-chart-bar me-2"></i>Contract Reports Dashboard
                    </h1>
                    <p class="text-muted">Monthly overview and contract statistics for {{ $currentDate->format('F Y') }}</p>
                </div>
                <div>
                    <a href="{{ route('contracts.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> Back to Contracts
                    </a>
                    <button class="btn btn-success" onclick="exportReport('excel')">
                        <i class="fas fa-download me-1"></i> Export Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Contracts
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($recentActivity['total_contracts']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-contract fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Added This Week
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $recentActivity['last_week'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Expiring Next 3 Months
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $expiringNext3Months->flatten()->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Overdue Renewals
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overdueContracts->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Monthly Expiry Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Contracts Expiring - Next 3 Months</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="monthlyExpiryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contract Value Overview -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Contract Values</h6>
                </div>
                <div class="card-body">
                    <div class="row no-gutters align-items-center mb-3">
                        <div class="col-sm-6">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Value
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                £{{ number_format($valueStats['total_value'], 2) }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Average Value
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                £{{ number_format($valueStats['average_value'], 2) }}
                            </div>
                        </div>
                    </div>
                    <div class="row no-gutters align-items-center">
                        <div class="col-sm-6">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Highest Value
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                £{{ number_format($valueStats['highest_value'], 2) }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Contracts with Value
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $valueStats['contracts_with_value'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Contracts Expiring Next 3 Months -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock me-1"></i>Expiring in Next 3 Months ({{ $expiringNext3Months->flatten()->count() }})
                    </h6>
                    <button class="btn btn-sm btn-outline-success" onclick="exportExpiringContracts()">
                        <i class="fas fa-download me-1"></i> Export
                    </button>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @if($expiringNext3Months->flatten()->count() > 0)
                        @foreach($monthlyBreakdown as $month)
                            @if($month['count'] > 0)
                                <div class="mb-3">
                                    <h6 class="text-primary">{{ $month['name'] }} ({{ $month['count'] }} contracts)</h6>
                                    @if(isset($expiringNext3Months[$month['month_year']]))
                                        @foreach($expiringNext3Months[$month['month_year']] as $contract)
                                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                                <div>
                                                    <strong>{{ $contract->contract_id }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $contract->contract_title }}</small>
                                                    <br>
                                                    <small class="text-info">{{ $contract->department->name }}</small>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-warning">{{ $contract->expiry_date->format('d/m/Y') }}</span>
                                                    <br>
                                                    <small class="text-muted">{{ $contract->days_until_expiry }} days</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5>No Contracts Expiring</h5>
                            <p class="text-muted">No contracts are due to expire in the next 3 months.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Overdue Renewals -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fas fa-exclamation-triangle me-1"></i>Overdue Renewals ({{ $overdueContracts->count() }})
                    </h6>
                    <button class="btn btn-sm btn-outline-success" onclick="exportOverdueContracts()">
                        <i class="fas fa-download me-1"></i> Export
                    </button>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @if($overdueContracts->count() > 0)
                        @foreach($overdueContracts as $contract)
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <strong>{{ $contract->contract_id }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $contract->contract_title }}</small>
                                    <br>
                                    <small class="text-info">{{ $contract->department->name }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-danger">Expired {{ $contract->expiry_date->format('d/m/Y') }}</span>
                                    <br>
                                    <small class="text-muted">{{ abs($contract->days_until_expiry) }} days overdue</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5>No Overdue Contracts</h5>
                            <p class="text-muted">All contracts are up to date.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Department and Supplier Statistics -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-building me-1"></i>Contracts by Department
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($departmentStats as $dept)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span>{{ $dept->name }}</span>
                            <span class="badge bg-primary">{{ $dept->contracts_count }} contracts</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-truck me-1"></i>Top Suppliers
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($supplierStats as $supplier)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <span>{{ $supplier->supplier_name }}</span>
                                @if($supplier->total_value > 0)
                                    <br>
                                    <small class="text-muted">Total Value: £{{ number_format($supplier->total_value, 2) }}</small>
                                @endif
                            </div>
                            <span class="badge bg-info">{{ $supplier->contract_count }} contracts</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-1"></i>Recent Contract Activity (Last Week)
                    </h6>
                </div>
                <div class="card-body">
                    @if($recentActivity['recent_contracts']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Contract ID</th>
                                        <th>Title</th>
                                        <th>Supplier</th>
                                        <th>Department</th>
                                        <th>Added Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentActivity['recent_contracts'] as $contract)
                                        <tr>
                                            <td><strong>{{ $contract->contract_id }}</strong></td>
                                            <td>{{ $contract->contract_title }}</td>
                                            <td>{{ $contract->supplier_name }}</td>
                                            <td>{{ $contract->department->name }}</td>
                                            <td>{{ $contract->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $contract->status == 'active' ? 'success' : ($contract->status == 'expiring' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($contract->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('contracts.show', $contract->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-info-circle fa-3x text-info mb-3"></i>
                            <h5>No Recent Activity</h5>
                            <p class="text-muted">No contracts have been added in the last week.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Expiry Chart
    const ctx = document.getElementById('monthlyExpiryChart').getContext('2d');
    const monthlyData = @json($monthlyBreakdown);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: monthlyData.map(month => month.name),
            datasets: [{
                label: 'Contracts Expiring',
                data: monthlyData.map(month => month.count),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(255, 205, 86, 0.2)'
                ],
                borderColor: [
                    'rgb(255, 99, 132)',
                    'rgb(255, 159, 64)',
                    'rgb(255, 205, 86)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Export function
    function exportReport(format, type = 'all') {
        const exportUrl = `{{ route('contracts.reports.export') }}?format=${format}&type=${type}`;
        window.open(exportUrl, '_blank');
    }
    
    // Export specific report types
    function exportExpiringContracts() {
        exportReport('excel', 'expiring_3_months');
    }
    
    function exportOverdueContracts() {
        exportReport('excel', 'overdue');
    }
    
    function exportRecentActivity() {
        exportReport('excel', 'recent_activity');
    }
</script>
@endpush