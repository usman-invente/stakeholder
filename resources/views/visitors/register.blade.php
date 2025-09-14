@extends('layouts.guest')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Visitor Registration</h3>
                </div>

                <div class="card-body">
                    <div id="success-message" class="alert alert-success d-none" role="alert">
                        Registration successful! Please wait for your host to receive you.
                    </div>
                    
                    <div id="receptionist-edit-message" class="alert alert-info d-none" role="alert">
                        <i class="fas fa-sync-alt"></i> A receptionist has updated some of your information.
                    </div>

                    <form id="guestForm">
                        @csrf
                        <input type="hidden" name="session_id" id="session_id" value="{{ session()->getId() }}">
                        
                        <div class="form-group row mb-3">
                            <label for="full_name" class="col-md-4 col-form-label text-md-end">Full Name *</label>
                            <div class="col-md-6">
                                <input id="full_name" type="text" class="form-control @error('full_name') is-invalid @enderror" name="full_name" value="{{ old('full_name') }}" required autocomplete="name" autofocus>
                                <span class="invalid-feedback" role="alert" id="full_name-error"></span>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">Email Address</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email">
                                <span class="invalid-feedback" role="alert" id="email-error"></span>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="card_no" class="col-md-4 col-form-label text-md-end">ID/Card Number</label>
                            <div class="col-md-6">
                                <input id="card_no" type="text" class="form-control @error('card_no') is-invalid @enderror" name="card_no" value="{{ old('card_no') }}">
                                <span class="invalid-feedback" role="alert" id="card_no-error"></span>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="contact_number" class="col-md-4 col-form-label text-md-end">Contact Number *</label>
                            <div class="col-md-6">
                                <input id="contact_number" type="text" class="form-control @error('contact_number') is-invalid @enderror" name="contact_number" value="{{ old('contact_number') }}" required>
                                <span class="invalid-feedback" role="alert" id="contact_number-error"></span>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="host_name" class="col-md-4 col-form-label text-md-end">Host Name *</label>
                            <div class="col-md-6">
                                <input id="host_name" type="text" class="form-control @error('host_name') is-invalid @enderror" name="host_name" value="{{ old('host_name') }}" required>
                                <span class="invalid-feedback" role="alert" id="host_name-error"></span>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="host_email" class="col-md-4 col-form-label text-md-end">Host Email *</label>
                            <div class="col-md-6">
                                <input id="host_email" type="email" class="form-control @error('host_email') is-invalid @enderror" name="host_email" value="{{ old('host_email') }}" required>
                                <span class="invalid-feedback" role="alert" id="host_email-error"></span>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" id="registerBtn" class="btn btn-primary">
                                    <span id="registerText">Register</span>
                                    <span id="registerSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <small class="text-muted">* Required fields</small>
                    <p class="text-muted mt-2 mb-0">
                        <small>Session ID: <span id="display-session-id">{{ session()->getId() }}</span></small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        let sessionId = "{{ session()->getId() }}";
        let pollingInterval;
        let lastUpdateTime = new Date().getTime();
        let isEditing = false;
        let focusedFieldId = null; // Track which field is currently focused
        
        // Function to poll for updates from receptionist
        function startPolling() {
            pollingInterval = setInterval(function() {
                if (!isEditing) {  // Only poll if user is not currently editing a field
                    $.ajax({
                        url: "{{ url('/form/fetch') }}/" + sessionId,
                        method: "GET",
                        success: function(response) {
                            if (response.success) {
                                // Check if the update is newer than our last edit
                                let serverUpdateTime = new Date(response.updated_at).getTime();
                                
                                // Always update if receptionist edited
                                if (response.receptionist_edit || serverUpdateTime > lastUpdateTime) {
                                    updateFormFields(response.form_data);
                                }
                            }
                        }
                    });
                }
            }, 1000); // Poll every 1 second for more real-time updates
        }
        
        // Function to update form fields when receptionist makes changes
        function updateFormFields(formData) {
            if (!formData) return;
            
            let receptionistEdited = false;
            
            $.each(formData, function(i, field) {
                if (field.name && field.name !== '_token' && field.name !== 'session_id') {
                    let element = $('#' + field.name);
                    
                    // Only update if the field is not currently being edited by the visitor
                    // AND the value has actually changed
                    if (element.length && 
                        element.val() !== field.value && 
                        focusedFieldId !== element.attr('id')) {
                        
                        // Don't update empty values from receptionist (they're probably still typing)
                        if (field.receptionist_edit && !field.value) {
                            return;
                        }
                        
                        // Update the field value silently without any highlight effects
                        element.val(field.value || '');
                        
                        // Track if receptionist made edits (for notification purposes only)
                        if (field.receptionist_edit) {
                            receptionistEdited = true;
                        }
                    }
                }
            });
            
            // Show receptionist edit notification if applicable (without highlighting fields)
            if (receptionistEdited) {
                $('#receptionist-edit-message').removeClass('d-none');
                setTimeout(function() {
                    $('#receptionist-edit-message').addClass('d-none');
                }, 5000);
            }
        }
        
        // Input event handlers for real-time updates
        $('#guestForm input').on('focus', function() {
            // Set flag that user is editing
            isEditing = true;
            focusedFieldId = $(this).attr('id');
        });
        
        $('#guestForm input').on('blur', function() {
            // User has finished editing
            isEditing = false;
            focusedFieldId = null;
        });
        
        $('#guestForm input').on('input', function() {
            let formData = $('#guestForm').serializeArray();
            lastUpdateTime = new Date().getTime(); // Update the last edit time
            
            $.ajax({
                url: "{{ route('form.update') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    session_id: sessionId,
                    form_data: formData
                },
                error: function(xhr, status, error) {
                    console.error("Error updating form data:", error);
                }
            });
        });
        
        // Form submission
        $('#guestForm').on('submit', function(e) {
            e.preventDefault();
            
            // Disable button and show loader
            $('#registerBtn').prop('disabled', true);
            $('#registerText').addClass('d-none');
            $('#registerSpinner').removeClass('d-none');
            
            let formData = new FormData(this);
            formData.append('session_id', sessionId);
            
            $.ajax({
                url: "{{ route('visitor.store') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Clear polling interval once form is submitted
                        clearInterval(pollingInterval);
                        
                        $('#success-message').text(response.message).removeClass('d-none');
                        $('#guestForm')[0].reset();
                        
                        // Scroll to the top to see the success message
                        window.scrollTo(0, 0);
                        
                        // Hide the success message after 5 seconds
                        setTimeout(function() {
                            $('#success-message').addClass('d-none');
                        }, 5000);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key + '-error').text(value[0]);
                            $('#' + key).addClass('is-invalid');
                        });
                    }
                },
                complete: function() {
                    // Re-enable button and hide loader
                    $('#registerBtn').prop('disabled', false);
                    $('#registerText').removeClass('d-none');
                    $('#registerSpinner').addClass('d-none');
                }
            });
        });
        
        // Start polling for updates when page loads
        startPolling();
        
        // Handle visibility changes (pause/resume polling)
        $(document).on('visibilitychange', function() {
            if (document.hidden) {
                clearInterval(pollingInterval);
            } else {
                startPolling();
            }
        });
    });
</script>
@endsection