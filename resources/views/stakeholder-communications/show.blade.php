@extends('layouts.master')

@section('title', 'Communication Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">Communication Details</h4>
                        <p class="text-muted mb-0">{{ $stakeholder->name }} - {{ $stakeholder->organization }}</p>
                    </div>
                    <div>
                        <a href="{{ route('stakeholder-communications.edit', [$stakeholder, $communication]) }}" class="btn btn-warning text-white">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('stakeholder-communications.index', $stakeholder) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Meeting Information</h5>
                            <table class="table">
                                <tr>
                                    <th width="150">Date & Time</th>
                                    <td>{{ $communication->meeting_date->format('M d, Y') }} at {{ \Carbon\Carbon::parse($communication->meeting_time)->format('h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>
                                        @php
                                            $badges = [
                                                'in-person' => 'success',
                                                'video' => 'info',
                                                'phone' => 'primary',
                                                'email' => 'secondary'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $badges[$communication->meeting_type] }}">
                                            {{ ucfirst($communication->meeting_type) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Location</th>
                                    <td>{{ $communication->location ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Recorded By</th>
                                    <td>{{ $communication->user->name }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Follow-up Information</h5>
                            <table class="table">
                                <tr>
                                    <th width="150">Follow-up Date</th>
                                    <td>
                                        @if($communication->follow_up_date)
                                            <span class="badge bg-{{ $communication->follow_up_date->isPast() ? 'danger' : 'warning' }}">
                                                {{ $communication->follow_up_date->format('M d, Y') }}
                                            </span>
                                        @else
                                            <span class="text-muted">No follow-up needed</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created</th>
                                    <td>{{ $communication->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated</th>
                                    <td>{{ $communication->updated_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>Attendees</h5>
                        <div class="card">
                            <div class="card-body">
                                {!! nl2br(e($communication->attendees)) !!}
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>Discussion Points</h5>
                        <div class="card">
                            <div class="card-body">
                                {!! nl2br(e($communication->discussion_points)) !!}
                            </div>
                        </div>
                    </div>

                    @if($communication->action_items)
                    <div class="mb-4">
                        <h5>Action Items</h5>
                        <div class="card">
                            <div class="card-body">
                                {!! nl2br(e($communication->action_items)) !!}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($communication->follow_up_notes)
                    <div class="mb-4">
                        <h5>Follow-up Notes</h5>
                        <div class="card">
                            <div class="card-body">
                                {!! nl2br(e($communication->follow_up_notes)) !!}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
