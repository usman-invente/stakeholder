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
                        <div class="col-md-4 mb-4">
                            <div class="card bg-primary text-white h-100">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><i class="fas fa-qrcode fa-fw"></i> Registration QR Code</h5>
                                    <p class="card-text flex-grow-1">Display a QR code for visitors to scan for self-registration.</p>
                                    <button data-bs-toggle="modal" data-bs-target="#qr-code-modal" class="btn btn-light mt-3">Show QR Code</button>
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
                                                @if(empty($visitors) || count($visitors) === 0)
                                                <tr>
                                                    <td colspan="5" class="text-center">No recent visitors</td>
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
@endsection

@section('scripts')
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
</script>
@endsection