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
                                    <button onclick="event.preventDefault(); document.getElementById('qr-code-modal').classList.add('show'); document.getElementById('qr-code-modal').style.display = 'block';" class="btn btn-light mt-3">Show QR Code</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0">Recent Visitors</h5>
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
@endsection