<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-people text-primary me-2"></i>Team Members</h5>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#inviteTeamMemberModal">
            <i class="bi bi-plus-circle me-1"></i>Invite Member
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Hourly Rate</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teamMembers as $member)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-2" 
                                     style="width: 32px; height: 32px;">
                                    <i class="bi bi-person-fill text-primary small"></i>
                                </div>
                                <div>
                                    {{ $member->name }}
                                    @if($member->isSuperAdmin())
                                    <span class="badge bg-purple ms-1">Super Admin</span>
                                    @endif
                                    @if($member->id === $user->id)
                                    <span class="badge bg-secondary ms-1">You</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $member->email }}</td>
                        <td>
                            <span class="badge bg-{{ $member->role === 'owner' ? 'warning' : ($member->role === 'admin' ? 'info' : 'secondary') }}">
                                {{ ucfirst($member->role) }}
                            </span>
                        </td>
                        <td>{{ $member->hourly_rate ? $tenant->currency . ' ' . number_format($member->hourly_rate, 2) : '-' }}</td>
                        <td>
                            @if($member->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if(!$member->isSuperAdmin() && $member->id !== $user->id && ($user->isOwner() || $user->isSuperAdmin()))
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTeamMemberModal{{ $member->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('settings.delete-team-member', $member) }}" method="POST" class="d-inline" 
                                  onsubmit="return confirm('Are you sure you want to delete this team member?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No team members found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Team Member Modals -->
@foreach($teamMembers as $member)
@if(!$member->isSuperAdmin() && $member->id !== $user->id && ($user->isOwner() || $user->isSuperAdmin()))
<div class="modal fade" id="editTeamMemberModal{{ $member->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('settings.update-team-member', $member) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Team Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ $member->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" value="{{ $member->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" name="role" required>
                            <option value="staff" {{ $member->role === 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="admin" {{ $member->role === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="owner" {{ $member->role === 'owner' ? 'selected' : '' }}>Owner</option>
                            <option value="client" {{ $member->role === 'client' ? 'selected' : '' }}>Client</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hourly Rate ({{ $tenant->currency }})</label>
                        <input type="number" step="0.01" class="form-control" name="hourly_rate" value="{{ $member->hourly_rate }}">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                               id="is_active{{ $member->id }}" {{ $member->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active{{ $member->id }}">
                            Active
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Member</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

<!-- Invite Team Member Modal -->
<div class="modal fade" id="inviteTeamMemberModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Invite Team Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Team invitation feature coming soon. For now, create users directly from the admin panel.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
