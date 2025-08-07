@extends('layouts.master')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>System Settings</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group mb-3">
                            <label for="communication_alert_threshold">Communication Alert Threshold (Days)</label>
                            <input type="number" name="communication_alert_threshold" id="communication_alert_threshold" 
                                class="form-control @error('communication_alert_threshold') is-invalid @enderror"
                                value="{{ old('communication_alert_threshold', $settings->where('key', 'communication_alert_threshold')->first()?->value) }}"
                                min="1" max="365">
                            <small class="form-text text-muted">
                                Set the number of days after which to alert if there has been no communication with a stakeholder.
                            </small>
                            @error('communication_alert_threshold')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
