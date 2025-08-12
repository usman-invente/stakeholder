@extends('layouts.master')

@section('title', 'Stakeholders')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('stakeholders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Stakeholder
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Organization</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stakeholders as $stakeholder)
                            <tr>
                                <td>{{ $stakeholder->name }}</td>
                                <td>{{ $stakeholder->email }}</td>
                                <td>{{ $stakeholder->organization }}</td>
                                <td>
                                    <span class="badge bg-{{ $stakeholder->type === 'internal' ? 'primary' : 'secondary' }}">
                                        {{ ucfirst($stakeholder->type) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('stakeholders.show', $stakeholder) }}" class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('stakeholders.edit', $stakeholder) }}" class="btn btn-sm btn-warning text-white">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('stakeholders.destroy', $stakeholder) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this stakeholder?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No stakeholders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    Showing {{ $stakeholders->firstItem() ?? 0 }} to {{ $stakeholders->lastItem() ?? 0 }} of {{ $stakeholders->total() }} entries
                </div>
                <div>
                    {{ $stakeholders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Custom pagination styling */
    .pagination {
        margin-bottom: 0;
    }
    .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .page-link {
        color: #0d6efd;
        padding: 0.375rem 0.75rem;
    }
    .page-link:hover {
        color: #0a58ca;
        background-color: #e9ecef;
        border-color: #dee2e6;
    }
    .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }
</style>
@endpush
@endsection
