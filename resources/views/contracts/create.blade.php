@extends('layouts.master')

@section('title', 'Create New Contract')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Create New Contract</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('contracts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="supplier_name" class="form-label">Supplier Name *</label>
                                    <input type="text" class="form-control @error('supplier_name') is-invalid @enderror" 
                                           id="supplier_name" name="supplier_name" value="{{ old('supplier_name') }}" required>
                                    @error('supplier_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="contract_title" class="form-label">Contract Title *</label>
                                    <input type="text" class="form-control @error('contract_title') is-invalid @enderror" 
                                           id="contract_title" name="contract_title" value="{{ old('contract_title') }}" required>
                                    @error('contract_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="contract_id" class="form-label">Contract ID</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('contract_id') is-invalid @enderror" 
                                               id="contract_id" name="contract_id" value="{{ old('contract_id') }}" 
                                               placeholder="Leave empty for auto-generation">
                                        <button type="button" class="btn btn-outline-secondary" id="generateIdBtn">
                                            <i class="fas fa-magic"></i> Generate
                                        </button>
                                    </div>
                                    <small class="text-muted">Leave empty to auto-generate (Format: CTR-YYYY-XXX)</small>
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
                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
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
                                           id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="expiry_date" class="form-label">Expiry Date *</label>
                                    <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" 
                                           id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}" required>
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
                                        <option value="auto-renew" {{ old('renewal_terms') == 'auto-renew' ? 'selected' : '' }}>Auto-Renew</option>
                                        <option value="manual" {{ old('renewal_terms') == 'manual' ? 'selected' : '' }}>Manual</option>
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
                                           id="contract_value" name="contract_value" value="{{ old('contract_value') }}">
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
                                           id="contract_owner" name="contract_owner" value="{{ old('contract_owner') }}" required>
                                    @error('contract_owner')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="contract_owner_email" class="form-label">Contract Owner Email *</label>
                                    <input type="email" class="form-control @error('contract_owner_email') is-invalid @enderror" 
                                           id="contract_owner_email" name="contract_owner_email" value="{{ old('contract_owner_email') }}" required>
                                    @error('contract_owner_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="document" class="form-label">Upload Contract Document (PDF/Word - Max 10MB)</label>
                                    <input type="file" class="form-control @error('document') is-invalid @enderror" 
                                           id="document" name="document" accept=".pdf,.doc,.docx">
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
                                        <i class="fas fa-save me-1"></i> Create Contract
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

@push('scripts')
<script>
    $(document).ready(function() {
        console.log('Document ready, setting up generate button...');
        
        // Generate Contract ID function
        function generateContractId() {
            const $btn = $('#generateIdBtn');
            const $input = $('#contract_id');
            
            console.log('Generate button clicked');
            
            // Show loading state
            $btn.prop('disabled', true);
            $btn.html('<i class="fas fa-spinner fa-spin"></i> Generating...');
            
            // AJAX request to generate unique ID
            $.ajax({
                url: '{{ route("contracts.generate-id") }}',
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('AJAX success:', response);
                    $input.val(response.contract_id);
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error, using fallback:', error);
                    // Fallback to client-side generation
                    const year = new Date().getFullYear();
                    const randomNum = Math.floor(Math.random() * 999) + 1;
                    const paddedNum = randomNum.toString().padStart(3, '0');
                    const contractId = `CTR-${year}-${paddedNum}`;
                    $input.val(contractId);
                    console.log('Generated fallback ID:', contractId);
                },
                complete: function() {
                    // Restore button state
                    $btn.prop('disabled', false);
                    $btn.html('<i class="fas fa-magic"></i> Generate');
                }
            });
        }
        
        // Bind click event to generate button
        $(document).on('click', '#generateIdBtn', function(e) {
            e.preventDefault();
            generateContractId();
        });
        
        // Auto-generate on page load if field is empty
        setTimeout(function() {
            if ($('#contract_id').val() === '') {
                console.log('Auto-generating contract ID on page load...');
                generateContractId();
            }
        }, 500);
    });
</script>
@endpush