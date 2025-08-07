@extends('layouts.master')

@section('title', 'Create Communication Log')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Add New Communication</h4>
                    <a href="{{ url('stakeholders/'.$stakeholder->id.'/communications') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('stakeholder-communications.store', $stakeholder) }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="meeting_datetime">Meeting Date & Time <span class="text-danger">*</span></label>
                                    <input type="text" name="meeting_datetime" id="meeting_datetime" 
                                        class="form-control flatpickr @error('meeting_date') is-invalid @enderror" 
                                        placeholder="Select Date & Time"
                                        autocomplete="off"
                                        value="{{ old('meeting_datetime') }}"
                                        data-input>
                                    @error('meeting_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <input type="hidden" name="meeting_date" id="meeting_date_hidden" value="{{ old('meeting_date') }}">
                                <input type="hidden" name="meeting_time" id="meeting_time_hidden" value="{{ old('meeting_time') }}">

                                <div class="form-group mb-3">
                                    <label for="meeting_type">Meeting Type <span class="text-danger">*</span></label>
                                    <select name="meeting_type" id="meeting_type" class="form-control @error('meeting_type') is-invalid @enderror" >
                                        <option value="">Select Type</option>
                                        <option value="in-person" {{ old('meeting_type') == 'in-person' ? 'selected' : '' }}>In Person</option>
                                        <option value="video" {{ old('meeting_type') == 'video' ? 'selected' : '' }}>Video</option>
                                        <option value="phone" {{ old('meeting_type') == 'phone' ? 'selected' : '' }}>Phone</option>
                                        <option value="email" {{ old('meeting_type') == 'email' ? 'selected' : '' }}>Email</option>
                                    </select>
                                    @error('meeting_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="location">Location</label>
                                    <input type="text" name="location" id="location" 
                                        class="form-control @error('location') is-invalid @enderror" 
                                        value="{{ old('location') }}">
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="attendees">Attendees <span class="text-danger">*</span></label>
                                    <textarea name="attendees" id="attendees" rows="2" 
                                        class="form-control @error('attendees') is-invalid @enderror" >{{ old('attendees') }}</textarea>
                                    @error('attendees')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="discussion_points">Discussion Points <span class="text-danger">*</span></label>
                                    <textarea name="discussion_points" id="discussion_points" rows="3" 
                                        class="form-control @error('discussion_points') is-invalid @enderror" >{{ old('discussion_points') }}</textarea>
                                    @error('discussion_points')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="action_items">Action Items</label>
                                    <textarea name="action_items" id="action_items" rows="2" 
                                        class="form-control @error('action_items') is-invalid @enderror">{{ old('action_items') }}</textarea>
                                    @error('action_items')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="follow_up_notes">Follow-up Notes</label>
                                    <textarea name="follow_up_notes" id="follow_up_notes" rows="3" 
                                        class="form-control @error('follow_up_notes') is-invalid @enderror">{{ old('follow_up_notes') }}</textarea>
                                    @error('follow_up_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="follow_up_date">Follow-up Date</label>
                                    <input type="text" name="follow_up_date" id="follow_up_date" 
                                        class="form-control flatpickr @error('follow_up_date') is-invalid @enderror" 
                                        value="{{ old('follow_up_date') }}">
                                    @error('follow_up_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="users">Assigned Users <span class="text-danger">*</span></label>
                            <select name="users[]" id="users" class="form-control @error('users') is-invalid @enderror" multiple>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                        {{ $user->id === auth()->id() ? 'selected' : '' }}
                                        {{ (is_array(old('users')) && in_array($user->id, old('users'))) ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('users')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Hold Ctrl/Cmd to select multiple users</small>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ url('stakeholders/'.$stakeholder->id.'/communications') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Communication
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#users').select2({
            placeholder: 'Select users',
            allowClear: true
        });
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize flatpickr for meeting date and time
    const meetingDatepicker = flatpickr("#meeting_datetime", {
        enableTime: true,
        dateFormat: "Y-m-d h:i K",
        time_24hr: false,
        defaultDate: null,
        noCalendar: false,
        disableMobile: true,
        minuteIncrement: 15,
        allowInput: true,
        clickOpens: true,
        placeholder: "Select Date & Time",
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates[0]) {
                // Format date as YYYY-MM-DD
                const date = selectedDates[0].toISOString().split('T')[0];
                
                // Format time as HH:mm in 24-hour format for backend
                const hours = selectedDates[0].getHours().toString().padStart(2, '0');
                const minutes = selectedDates[0].getMinutes().toString().padStart(2, '0');
                const time = `${hours}:${minutes}`;
                
                // Set the hidden input values
                document.getElementById('meeting_date_hidden').value = date;
                document.getElementById('meeting_time_hidden').value = time;
            }
        },
        locale: {
            meridiem: {
                AM: 'AM',
                PM: 'PM'
            }
        }
    });

    // Initialize flatpickr for follow up date
    const followUpDatepicker = flatpickr("#follow_up_date", {
        enableTime: false,
        dateFormat: "Y-m-d",
        minDate: "today",
        allowInput: true,
        defaultDate: null,
        placeholder: "Select Follow-up Date"
    });
});
</script>
@endpush
