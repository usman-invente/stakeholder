@extends('layouts.master')

@section('title', 'Stakeholder Communications Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Stakeholder Communications Report</h4>
                    <div>
                        <a href="{{ route('stakeholder-communications.export') }}{{ request()->has('start_date') ? '?start_date='.request('start_date').'&end_date='.request('end_date') : '' }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('stakeholder-communications.report') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="text" class="form-control flatpickr" id="start_date" name="start_date" 
                                        value="{{ request('start_date', now()->subMonths(12)->format('Y-m-d')) }}" 
                                        placeholder="Select start date">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="text" class="form-control flatpickr" id="end_date" name="end_date" 
                                        value="{{ request('end_date', now()->format('Y-m-d')) }}" 
                                        placeholder="Select end date">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="invisible">Actions</label>
                                    <div class="d-flex align-items-center">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="fas fa-filter"></i> Filter
                                        </button>
                                        <a href="{{ route('stakeholder-communications.report') }}" class="btn btn-secondary">
                                            <i class="fas fa-redo"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Stakeholder</th>
                                    <th>Meeting Date</th>
                                    <th>Meeting Time</th>
                                    <th>Type</th>
                                    <th>Location</th>
                                    <th>Attendees</th>
                                    <th>Discussion Points</th>
                                    <th>Action Items</th>
                                    <th>Follow Up Date</th>
                                    <th>Assigned Users</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $currentMonth = null;
                                @endphp
                                @foreach($communications as $communication)
                                    @php
                                        $monthYear = $communication->meeting_date->format('F Y');
                                        $meetingTime = \Carbon\Carbon::createFromFormat('H:i:s', $communication->meeting_time);
                                        $formattedTime = $meetingTime->format('h:i A'); // 12-hour format with AM/PM
                                    @endphp

                                    @if($currentMonth !== $monthYear)
                                        <tr>
                                            <td colspan="11" class="bg-light">
                                                <strong>{{ $monthYear }}</strong>
                                            </td>
                                        </tr>
                                        @php
                                            $currentMonth = $monthYear;
                                        @endphp
                                    @endif

                                    <tr>
                                        <td>{{ $communication->id }}</td>
                                        <td>{{ $communication->stakeholder->name }}</td>
                                        <td>{{ $communication->meeting_date->format('Y-m-d') }}</td>
                                        <td>{{ $formattedTime }}</td>
                                        <td>{{ ucfirst($communication->meeting_type) }}</td>
                                        <td>{{ $communication->location }}</td>
                                        <td>{{ Str::limit($communication->attendees, 50) }}</td>
                                        <td>{{ Str::limit($communication->discussion_points, 50) }}</td>
                                        <td>{{ Str::limit($communication->action_items, 50) }}</td>
                                        <td>{{ $communication->follow_up_date ? $communication->follow_up_date->format('Y-m-d') : '-' }}</td>
                                        <td>{{ $communication->users->pluck('name')->implode(', ') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $communications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize flatpickr for date inputs
    flatpickr(".flatpickr", {
        dateFormat: "Y-m-d",
        allowInput: true,
    });
});
</script>
@endpush
