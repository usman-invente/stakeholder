@extends('layouts.master')

@section('title', 'Communications History - ' . $stakeholder->name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Communications History</h1>
            <p class="text-muted">{{ $stakeholder->name }} - {{ $stakeholder->organization }}</p>
        </div>
        <div>
            <a href="{{ route('stakeholder-communications.create', $stakeholder) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Record Communication
            </a>
            <a href="{{ route('stakeholders.show', $stakeholder) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Stakeholder
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Recorded By</th>
                            <th>Follow-up Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($communications as $communication)
                        <tr>
                            <td>{{ $communication->meeting_date->format('M d, Y') }} at {{ \Carbon\Carbon::parse($communication->meeting_time)->format('h:i A') }}</td>
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
                            <td>{{ $communication->location ?? 'N/A' }}</td>
                            <td>{{ $communication->user->name }}</td>
                            <td>
                                @if($communication->follow_up_date)
                                    <span class="badge bg-{{ $communication->follow_up_date->isPast() ? 'danger' : 'warning' }}">
                                        {{ $communication->follow_up_date->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">No follow-up needed</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('stakeholder-communications.show', [$stakeholder, $communication]) }}" class="btn btn-sm btn-info text-white" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('stakeholder-communications.edit', [$stakeholder, $communication]) }}" class="btn btn-sm btn-warning text-white" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('stakeholder-communications.destroy', [$stakeholder, $communication]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this communication record?')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No communication records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $communications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
