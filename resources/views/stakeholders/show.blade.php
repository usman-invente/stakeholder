@extends('layouts.master')

@section('title', 'View Stakeholder')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Stakeholder Details</h4>
                    <div>
                        <a href="{{ route('stakeholders.edit', $stakeholder) }}" class="btn btn-warning text-white">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('stakeholders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th width="150">Name</th>
                                    <td>{{ $stakeholder->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $stakeholder->email }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $stakeholder->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Organization</th>
                                    <td>{{ $stakeholder->organization }}</td>
                                </tr>
                                <tr>
                                    <th>Position</th>
                                    <td>{{ $stakeholder->position ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>
                                        <span class="badge bg-{{ $stakeholder->type === 'internal' ? 'primary' : 'secondary' }}">
                                            {{ ucfirst($stakeholder->type) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $stakeholder->address ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Notes</th>
                                    <td>{{ $stakeholder->notes ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
