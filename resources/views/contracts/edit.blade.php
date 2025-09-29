@extends('layouts.master')

@section('title', 'Edit Contract')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edit Contract: {{ $contract->contract_id }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('contracts.update', $contract) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="supplier_name" class="form-label">Supplier Name *</label>
                                    <input type="text" class="form-control @error('supplier_name') is-invalid @enderror" 
                                           id="supplier_name" name="supplier_name" value="{{ old('supplier_name', $contract->supplier_name) }}" required>
                                    @error('supplier_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="contract_title" class="form-label">Contract Title *</label>
                                    <input type="text" class="form-control @error('contract_title') is-invalid @enderror" 
                                           id="contract_title" name="contract_title" value="{{ old('contract_title', $contract->contract_title) }}" required>
                                    @error('contract_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="contract_id" class="form-label">Contract ID *</label>
                                    <input type="text" class="form-control @error('contract_id') is-invalid @enderror" 
                                           id="contract_id" name="contract_id" value="{{ old('contract_id', $contract->contract_id) }}" required>
                                    @error('contract_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="department_id" class="form-label">Responsible Department *</label>
                                    <select class="form-control @error('department_id') is-invalid @enderror" 
                                            id="department_id" name="department_id" required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id', $contract->department_id) == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="start_date" class="form-label">Start Date *</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" value="{{ old('start_date', $contract->start_date->format('Y-m-d')) }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="expiry_date" class="form-label">Expiry Date *</label>
                                    <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" 
                                           id="expiry_date" name="expiry_date" value="{{ old('expiry_date', $contract->expiry_date->format('Y-m-d')) }}" required>
                                    @error('expiry_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="renewal_terms" class="form-label">Renewal Terms *</label>
                                    <select class="form-control @error('renewal_terms') is-invalid @enderror" 
                                            id="renewal_terms" name="renewal_terms" required>
                                        <option value="">Select Renewal Terms</option>
                                        <option value="auto-renew" {{ old('renewal_terms', $contract->renewal_terms) == 'auto-renew' ? 'selected' : '' }}>Auto-Renew</option>
                                        <option value="manual" {{ old('renewal_terms', $contract->renewal_terms) == 'manual' ? 'selected' : '' }}>Manual</option>
                                    </select>
                                    @error('renewal_terms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="contract_value" class="form-label">Contract Value (Optional)</label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('contract_value') is-invalid @enderror" 
                                           id="contract_value" name="contract_value" value="{{ old('contract_value', $contract->contract_value) }}">
                                    @error('contract_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                      

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="contract_owner" class="form-label">Contract Owner *</label>
                                    <input type="text" class="form-control @error('contract_owner') is-invalid @enderror" 
                                           id="contract_owner" name="contract_owner" value="{{ old('contract_owner', $contract->contract_owner) }}" required>
                                    @error('contract_owner')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="contract_owner_email" class="form-label">Contract Owner Email *</label>
                                    <input type="email" class="form-control @error('contract_owner_email') is-invalid @enderror" 
                                           id="contract_owner_email" name="contract_owner_email" value="{{ old('contract_owner_email', $contract->contract_owner_email) }}" required>
                                    @error('contract_owner_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="document" class="form-label">Upload New Contract Document (PDF/Word - Max 10MB)</label>
                                    @if($contract->document_path)
                                        <div class="mb-2">
                                            <small class="text-muted">Current: <a href="{{ route('contracts.download', $contract) }}">Download Current Document</a></small>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control @error('document') is-invalid @enderror" 
                                           id="document" name="document" accept=".pdf,.doc,.docx">
                                    <small class="text-muted">Leave empty to keep current document</small>
                                    @error('document')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('contracts.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Back to Contracts
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Update Contract
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection