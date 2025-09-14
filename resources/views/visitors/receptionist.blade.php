@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Receptionist View - Live Visitor Registration</h3>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-success me-2" id="connection-status">Connected</span>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        This screen shows real-time updates as visitors fill out the registration form.
                    </div>

                    <div id="sessions-container">
                        <div class="text-center my-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Checking for active visitor sessions...</p>
                        </div>
                    </div>

                    <div class="card mt-4 mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Current Visitor Information</h5>
                        </div>
                        <div class="card-body">
                            <form id="liveDataForm" class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="display-full_name" class="form-label"><strong>Full Name:</strong></label>
                                        <input type="text" class="form-control" id="display-full_name" name="full_name">
                                    </div>
                                    <div class="mb-3">
                                        <label for="display-email" class="form-label"><strong>Email:</strong></label>
                                        <input type="email" class="form-control" id="display-email" name="email">
                                    </div>
                                    <div class="mb-3">
                                        <label for="display-card_no" class="form-label"><strong>ID/Card Number:</strong></label>
                                        <input type="text" class="form-control" id="display-card_no" name="card_no">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="display-contact_number" class="form-label"><strong>Contact Number:</strong></label>
                                        <input type="text" class="form-control" id="display-contact_number" name="contact_number">
                                    </div>
                                    <div class="mb-3">
                                        <label for="display-host_name" class="form-label"><strong>Host Name:</strong></label>
                                        <input type="text" class="form-control" id="display-host_name" name="host_name">
                                    </div>
                                    <div class="mb-3">
                                        <label for="display-host_email" class="form-label"><strong>Host Email:</strong></label>
                                        <input type="email" class="form-control" id="display-host_email" name="host_email">
                                    </div>
                                </div>
                                <!-- <div class="col-12">
                                    <button type="button" id="updateVisitorBtn" class="btn btn-primary">Update Information</button>
                                </div> -->
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Visitors</h5>
                                <div>
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                                        <i class="fas fa-file-excel me-1"></i> Export to Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Contact Number</th>
                                            <th>Host</th>
                                            <th>Check In Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($visitors ?? [] as $index => $visitor)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $visitor->full_name }}</td>
                                            <td>{{ $visitor->contact_number }}</td>
                                            <td>{{ $visitor->host_name }}</td>
                                            <td>
                                                @if($visitor->check_in_time instanceof \Carbon\Carbon)
                                                    {{ $visitor->check_in_time->format('M d, Y H:i:s') }}
                                                @else
                                                    {{ \Carbon\Carbon::parse($visitor->check_in_time)->format('M d, Y H:i:s') }}
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('visitors.export') }}" method="GET">
        <div class="modal-header">
          <h5 class="modal-title" id="exportModalLabel">Export Visitors Data</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date">
                <div class="form-text">Leave blank to include all records from the beginning</div>
            </div>
            <div class="mb-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date">
                <div class="form-text">Leave blank to include all records up to today</div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">
            <i class="fas fa-file-excel me-1"></i> Export
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        let currentSessionId = null;
        let pollingInterval;
        let sessionsPollingInterval;
        let activeSessions = [];
        let focusedFieldId = null; // Track which field is currently focused
        
        // Function to check for active sessions
        function checkActiveSessions() {
            $.ajax({
                url: "{{ route('form.active-sessions') }}",
                method: "GET",
                success: function(response) {
                    if (response.success) {
                        activeSessions = response.activeSessions;
                        
                        // Update the sessions container
                        updateSessionsContainer(activeSessions);
                        
                        // If we have sessions but none selected, select the first one
                        if (activeSessions.length > 0 && !currentSessionId) {
                            currentSessionId = activeSessions[0].session_id;
                            startPolling(currentSessionId);
                        }
                    }
                },
                error: function() {
                    console.error("Error fetching active sessions");
                }
            });
        }
        
        // Function to update the sessions container
        function updateSessionsContainer(sessions) {
            let container = $('#sessions-container');
            
            if (sessions.length === 0) {
                container.html(`
                    <div class="alert alert-warning">
                        No active visitor sessions at the moment. Waiting for visitors...
                    </div>
                `);
                return;
            }
            
            let html = `
                <div class="mb-3">
                    <label for="session-selector" class="form-label">Select active visitor session:</label>
                    <select id="session-selector" class="form-select">
            `;
            
            $.each(sessions, function(index, session) {
                const isSelected = session.session_id === currentSessionId ? 'selected' : '';
                const lastActivity = new Date(session.updated_at).toLocaleString();
                html += `<option value="${session.session_id}" ${isSelected}>
                            Session ${index + 1} - Last activity: ${lastActivity}
                         </option>`;
            });
            
            html += `
                    </select>
                </div>
            `;
            
            container.html(html);
            
            // Re-attach event listener to the new select element
            $('#session-selector').on('change', function() {
                currentSessionId = $(this).val();
                // Reset form fields
                $('#liveDataForm input').val('');
                startPolling(currentSessionId);
            });
        }
        
        // Function to start polling for a specific session
        function startPolling(sessionId) {
            // Clear any existing polling
            clearInterval(pollingInterval);
            
            if (!sessionId) {
                $('#connection-status').removeClass('bg-success').addClass('bg-warning').text('No Session Selected');
                return;
            }
            
            // Start new polling
            pollingInterval = setInterval(function() {
                $.ajax({
                    url: "{{ url('/form/fetch') }}/" + sessionId,
                    method: "GET",
                    success: function(response) {
                        if (response.success) {
                            updateDisplayFields(response.form_data);
                            $('#connection-status').removeClass('bg-warning bg-danger').addClass('bg-success').text('Connected');
                        }
                    },
                    error: function() {
                        $('#connection-status').removeClass('bg-success').addClass('bg-danger').text('Error');
                    }
                });
            }, 2000); // Poll every 2 seconds
        }
        
        // Update display fields
        function updateDisplayFields(formData) {
            if (!formData) return;
            
            $.each(formData, function(i, field) {
                if (field.name && field.name !== '_token' && field.name !== 'session_id') {
                    let element = $('#display-' + field.name);
                    
                    // Only update if the field is not currently focused by receptionist
                    if (element.length && element.val() !== field.value && focusedFieldId !== element.attr('id')) {
                        const oldValue = element.val();
                        const newValue = field.value || '';
                        
                        // Update the field value
                        element.val(newValue);
                        
                        // Highlight the field that changed
                        element.addClass('bg-warning');
                        setTimeout(function() {
                            element.removeClass('bg-warning');
                        }, 1000);
                    }
                }
            });
        }
        
        // Start checking for active sessions immediately and then every 3 seconds
        checkActiveSessions();
        sessionsPollingInterval = setInterval(checkActiveSessions, 3000);
        
        // Handle visibility changes
        $(document).on('visibilitychange', function() {
            if (document.hidden) {
                clearInterval(pollingInterval);
                clearInterval(sessionsPollingInterval);
                $('#connection-status').removeClass('bg-success').addClass('bg-warning').text('Paused');
            } else {
                checkActiveSessions();
                sessionsPollingInterval = setInterval(checkActiveSessions, 3000);
                if (currentSessionId) {
                    startPolling(currentSessionId);
                }
            }
        });
        
        // Track field focus/blur
        $('#liveDataForm input').on('focus', function() {
            focusedFieldId = $(this).attr('id');
            // Add a visual indicator that receptionist is editing this field
            $(this).addClass('border-primary');
        });
        
        $('#liveDataForm input').on('blur', function() {
            // When leaving a field, submit the updated value
            let fieldName = $(this).attr('name');
            let fieldValue = $(this).val();
            
            // Only send update if there is a current session
            if (currentSessionId) {
                // Get current form data
                let formData = $('#liveDataForm').serializeArray();
                formData.push({name: 'session_id', value: currentSessionId});
                
                // Update in background
                $.ajax({
                    url: "{{ route('form.update-receptionist') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        session_id: currentSessionId,
                        form_data: formData
                    },
                    success: function(response) {
                        // Success indicator
                    }
                });
            }
            
            // Remove the visual editing indicator
            $(this).removeClass('border-primary');
            focusedFieldId = null;
        });
        
        // Handle form updates from receptionist
        $('#updateVisitorBtn').on('click', function() {
            if (!currentSessionId) {
                alert('No active session selected.');
                return;
            }
            
            let formData = $('#liveDataForm').serializeArray();
            formData.push({name: 'session_id', value: currentSessionId});
            
            $.ajax({
                url: "{{ route('form.update-receptionist') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    session_id: currentSessionId,
                    form_data: formData
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        $('<div class="alert alert-success alert-dismissible fade show" role="alert">')
                            .text('Visitor information updated successfully!')
                            .append('<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>')
                            .prependTo('.card-body')
                            .delay(3000)
                            .fadeOut(function() {
                                $(this).remove();
                            });
                    }
                },
                error: function() {
                    // Show error message
                    $('<div class="alert alert-danger alert-dismissible fade show" role="alert">')
                        .text('Error updating visitor information. Please try again.')
                        .append('<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>')
                        .prependTo('.card-body')
                        .delay(3000)
                        .fadeOut(function() {
                            $(this).remove();
                        });
                }
            });
        });
        
        // Real-time update for individual fields when changed by receptionist
        let typingTimer;                // Timer identifier
        const doneTypingInterval = 500;  // Time in ms (0.5 seconds)
        
        // On keyup, start the countdown
        $('#liveDataForm input').on('keyup', function() {
            clearTimeout(typingTimer);
            if ($(this).val()) {
                const $this = $(this);
                typingTimer = setTimeout(function() {
                    // Send update only after user has stopped typing
                    if (!currentSessionId) return;
                    
                    // Get current form data
                    let formData = $('#liveDataForm').serializeArray();
                    formData.push({name: 'session_id', value: currentSessionId});
                    
                    // Update in background without showing alert
                    $.ajax({
                        url: "{{ route('form.update-receptionist') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            session_id: currentSessionId,
                            form_data: formData
                        },
                        success: function(response) {
                            if (response.success) {
                                // Briefly show update indicator
                                $('#connection-status')
                                    .removeClass('bg-success')
                                    .addClass('bg-info')
                                    .text('Updated');
                                
                                setTimeout(function() {
                                    $('#connection-status')
                                        .removeClass('bg-info')
                                        .addClass('bg-success')
                                        .text('Connected');
                                }, 1000);
                            }
                        }
                    });
                }, doneTypingInterval);
            }
        });
        
        // On keydown, clear the countdown 
        $('#liveDataForm input').on('keydown', function() {
            clearTimeout(typingTimer);
        });
        
        // Set default dates for export modal
        $('#exportModal').on('show.bs.modal', function() {
            const today = new Date();
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(today.getDate() - 30);
            
            // Format dates for input fields (YYYY-MM-DD)
            const formatDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };
            
            $('#start_date').val(formatDate(thirtyDaysAgo));
            $('#end_date').val(formatDate(today));
        });
    });
</script>
@endsection