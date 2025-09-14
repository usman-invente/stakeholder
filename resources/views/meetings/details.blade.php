@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Meeting Details</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h4>Visitor Information</h4>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Visitor Name:</strong></p>
                            <p class="lead">{{ $visitor->full_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Contact Number:</strong></p>
                            <p class="lead">{{ $visitor->contact_number }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Email:</strong></p>
                            <p class="lead">{{ $visitor->email ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Card No:</strong></p>
                            <p class="lead">{{ $visitor->card_no ?? 'Not provided' }}</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center mb-4">
                        <h4>Host Information</h4>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Host Name:</strong></p>
                            <p class="lead">{{ $visitor->host_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Host Email:</strong></p>
                            <p class="lead">{{ $visitor->host_email }}</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center mb-4">
                        <h4>Visit Details</h4>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Check-in Time:</strong></p>
                            <p class="lead">{{ $visitor->check_in_time ? $visitor->check_in_time->format('F j, Y, g:i a') : 'Not recorded' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Check-out Time:</strong></p>
                            <p class="lead">{{ $visitor->check_out_time ? $visitor->check_out_time->format('F j, Y, g:i a') : 'Not checked out yet' }}</p>
                        </div>
                    </div>
                    
                    @if($qrCode)
                    <hr>
                    <div class="text-center mt-4">
                        <h4>Meeting QR Code</h4>
                        <div class="mt-3">
                            <img src="data:image/png;base64,{{ $qrCode }}" alt="Meeting QR Code">
                        </div>
                        <p class="text-muted mt-2">
                            Scan this QR code to access meeting details
                        </p>
                    </div>
                    @endif
                </div>
                <div class="card-footer text-center">
                    <p class="text-muted mb-0">Meeting ID: {{ $visitor->meeting_id }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection