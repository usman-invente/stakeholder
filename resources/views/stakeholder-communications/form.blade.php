@extends('layouts.master')

@section('title', isset($communication) ? 'Edit Communication Record' : 'Record New Communication')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ isset($communication) ? 'Edit Communication Record' : 'Record New Communication' }}</h4>
                    <p class="text-muted mb-0">Stakeholder: {{ $stakeholder->name }} - {{ $stakeholder->organization }}</p>
                </div>
                <div class="card-body">
                    <form action="{{ isset($communication) 
                        ? route('stakeholder-communications.update', [$stakeholder, $communication]) 
                        : route('stakeholder-communications.store', $stakeholder) }}" 
                        method="POST">
                        @csrf
                        @if(isset($communication))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="meeting_date" class="form-label">Meeting Date</label>
                                    <input type="date" class="form-control @error('meeting_date') is-invalid @enderror" 
                                        id="meeting_date" name="meeting_date" 
                                        value="{{ old('meeting_date', isset($communication) ? $communication->meeting_date->format('Y-m-d') : '') }}" 
                                        required>
                                    @error('meeting_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="meeting_time" class="form-label">Meeting Time</label>
                                    <input type="time" class="form-control @error('meeting_time') is-invalid @enderror" 
                                        id="meeting_time" name="meeting_time" 
                                        value="{{ old('meeting_time', isset($communication) ? \Carbon\Carbon::parse($communication->meeting_time)->format('H:i') : '') }}" 
                                        required>
                                    @error('meeting_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="meeting_type" class="form-label">Meeting Type</label>
                                    <select class="form-select @error('meeting_type') is-invalid @enderror" id="meeting_type" name="meeting_type" required>
                                        <option value="">Select Type</option>
                                        @foreach(['in-person', 'video', 'phone', 'email'] as $type)
                                            <option value="{{ $type }}" 
                                                {{ old('meeting_type', $communication->meeting_type ?? '') == $type ? 'selected' : '' }}>
                                                {{ ucfirst($type) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('meeting_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                        id="location" name="location" 
                                        value="{{ old('location', $communication->location ?? '') }}"
                                        placeholder="Optional - Meeting location or platform">
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="attendees" class="form-label">Attendees</label>
                            <textarea class="form-control @error('attendees') is-invalid @enderror" 
                                id="attendees" name="attendees" rows="2" required
                                placeholder="List all attendees (one per line or comma-separated)">{{ old('attendees', $communication->attendees ?? '') }}</textarea>
                            @error('attendees')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="discussion_points" class="form-label">Discussion Points</label>
                            <textarea class="form-control @error('discussion_points') is-invalid @enderror" 
                                id="discussion_points" name="discussion_points" rows="4" required
                                placeholder="What was discussed during the meeting?">{{ old('discussion_points', $communication->discussion_points ?? '') }}</textarea>
                            @error('discussion_points')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="action_items" class="form-label">Action Items</label>
                            <textarea class="form-control @error('action_items') is-invalid @enderror" 
                                id="action_items" name="action_items" rows="3"
                                placeholder="List any action items or next steps (optional)">{{ old('action_items', $communication->action_items ?? '') }}</textarea>
                            @error('action_items')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="follow_up_notes" class="form-label">Follow-up Notes</label>
                                    <textarea class="form-control @error('follow_up_notes') is-invalid @enderror" 
                                        id="follow_up_notes" name="follow_up_notes" rows="2"
                                        placeholder="Any follow-up notes (optional)">{{ old('follow_up_notes', $communication->follow_up_notes ?? '') }}</textarea>
                                    @error('follow_up_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="follow_up_date" class="form-label">Follow-up Date</label>
                                    <input type="date" class="form-control @error('follow_up_date') is-invalid @enderror" 
                                        id="follow_up_date" name="follow_up_date" 
                                        value="{{ old('follow_up_date', isset($communication) && $communication->follow_up_date ? $communication->follow_up_date->format('Y-m-d') : '') }}">
                                    @error('follow_up_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('stakeholder-communications.index', $stakeholder) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ isset($communication) ? 'Update' : 'Save' }} Communication Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
