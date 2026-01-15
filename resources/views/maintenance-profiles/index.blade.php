@extends('layouts.app')

@section('title', 'Maintenance Profiles')
@section('header', 'Maintenance Profiles')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Maintenance Profiles</li>
@endsection

@section('actions')
<a href="{{ route('maintenance-profiles.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-circle me-2"></i>New Profile
</a>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        @if($profiles->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Client</th>
                        <th>Type</th>
                        <th>Monthly Hours</th>
                        <th>Rate</th>
                        <th class="text-center">Usage</th>
                        <th>Start Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($profiles as $profile)
                    <tr>
                        <td>
                            <a href="{{ route('maintenance-profiles.show', $profile) }}" class="text-decoration-none fw-semibold">
                                {{ $profile->project->name }}
                            </a>
                        </td>
                        <td>{{ $profile->project->client->name }}</td>
                        <td>
                            @if($profile->maintenance_type === 'retainer')
                            <span class="badge bg-primary">Retainer</span>
                            @else
                            <span class="badge bg-secondary">Hourly</span>
                            @endif
                        </td>
                        <td>
                            @if($profile->maintenance_type === 'retainer')
                                {{ number_format($profile->monthly_hours, 1) }} hrs
                                @if($profile->rollover_hours > 0)
                                    <small class="text-muted">(+{{ number_format($profile->rollover_hours, 1) }} rollover)</small>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $profile->project->client->currency ?? 'ZAR' }} {{ number_format($profile->rate, 2) }}/hr</td>
                        <td class="text-center">
                            @if($profile->maintenance_type === 'retainer')
                                <div class="d-flex flex-column align-items-center">
                                    <div class="progress" style="width: 100px; height: 8px;">
                                        <div class="progress-bar {{ $profile->usage_percentage > 90 ? 'bg-danger' : ($profile->usage_percentage > 75 ? 'bg-warning' : 'bg-success') }}" 
                                             role="progressbar" 
                                             style="width: {{ min(100, $profile->usage_percentage) }}%">
                                        </div>
                                    </div>
                                    <small class="text-muted mt-1">{{ number_format($profile->used_hours, 1) }}/{{ number_format($profile->total_available_hours, 1) }} hrs</small>
                                </div>
                            @else
                                <span class="text-muted">{{ number_format($profile->used_hours, 1) }} hrs</span>
                            @endif
                        </td>
                        <td>{{ $profile->start_date->format('M d, Y') }}</td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('maintenance-profiles.show', $profile) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('maintenance-profiles.edit', $profile) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Showing {{ $profiles->firstItem() ?? 0 }} to {{ $profiles->lastItem() ?? 0 }} of {{ $profiles->total() }} profiles
            </div>
            {{ $profiles->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-calendar-check display-1 text-muted"></i>
            <h5 class="mt-3">No maintenance profiles yet</h5>
            <p class="text-muted">Create a maintenance profile to track retainer hours or hourly maintenance work.</p>
            <a href="{{ route('maintenance-profiles.create') }}" class="btn btn-primary mt-2">
                <i class="bi bi-plus-circle me-2"></i>Create First Profile
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
