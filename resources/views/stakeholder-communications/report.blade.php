@extends('layouts.master')

@section('title', 'Stakeholder Communications Report')

@push('styles')
<style>
    /* Mobile-friendly styles for date inputs */
    input[type="date"] {
        -webkit-appearance: none;
        appearance: none;
        padding: 8px 12px;
        font-size: 16px; /* Prevents iOS zoom on focus */
        line-height: 1.5;
        width: 100%;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        background-color: #fff;
    }
    
    /* Calendar icon styling */
    input[type="date"]::-webkit-calendar-picker-indicator {
        width: 20px;
        height: 20px;
        margin-left: 0.5rem;
        cursor: pointer;
    }
    
    /* Ensure form elements have proper spacing */
    .form-group {
        margin-bottom: 1rem;
    }
    
    /* Better button sizing on mobile */
    @media (max-width: 576px) {
        .btn {
            padding: .375rem .5rem;
            font-size: .875rem;
        }
        
        input[type="date"] {
            padding: 10px 8px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Stakeholder Communications Report</h4>
                    <div>
                        <a href="{{ route('stakeholder-communications.export') }}{{ request()->has('start_date') ? '?start_date='.request('start_date').'&end_date='.request('end_date') : '' }}" class="btn btn-success me-2">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </a>
                        <!-- <a href="{{ route('stakeholder-communications.export-csv') }}{{ request()->has('start_date') ? '?start_date='.request('start_date').'&end_date='.request('end_date') : '' }}" class="btn btn-info">
                            <i class="fas fa-file-csv"></i> Export to CSV
                        </a> -->
                    </div>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <form action="{{ route('stakeholder-communications.report') }}" method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                        value="{{ request('start_date', now()->subMonths(12)->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                        value="{{ request('end_date', now()->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label class="d-none d-md-block invisible">Actions</label>
                                    <div class="d-flex align-items-center mt-2 mt-md-0">
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
                                        $meetingDate = \Carbon\Carbon::parse($communication->meeting_date);
                                        $monthYear = $meetingDate->format('F Y');
                                        
                                        // Safely format the time
                                        try {
                                            $meetingTime = \Carbon\Carbon::parse($communication->meeting_time);
                                            $formattedTime = $meetingTime->format('h:i A'); // 12-hour format with AM/PM
                                        } catch (\Exception $e) {
                                            $formattedTime = $communication->meeting_time;
                                        }
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
                                        <td>{{ $meetingDate->format('Y-m-d') }}</td>
                                        <td>{{ $formattedTime }}</td>
                                        <td>{{ ucfirst($communication->meeting_type) }}</td>
                                        <td>{{ $communication->location }}</td>
                                        <td>{{ Str::limit($communication->attendees, 50) }}</td>
                                        <td>{{ Str::limit($communication->discussion_points, 50) }}</td>
                                        <td>{{ Str::limit($communication->action_items, 50) }}</td>
                                        <td>{{ $communication->follow_up_date ? \Carbon\Carbon::parse($communication->follow_up_date)->format('Y-m-d') : '-' }}</td>
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
    // Function to check if the device is mobile/tablet
    function isMobileOrTablet() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || window.innerWidth < 768;
    }
    
    // On mobile/tablet, use native date picker
    if (!isMobileOrTablet()) {
        // Only use flatpickr on desktop
        flatpickr("#start_date, #end_date", {
            dateFormat: "Y-m-d",
            allowInput: true
        });
    }
    
    // Make sure the date inputs are in the right format
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        // Ensure the date value is in the format expected by the input
        if (input.value) {
            try {
                const date = new Date(input.value);
                if (!isNaN(date.getTime())) {
                    const formattedDate = date.toISOString().split('T')[0];
                    input.value = formattedDate;
                }
            } catch (e) {
                console.error('Error formatting date:', e);
            }
        }
        
        // Add extra styling for better mobile appearance
        input.style.paddingRight = '10px';
        input.style.paddingLeft = '10px';
    });
});
</script>
@endpush
