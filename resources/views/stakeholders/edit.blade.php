@extends('layouts.master')

@section('title', 'Edit Stakeholder')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Stakeholder</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('stakeholders.update', $stakeholder) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="organization" class="form-label">Organization <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('organization') is-invalid @enderror" id="organization" name="organization"
                                        value="{{ old('organization', $stakeholder->organization) }}" required>
                                    <small class="text-muted">Required, maximum 255 characters</small>
                                    @error('organization')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                        value="{{ old('name', $stakeholder->name) }}" required>
                                    <small class="text-muted">Required, maximum 255 characters</small>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                        value="{{ old('email', $stakeholder->email) }}" required>
                                    <small class="text-muted">Required, must be a valid email address and unique</small>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                                        value="{{ old('phone', $stakeholder->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                      

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="dcg_contact_person" class="form-label">DCG Contact Person</label>
                                    <input type="text" class="form-control @error('dcg_contact_person') is-invalid @enderror" id="dcg_contact_person" name="dcg_contact_person"
                                        value="{{ old('dcg_contact_person', $stakeholder->dcg_contact_person) }}">
                                    @error('dcg_contact_person')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="method_of_engagement" class="form-label">Method of Engagement</label>
                                    <input type="text" class="form-control @error('method_of_engagement') is-invalid @enderror" id="method_of_engagement" name="method_of_engagement"
                                        value="{{ old('method_of_engagement', $stakeholder->method_of_engagement) }}">
                                    @error('method_of_engagement')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="dcg_contact_person" class="form-label">DCG Contact Person</label>
                                    <input type="text" class="form-control @error('dcg_contact_person') is-invalid @enderror" id="dcg_contact_person" name="dcg_contact_person"
                                        value="{{ old('dcg_contact_person', $stakeholder->dcg_contact_person) }}">
                                    @error('dcg_contact_person')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="method_of_engagement" class="form-label">Method of Engagement</label>
                                    <input type="text" class="form-control @error('method_of_engagement') is-invalid @enderror" id="method_of_engagement" name="method_of_engagement"
                                        value="{{ old('method_of_engagement', $stakeholder->method_of_engagement) }}">
                                    @error('method_of_engagement')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="position" class="form-label">Position</label>
                                    <input type="text" class="form-control @error('position') is-invalid @enderror" id="position" name="position"
                                        value="{{ old('position', $stakeholder->position) }}">
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Type</label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="">Select Type</option>
                                        <option value="internal" {{ old('type', $stakeholder->type) == 'internal' ? 'selected' : '' }}>Internal</option>
                                        <option value="external" {{ old('type', $stakeholder->type) == 'external' ? 'selected' : '' }}>External</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $stakeholder->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $stakeholder->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('stakeholders.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Stakeholder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
