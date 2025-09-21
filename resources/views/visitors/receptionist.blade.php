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
                                    <div class="mb-3">
                                        <label for="display-visiting_company" class="form-label"><strong>Visiting Company:</strong></label>
                                        <select class="form-select" id="display-visiting_company" name="visiting_company">
                                            <option value="">Select Company</option>
                                            <option value="DSM Corridor Group Co. Ltd">DSM Corridor Group Co. Ltd</option>
                                             <option value="DSM Corridor Ships Chandelling Ltd">DSM Corridor Ships Chandelling Ltd</option>
                                            <option value="  Manchinchi Marine Movers">  Manchinchi Marine Movers</option>
                                            <option value="Galla Logistics">Galla Logistics</option>
                                            <option value="Scan Global Logistics">Scan Global Logistics</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="display-contact_number" class="form-label"><strong>Mobile Number:</strong></label>
                                        <input type="text" class="form-control" id="display-contact_number" name="contact_number">
                                    </div>
                                    <div class="mb-3">
                                        <label for="display-coming_from_company" class="form-label"><strong>Which company are you working for:</strong></label>
                                        <input type="text" class="form-control" id="display-coming_from_company" name="coming_from_company">
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
                                            <th>Card No</th>
                                            <th>Coming From</th>
                                            <th>Visiting Company</th>
                                            <th>Host</th>
                                            <th>Check In Time</th>
                                            <th>Checkout</th>
                                            <th>Card Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($visitors ?? [] as $index => $visitor)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $visitor->full_name }}</td>
                                            <td>{{ $visitor->contact_number }}</td>
                                            <td>{{ $visitor->card_no }}</td>
                                            <td>{{ $visitor->coming_from_company }}</td>
                                            <td>{{ $visitor->visiting_company }}</td>
                                            <td>{{ $visitor->host_name }}</td>
                                            <td>
                                                @if($visitor->check_in_time instanceof \Carbon\Carbon)
                                                    {{ $visitor->check_in_time->format('M d, Y H:i:s') }}
                                                @else
                                                    {{ \Carbon\Carbon::parse($visitor->check_in_time)->format('M d, Y H:i:s') }}
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" 
                                                    class="btn @if($visitor->check_out_time) btn-secondary disabled @else btn-warning @endif btn-sm checkout-btn" 
                                                    data-visitor-id="{{ $visitor->id }}"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#checkoutModal"
                                                    @if($visitor->check_out_time) disabled @endif>
                                                    @if($visitor->check_out_time)
                                                        Checked Out
                                                    @else
                                                        Checkout
                                                    @endif
                                                </button>
                                            </td>
                                            <td>
                                                <button type="button" 
                                                    class="btn @if($visitor->card_returned) btn-success @else btn-danger @endif btn-sm card-status-btn" 
                                                    data-visitor-id="{{ $visitor->id }}"
                                                    data-card-status="{{ $visitor->card_returned ? '1' : '0' }}">
                                                    @if($visitor->card_returned)
                                                        Card Returned
                                                    @else
                                                        Card Not Returned
                                                    @endif
                                                </button>
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

<!-- Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="checkoutModalLabel">Visitor Checkout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="checkoutForm">
          <input type="hidden" id="visitor_id" name="visitor_id">
          <div class="mb-3">
            <label for="checkout_time" class="form-label">Checkout Time</label>
            <input type="datetime-local" class="form-control" id="checkout_time" name="checkout_time">
            <div class="form-text">Leave blank to use current time</div>
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="card_returned" name="card_returned" value="1">
              <label class="form-check-label" for="card_returned">
                Card Returned
              </label>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveCheckoutBtn">
          <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="checkoutSpinner"></span>
          Save Checkout
        </button>
      </div>
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
                    if (element.length && focusedFieldId !== element.attr('id')) {
                        const oldValue = element.val();
                        const newValue = field.value || '';
                        
                        // Update the field value based on element type (input or select)
                        if (element.is('select')) {
                            // For select elements, we need to select the correct option
                            if (oldValue !== newValue) {
                                element.val(newValue);
                                element.addClass('bg-warning');
                                setTimeout(function() {
                                    element.removeClass('bg-warning');
                                }, 1000);
                            }
                        } else {
                            // For input elements
                            if (oldValue !== newValue) {
                                element.val(newValue);
                                element.addClass('bg-warning');
                                setTimeout(function() {
                                    element.removeClass('bg-warning');
                                }, 1000);
                            }
                        }
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
        $('#liveDataForm input, #liveDataForm select').on('focus', function() {
            focusedFieldId = $(this).attr('id');
            // Add a visual indicator that receptionist is editing this field
            $(this).addClass('border-primary');
        });
        
        $('#liveDataForm input, #liveDataForm select').on('blur', function() {
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
        $('#liveDataForm input, #liveDataForm select').on('keyup change', function() {
            clearTimeout(typingTimer);
            if ($(this).val() !== undefined) {
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
        $('#liveDataForm input, #liveDataForm select').on('keydown', function() {
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
        
        // Checkout Modal Functionality
        $('.checkout-btn').on('click', function() {
            const visitorId = $(this).data('visitor-id');
            $('#visitor_id').val(visitorId);
            
            // Set default checkout time to now
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            
            const formattedNow = `${year}-${month}-${day}T${hours}:${minutes}`;
            $('#checkout_time').val(formattedNow);
        });
        
        // Handle Checkout Form Submission
        $('#saveCheckoutBtn').on('click', function() {
            const visitorId = $('#visitor_id').val();
            const checkoutTime = $('#checkout_time').val();
            const cardReturned = $('#card_returned').is(':checked') ? 1 : 0;
            
            // Show spinner
            $('#checkoutSpinner').removeClass('d-none');
            $(this).prop('disabled', true);
            
            $.ajax({
                url: `/visitors/${visitorId}/checkout`,
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    check_out_time: checkoutTime,
                    card_returned: cardReturned
                },
                success: function(response) {
                    if (response.success) {
                        // Hide modal
                        $('#checkoutModal').modal('hide');
                        
                        // Show success message
                        $('<div class="alert alert-success alert-dismissible fade show" role="alert">')
                            .text('Visitor checkout updated successfully!')
                            .append('<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>')
                            .prependTo('.card-body')
                            .delay(3000)
                            .fadeOut(function() {
                                $(this).remove();
                            });
                            
                        // Refresh the page to show updated visitor list
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    }
                },
                error: function() {
                    // Show error message
                    $('<div class="alert alert-danger alert-dismissible fade show" role="alert">')
                        .text('Error updating checkout. Please try again.')
                        .append('<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>')
                        .prependTo('#checkoutModal .modal-body')
                        .delay(3000)
                        .fadeOut(function() {
                            $(this).remove();
                        });
                },
                complete: function() {
                    // Hide spinner
                    $('#checkoutSpinner').addClass('d-none');
                    $('#saveCheckoutBtn').prop('disabled', false);
                }
            });
        });
        
        // Handle Card Status Toggle
        $('.card-status-btn').on('click', function() {
            const visitorId = $(this).data('visitor-id');
            const currentStatus = $(this).data('card-status');
            const newStatus = currentStatus == '1' ? 0 : 1;
            const $button = $(this);
            
            // Disable button during update
            $button.prop('disabled', true);
            
            $.ajax({
                url: `/visitors/${visitorId}/checkout`,
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    card_returned: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        // Update button appearance and data attribute
                        if (newStatus) {
                            $button.removeClass('btn-danger').addClass('btn-success').text('Card Returned');
                        } else {
                            $button.removeClass('btn-success').addClass('btn-danger').text('Card Not Returned');
                        }
                        $button.data('card-status', newStatus.toString());
                        
                        // Show success toast/alert
                        $('<div class="alert alert-success alert-dismissible fade show" role="alert">')
                            .text('Card status updated successfully!')
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
                        .text('Error updating card status. Please try again.')
                        .append('<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>')
                        .prependTo('.card-body')
                        .delay(3000)
                        .fadeOut(function() {
                            $(this).remove();
                        });
                },
                complete: function() {
                    // Re-enable button
                    $button.prop('disabled', false);
                }
            });
        });
    });
</script>
@endsection