@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Dashboard</h1>
        </div>
    </div>

    @if(isset($stakeholdersNeedingCommunication) && $stakeholdersNeedingCommunication->count() > 0)
    <div class="row mb-4">
        <div class="col-md-12">
            <div id="communicationAlert" class="alert alert-warning alert-dismissible fade show" role="alert">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="alert-heading mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i> Communication Alert
                    </h4>
                    <div>
                        <button id="collapseBtn" 
                           class="btn btn-link text-warning p-0 border-0 me-2" 
                           type="button">
                            <i id="collapseIcon" class="fas fa-chevron-down"></i>
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                <p class="mb-0">{{ $stakeholdersNeedingCommunication->count() }} stakeholder(s) haven't been contacted in the last {{ $threshold }} days.</p>
                    
                <div class="collapse mt-3" id="collapseContent">
                    <div class="card card-body bg-light">
                        <ul class="list-unstyled mb-0">
                            @foreach($stakeholdersNeedingCommunication as $stakeholder)
                            <li class="mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="fas fa-user-tie"></i> {{ $stakeholder->name }}
                                        @if($stakeholder->communications->isNotEmpty())
                                            <small class="text-muted">
                                                (Last contact: {{ $stakeholder->communications->sortByDesc('meeting_date')->first()->meeting_date->format('M d, Y') }})
                                            </small>
                                        @else
                                            <small class="text-muted">(Never contacted)</small>
                                        @endif
                                    </span>
                                    <a href="{{ route('stakeholder-communications.create', $stakeholder) }}" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-plus"></i> Add Communication
                                    </a>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Users</h6>
                            <h2 class="mb-0">{{ \App\Models\User::count() }}</h2>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Today's Users</h6>
                            <h2 class="mb-0">{{ \App\Models\User::whereDate('created_at', today())->count() }}</h2>
                        </div>
                        <i class="fas fa-user-plus fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Users</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\User::latest()->take(5)->get() as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->created_at->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const collapseBtn = document.getElementById('collapseBtn');
    const collapseIcon = document.getElementById('collapseIcon');
    const collapseContent = document.getElementById('collapseContent');
    
    // Initialize Bootstrap collapse
    const myCollapse = new bootstrap.Collapse(collapseContent, {
        toggle: false
    });

    // Track collapse state
    let isCollapsed = true;

    // Handle click event
    collapseBtn.addEventListener('click', function() {
        if (isCollapsed) {
            myCollapse.show();
            collapseIcon.classList.remove('fa-chevron-down');
            collapseIcon.classList.add('fa-chevron-up');
        } else {
            myCollapse.hide();
            collapseIcon.classList.remove('fa-chevron-up');
            collapseIcon.classList.add('fa-chevron-down');
        }
        isCollapsed = !isCollapsed;
    });

    // Handle Bootstrap events to ensure icon state stays correct
    collapseContent.addEventListener('hidden.bs.collapse', function () {
        collapseIcon.classList.remove('fa-chevron-up');
        collapseIcon.classList.add('fa-chevron-down');
        isCollapsed = true;
    });

    collapseContent.addEventListener('shown.bs.collapse', function () {
        collapseIcon.classList.remove('fa-chevron-down');
        collapseIcon.classList.add('fa-chevron-up');
        isCollapsed = false;
    });
});
</script>
@endpush
