@extends('layouts.master')

@section('title', 'Import Stakeholders')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Import Stakeholders</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if(session('import_errors'))
                    <div class="alert alert-danger">
                        <h5>Import Errors:</h5>
                        <ul>
                            @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('stakeholders.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="file" class="form-label">Excel File</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file">
                            @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted mt-2">
                                Upload Excel file (.xlsx, .xls) or CSV file. Maximum size: 2MB.
                            </small>
                        </div>

                        <div class="alert alert-info">
                            <p><i class="fas fa-info-circle"></i> <strong>Note:</strong> Please make sure your Excel file has the correct column headers as described below.</p>
                            <p>Download a <a  download href="{{ asset('public/sample_template.xlsx') }}" class="text-primary">sample template</a> to see the expected format.</p>
                        </div>

                    

                        <div class="mb-3">
                            <a href="{{ route('stakeholders.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Stakeholders
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-file-import"></i> Import Stakeholders
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection