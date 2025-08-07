@extends('layouts.master')

@section('title', 'View Stakeholder')

@push('styles')
<style>
    /* Updated Timeline Styles */
    .timeline {
        position: relative;
        padding: 20px 0;
    }

    .timeline-item {
        padding: 20px 0;
        position: relative;
        border-left: 2px solid #e9ecef;
        margin-left: 25px;
    }

    .timeline-content {
        padding: 15px;
        background: #fff;
        border-radius: 4px;
        border: 1px solid #dee2e6;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        margin-left: 20px;
    }

    .timeline-dot {
        width: 12px;
        height: 12px;
        background: #0d6efd;
        border-radius: 50%;
        position: absolute;
        left: -7px;
        top: 28px;
    }

    .timeline-date {
        font-size: 12px;
        color: #fff;
        background: #0d6efd;
        padding: 3px 12px;
        border-radius: 15px;
        display: inline-block;
        margin-bottom: 10px;
        margin-left: 20px;
    }

    .timeline-type {
        font-size: 12px;
        text-transform: uppercase;
        color: #6c757d;
        margin-bottom: 8px;
    }

    .timeline-content h5 {
        margin-bottom: 10px;
        font-size: 14px;
        color: #333;
    }

    .timeline-attendees {
        font-size: 13px;
        color: #666;
        margin-bottom: 8px;
    }

    .timeline-assigned {
        margin-top: 10px;
    }

    .timeline-assigned .badge {
        font-size: 11px;
        padding: 4px 8px;
        margin-right: 4px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Stakeholder Details</h4>
                    <div>
                        <a href="{{ url('stakeholders/'.$stakeholder->id.'/communications') }}" class="btn btn-primary">
                            <i class="fas fa-comments"></i> Communications
                        </a>
                        <a href="{{ route('stakeholders.edit', $stakeholder) }}" class="btn btn-warning text-white">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('stakeholders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th width="150">Name</th>
                                    <td>{{ $stakeholder->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $stakeholder->email }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $stakeholder->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Organization</th>
                                    <td>{{ $stakeholder->organization }}</td>
                                </tr>
                                <tr>
                                    <th>Position</th>
                                    <td>{{ $stakeholder->position ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>
                                        <span class="badge bg-{{ $stakeholder->type === 'internal' ? 'primary' : 'secondary' }}">
                                            {{ ucfirst($stakeholder->type) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $stakeholder->address ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Notes</th>
                                    <td>{{ $stakeholder->notes ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Communications Timeline -->
            <div class="card">
                <div class="card-header">
                    <h4>Communication History</h4>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @forelse($stakeholder->communications()->with('users')->latest('meeting_date')->get() as $communication)
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div class="timeline-date">
                                    {{ $communication->meeting_date->format('M d, Y h:i A') }}
                                </div>
                                <div class="timeline-content">
                                    <a href="{{ route('stakeholder-communications.show', ['stakeholder' => $stakeholder, 'communication' => $communication]) }}" 
                                       class="text-decoration-none">
                                        <div class="timeline-type">
                                            <i class="fas fa-{{ 
                                                $communication->meeting_type === 'in-person' ? 'user' : 
                                                ($communication->meeting_type === 'video' ? 'video' : 
                                                ($communication->meeting_type === 'phone' ? 'phone' : 'envelope'))
                                            }}"></i>
                                            {{ strtoupper($communication->meeting_type) }}
                                        </div>
                                        <h5>{{ Str::limit($communication->discussion_points, 100) }}</h5>
                                        <div class="timeline-attendees">
                                            <i class="fas fa-users"></i> {{ $communication->attendees }}
                                        </div>
                                        @if($communication->users->count() > 0)
                                            <div class="timeline-assigned">
                                                <small class="text-muted">Assigned to:</small>
                                                @foreach($communication->users as $user)
                                                    <span class="badge bg-info">{{ $user->name }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">
                                No communications recorded yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
