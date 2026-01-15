@extends('layouts.app')

@section('title', 'Clients')

{{-- @section('breadcrumb')
<li class="breadcrumb-item active">Clients</li>
@endsection --}}

@section('header', 'Clients')

@section('actions')
<a href="{{ route('clients.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-circle me-2"></i>New Client
</a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        @if($clients->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-people display-1 text-muted"></i>
                <h4 class="mt-3">No clients yet</h4>
                <p class="text-muted">Start by adding your first client</p>
                <a href="{{ route('clients.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Add Client
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Company</th>
                            <th>Status</th>
                            <th>Projects</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clients as $client)
                        <tr>
                            <td>
                                <a href="{{ route('clients.show', $client) }}" class="text-decoration-none fw-semibold">
                                    {{ $client->name }}
                                </a>
                            </td>
                            <td>{{ $client->email }}</td>
                            <td>{{ $client->phone ?? '-' }}</td>
                            <td>{{ $client->company_name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $client->is_active ? 'success' : 'secondary' }}">
                                    {{ $client->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ $client->projects->count() }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this client?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $clients->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
