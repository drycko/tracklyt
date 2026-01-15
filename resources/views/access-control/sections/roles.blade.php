<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-shield-check text-primary me-2"></i>Roles</h5>
        @if($user->isOwner() || $user->isSuperAdmin())
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
            <i class="bi bi-plus-circle me-1"></i>Create Role
        </button>
        @endif
    </div>
    <div class="card-body">
        <div class="row g-3">
            @forelse($roles as $role)
            <div class="col-md-6">
                <div class="card border {{ in_array($role->name, $systemRoles) ? 'border-primary' : '' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1">
                                    {{ ucwords(str_replace('-', ' ', $role->name)) }}
                                    @if(in_array($role->name, $systemRoles))
                                    <span class="badge bg-primary ms-2">System</span>
                                    @endif
                                </h6>
                                <small class="text-muted">{{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}</small>
                            </div>
                            @if($user->isOwner() || $user->isSuperAdmin())
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-muted" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editRolePermissionsModal{{ $role->id }}">
                                            <i class="bi bi-key me-2"></i>Edit Permissions
                                        </button>
                                    </li>
                                    @if(!in_array($role->name, $systemRoles))
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('access-control.destroy-role', $role) }}" method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this role?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash me-2"></i>Delete
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                            @endif
                        </div>
                        
                        <div class="mt-3">
                            <small class="text-muted d-block mb-2">Permissions ({{ $role->permissions->count() }})</small>
                            <div class="d-flex flex-wrap gap-1">
                                @forelse($role->permissions->take(5) as $permission)
                                <span class="badge bg-light text-dark border">{{ $permission->name }}</span>
                                @empty
                                <span class="text-muted small">No permissions assigned</span>
                                @endforelse
                                @if($role->permissions->count() > 5)
                                <span class="badge bg-secondary">+{{ $role->permissions->count() - 5 }} more</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <p class="text-center text-muted py-4">No roles found</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Create Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('access-control.store-role') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create New Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Role Name (slug) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required placeholder="e.g., project-manager">
                        <small class="text-muted">Use lowercase with hyphens (e.g., project-manager)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="display_name" required placeholder="e.g., Project Manager">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Brief description of this role"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Role Permissions Modals -->
@foreach($roles as $role)
<div class="modal fade" id="editRolePermissionsModal{{ $role->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('access-control.update-role-permissions', $role) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">
                        Edit Permissions: {{ ucwords(str_replace('-', ' ', $role->name)) }}
                        @if(in_array($role->name, $systemRoles))
                        <span class="badge bg-primary ms-2">System Role</span>
                        @endif
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Permissions</label>
                        @foreach($permissions as $module => $modulePermissions)
                        <div class="mb-3">
                            <h6 class="text-muted small text-uppercase mb-2">{{ ucfirst($module) }}</h6>
                            <div class="row">
                                @foreach($modulePermissions as $permission)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" 
                                               value="{{ $permission->id }}" id="perm_{{ $role->id }}_{{ $permission->id }}"
                                               {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="perm_{{ $role->id }}_{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Permissions</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
