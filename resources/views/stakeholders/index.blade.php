@extends('layouts.master')

@section('title', 'Stakeholders')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Stakeholders</h1>
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
                        @foreach($stakeholders as $stakeholder)
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
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $stakeholders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
