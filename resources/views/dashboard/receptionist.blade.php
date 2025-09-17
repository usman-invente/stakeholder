@extends('layouts.master')

@section('title', 'Receptionist Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Receptionist Dashboard</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card bg-info text-white h-100">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><i class="fas fa-desktop fa-fw"></i> Visitor Monitor</h5>
                                    <p class="card-text flex-grow-1">View and manage active visitor registration sessions in real-time.</p>
                                    <a href="{{ route('receptionist.view') }}" class="btn btn-light mt-3">Open Monitor</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><i class="fas fa-user-plus fa-fw"></i> New Registration</h5>
                                    <p class="card-text flex-grow-1">Start a new visitor registration on behalf of a guest.</p>
                                    <a href="{{ route('visitor.register') }}" target="_blank" class="btn btn-light mt-3">New Visitor</a>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
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
                                                    <th>Visiting Company</th>
                                                    <th>Host</th>
                                                    <th>Check In Time</th>
                                                    <th>Check Out</th>
                                                    <th>Card Status</th>
                                                    <th>Follow-up</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($visitors ?? [] as $index => $visitor)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $visitor->full_name }}</td>
                                                    <td>{{ $visitor->contact_number }}</td>
                                                    <td>{{ $visitor->visiting_company ?? 'N/A' }}</td>
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
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge bg-{{ $visitor->follow_up_count > 0 ? ($visitor->follow_up_count > 1 ? 'danger' : 'warning') : 'secondary' }} me-2">
                                                                {{ $visitor->follow_up_count }}
                                                            </span>
                                                            <div class="btn-group btn-group-sm">
                                                                <button type="button" 
                                                                    class="btn btn-outline-primary follow-up-btn" 
                                                                    data-visitor-id="{{ $visitor->id }}"
                                                                    data-action="increment"
                                                                    title="Increment follow-up count">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                                <button type="button" 
                                                                    class="btn btn-outline-secondary follow-up-btn" 
                                                                    data-visitor-id="{{ $visitor->id }}"
                                                                    data-action="reset"
                                                                    title="Reset follow-up count">
                                                                    <i class="fas fa-undo"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <button type="button" 
                                                            class="btn btn-danger btn-sm delete-visitor-btn" 
                                                            data-visitor-id="{{ $visitor->id }}"
                                                            title="Delete Visitor">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @if(empty($visitors) || count($visitors) === 0)
                                                <tr>
                                                    <td colspan="10" class="text-center">No recent visitors</td>
                                                </tr>
                                                @endif
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

<!-- QR Code Modal -->
<div class="modal fade" id="qr-code-modal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="qrCodeModalLabel">Registration QR Code</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <div class="mb-3">
          <img src="{{ asset('images/visitor-registration-qr.png') }}" alt="Visitor Registration QR Code" class="img-fluid" style="max-width: 250px;">
        </div>
        <p>Scan this QR code to register as a visitor</p>
        <div class="alert alert-info">
          QR code directs to: {{ route('visitor.register') }}
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <a href="{{ route('visitor.register') }}" target="_blank" class="btn btn-primary">
          <i class="fas fa-external-link-alt me-1"></i> Open Registration
        </a>
      </div>
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

@push('scripts')
<script>
    // Set default dates for export modal
    document.addEventListener('DOMContentLoaded', function() {
        const exportModal = document.getElementById('exportModal');
        if (exportModal) {
            exportModal.addEventListener('show.bs.modal', function() {
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
                
                document.getElementById('start_date').value = formatDate(thirtyDaysAgo);
                document.getElementById('end_date').value = formatDate(today);
            });
        }
        
        // Generate QR code URL dynamically
        const qrModal = document.getElementById('qr-code-modal');
        if (qrModal) {
            const registrationUrl = "{{ route('visitor.register') }}";
            
            // This will generate QR code on the fly if no static image is available
            const qrCodeImg = qrModal.querySelector('img');
            if (qrCodeImg && qrCodeImg.src.includes('visitor-registration-qr.png')) {
                // Fallback to a dynamic QR code service if the static image doesn't exist
                qrCodeImg.onerror = function() {
                    this.src = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' + encodeURIComponent(registrationUrl);
                };
            }
        }
    });

    $(document).ready(function() {
        console.log('Document ready - initializing checkout functionality');
        
        // Checkout Modal handling
        $(document).on('click', '.checkout-btn', function() {
            console.log('Checkout button clicked');
            const visitorId = $(this).data('visitor-id');
            console.log('Visitor ID:', visitorId);
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
        $(document).on('click', '#saveCheckoutBtn', function() {
            console.log('Save checkout button clicked');
            const visitorId = $('#visitor_id').val();
            const checkoutTime = $('#checkout_time').val();
            const cardReturned = $('#card_returned').is(':checked') ? 1 : 0;
            
            console.log('Form data:', {
                visitorId: visitorId,
                checkoutTime: checkoutTime,
                cardReturned: cardReturned
            });
            
            // Show spinner
            $('#checkoutSpinner').removeClass('d-none');
            $(this).prop('disabled', true);
            
            $.ajax({
                url: "{{ url('/visitors') }}/" + visitorId + "/checkout",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    check_out_time: checkoutTime,
                    card_returned: cardReturned
                },
                success: function(response) {
                    console.log('Success response:', response);
                    if (response.success) {
                        // Hide modal
                        $('#checkoutModal').modal('hide');
                        
                        // Show success message
                        const alertHtml = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                Visitor checkout updated successfully!
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        $('.card-body').first().prepend(alertHtml);
                            
                        // Refresh the page to show updated visitor list
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr, status, error);
                    // Show error message
                    const alertHtml = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Error updating checkout: ${xhr.responseText}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    $('#checkoutModal .modal-body').prepend(alertHtml);
                },
                complete: function() {
                    // Hide spinner
                    $('#checkoutSpinner').addClass('d-none');
                    $('#saveCheckoutBtn').prop('disabled', false);
                }
            });
        });
        
        // Handle Card Status Toggle
        $(document).on('click', '.card-status-btn', function() {
            console.log('Card status button clicked');
            const visitorId = $(this).data('visitor-id');
            const currentStatus = $(this).data('card-status');
            const newStatus = currentStatus == '1' ? 0 : 1;
            const $button = $(this);
            
            console.log('Card status data:', {
                visitorId: visitorId,
                currentStatus: currentStatus,
                newStatus: newStatus
            });
            
            // Disable button during update
            $button.prop('disabled', true);
            
            $.ajax({
                url: "{{ url('/visitors') }}/" + visitorId + "/checkout",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    card_returned: newStatus
                },
                success: function(response) {
                    console.log('Success response:', response);
                    if (response.success) {
                        // Update button appearance and data attribute
                        if (newStatus) {
                            $button.removeClass('btn-danger').addClass('btn-success').text('Card Returned');
                        } else {
                            $button.removeClass('btn-success').addClass('btn-danger').text('Card Not Returned');
                        }
                        $button.data('card-status', newStatus.toString());
                        
                        // Show success toast/alert
                        const alertHtml = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                Card status updated successfully!
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        $('.card-body').first().prepend(alertHtml);
                        
                        // Auto-dismiss the alert after 3 seconds
                        setTimeout(function() {
                            $('.alert-success').fadeOut(function() {
                                $(this).remove();
                            });
                        }, 3000);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr, status, error);
                    // Show error message
                    const alertHtml = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Error updating card status: ${xhr.responseText}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    $('.card-body').first().prepend(alertHtml);
                    
                    // Auto-dismiss the alert after 3 seconds
                    setTimeout(function() {
                        $('.alert-danger').fadeOut(function() {
                            $(this).remove();
                        });
                    }, 3000);
                },
                complete: function() {
                    // Re-enable button
                    $button.prop('disabled', false);
                }
            });
        });
        
        // Handle Follow-up Count Updates
        $(document).on('click', '.follow-up-btn', function() {
            console.log('Follow-up button clicked');
            const visitorId = $(this).data('visitor-id');
            const action = $(this).data('action');
            const $button = $(this);
            
            console.log('Follow-up data:', {
                visitorId: visitorId,
                action: action
            });
            
            // Disable button during update
            $button.prop('disabled', true);
            
            $.ajax({
                url: "{{ url('/visitors') }}/" + visitorId + "/follow-up",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    action: action
                },
                success: function(response) {
                    console.log('Success response:', response);
                    if (response.success) {
                        // Update the follow-up count display in the table cell
                        const newCount = response.visitor.follow_up_count;
                        const visitorRow = $button.closest('tr');
                        const followUpBadge = visitorRow.find('td .badge');
                        
                        // Update the badge text
                        followUpBadge.text(newCount);
                        
                        // Update the badge color based on the count
                        followUpBadge.removeClass('bg-secondary bg-warning bg-danger');
                        if (newCount > 1) {
                            followUpBadge.addClass('bg-danger');
                        } else if (newCount == 1) {
                            followUpBadge.addClass('bg-warning');
                        } else {
                            followUpBadge.addClass('bg-secondary');
                        }
                        
                        // Show success toast/alert
                        const alertHtml = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                Follow-up count updated successfully!
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        $('.card-body').first().prepend(alertHtml);
                        
                        // Auto-dismiss the alert after 3 seconds
                        setTimeout(function() {
                            $('.alert-success').fadeOut(function() {
                                $(this).remove();
                            });
                        }, 3000);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr, status, error);
                    // Show error message
                    const alertHtml = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Error updating follow-up count: ${xhr.responseText}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    $('.card-body').first().prepend(alertHtml);
                    
                    // Auto-dismiss the alert after 3 seconds
                    setTimeout(function() {
                        $('.alert-danger').fadeOut(function() {
                            $(this).remove();
                        });
                    }, 3000);
                },
                complete: function() {
                    // Re-enable button
                    $button.prop('disabled', false);
                }
            });
        });
        
        // Handle Visitor Deletion
        $(document).on('click', '.delete-visitor-btn', function() {
            console.log('Delete button clicked');
            const visitorId = $(this).data('visitor-id');
            const $button = $(this);
            const $row = $button.closest('tr');
            
            // Show confirmation dialog
            if (confirm('Are you sure you want to delete this visitor record? This action cannot be undone.')) {
                // Disable button during deletion
                $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                
                $.ajax({
                    url: "{{ url('/visitors') }}/" + visitorId,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        console.log('Success response:', response);
                        if (response.success) {
                            // Fade out and remove the row
                            $row.fadeOut('slow', function() {
                                $(this).remove();
                                
                                // Check if table is now empty
                                if ($('table tbody tr').length === 0) {
                                    $('table tbody').append('<tr><td colspan="9" class="text-center">No recent visitors</td></tr>');
                                }
                            });
                            
                            // Show success toast/alert
                            const alertHtml = `
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    Visitor deleted successfully!
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            `;
                            $('.card-body').first().prepend(alertHtml);
                            
                            // Auto-dismiss the alert after 3 seconds
                            setTimeout(function() {
                                $('.alert-success').fadeOut(function() {
                                    $(this).remove();
                                });
                            }, 3000);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', xhr, status, error);
                        // Show error message
                        const alertHtml = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                Error deleting visitor: ${xhr.responseText}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        $('.card-body').first().prepend(alertHtml);
                        
                        // Auto-dismiss the alert after 3 seconds
                        setTimeout(function() {
                            $('.alert-danger').fadeOut(function() {
                                $(this).remove();
                            });
                        }, 3000);
                        
                        // Re-enable button
                        $button.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                    }
                });
            }
        });
    });
</script>
@endpush