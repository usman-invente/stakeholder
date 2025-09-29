@extends('layouts.master')

@section('title', 'Contract Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Contract Details: {{ $contract->contract_id }}</h5>
                        <div>
                            <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-light btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            @if($contract->document_path)
                                <a href="{{ asset('storage/' . $contract->document_path) }}" target="_blank" class="btn btn-success btn-sm">
                                    <i class="fas fa-download me-1"></i> Download Document
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Contract Information</h6>
                            <div class="mb-3">
                                <strong>Contract ID:</strong><br>
                                <span class="text-primary">{{ $contract->contract_id }}</span>
                            </div>
                            <div class="mb-3">
                                <strong>Contract Title:</strong><br>
                                {{ $contract->contract_title }}
                            </div>
                            <div class="mb-3">
                                <strong>Supplier Name:</strong><br>
                                {{ $contract->supplier_name }}
                            </div>
                            <div class="mb-3">
                                <strong>Contract Value:</strong><br>
                                @if($contract->contract_value)
                                    <span class="text-success">${{ number_format($contract->contract_value, 2) }}</span>
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </div>
                            <div class="mb-3">
                                <strong>Status:</strong><br>
                                <span class="badge bg-{{ $contract->status === 'active' ? 'success' : ($contract->status === 'expiring' ? 'warning' : ($contract->status === 'expired' ? 'danger' : 'secondary')) }} fs-6">
                                    {{ ucfirst($contract->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Dates & Terms</h6>
                            <div class="mb-3">
                                <strong>Start Date:</strong><br>
                                {{ $contract->start_date->format('M d, Y') }}
                            </div>
                            <div class="mb-3">
                                <strong>Expiry Date:</strong><br>
                                {{ $contract->expiry_date->format('M d, Y') }}
                                @if($contract->days_until_expiry > 0)
                                    <small class="text-muted">{{ $contract->formatted_expiry_status }}</small>
                                @elseif($contract->days_until_expiry == 0)
                                    <small class="text-warning">{{ $contract->formatted_expiry_status }}</small>
                                @else
                                    <small class="text-danger">{{ $contract->formatted_expiry_status }}</small>
                                @endif
                            </div>
                            <div class="mb-3">
                                <strong>Renewal Terms:</strong><br>
                                <span class="badge bg-{{ $contract->renewal_terms === 'auto-renew' ? 'info' : 'secondary' }}">
                                    {{ ucwords(str_replace('-', ' ', $contract->renewal_terms)) }}
                                </span>
                            </div>
                            <div class="mb-3">
                                <strong>Responsible Department:</strong><br>
                                {{ $contract->department->name ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Contract Owner</h6>
                            <div class="mb-3">
                                <strong>Name:</strong><br>
                                {{ $contract->contract_owner }}
                            </div>
                            <div class="mb-3">
                                <strong>Email:</strong><br>
                                <a href="mailto:{{ $contract->contract_owner_email }}">{{ $contract->contract_owner_email }}</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Document</h6>
                            @if($contract->document_path)
                                <div class="mb-3">
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    <a href="{{ route('contracts.download', $contract) }}">
                                        Download Contract Document
                                    </a>
                                </div>
                            @else
                                <p class="text-muted">No document uploaded</p>
                            @endif
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-muted mb-3">System Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Created:</strong><br>
                                    {{ $contract->created_at->format('M d, Y H:i:s') }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Last Updated:</strong><br>
                                    {{ $contract->updated_at->format('M d, Y H:i:s') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('contracts.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Back to Contracts
                                </a>
                                <div>
                                    <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-primary">
                                        <i class="fas fa-edit me-1"></i> Edit Contract
                                    </a>
                                    <form action="{{ route('contracts.destroy', $contract) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this contract?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash me-1"></i> Delete
                                        </button>
                                    </form>
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