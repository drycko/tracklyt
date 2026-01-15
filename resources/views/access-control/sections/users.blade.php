<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="bi bi-people text-primary me-2"></i>User Access Management</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Direct Permissions</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $assignUser)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-2" 
                                     style="width: 32px; height: 32px;">
                                    <i class="bi bi-person-fill text-primary small"></i>
                                </div>
                                <div>
                                    {{ $assignUser->name }}
                                    @if($assignUser->isSuperAdmin())
                                    <span class="badge bg-purple ms-1">Super Admin</span>
                                    @endif
                                    @if($assignUser->id === $user->id)
                                    <span class="badge bg-secondary ms-1">You</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $assignUser->email }}</td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @forelse($assignUser->roles as $role)
                                <span class="badge bg-info">{{ $role->name }}</span>
                                @empty
                                <span class="text-muted small">No roles</span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @php
                                    $directPermissions = $assignUser->permissions;
                                @endphp
                                @if($directPermissions->count() > 0)
                                    @foreach($directPermissions->take(2) as $permission)
                                    <span class="badge bg-light text-dark border">{{ $permission->name }}</span>
                                    @endforeach
                                    @if($directPermissions->count() > 2)
                                    <span class="badge bg-secondary">+{{ $directPermissions->count() - 2 }} more</span>
                                    @endif
                                @else
                                <span class="text-muted small">Via roles</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-end">
                            @if(!$assignUser->isSuperAdmin() || $user->isSuperAdmin())
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#assignRoleModal{{ $assignUser->id }}">
                                <i class="bi bi-shield-check"></i> Roles
                            </button>
                            @if($user->isOwner() || $user->isSuperAdmin())
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#assignPermissionModal{{ $assignUser->id }}">
                                <i class="bi bi-key"></i> Permissions
                            </button>
                            @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No users found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Assign Role Modals -->
@foreach($users as $assignUser)
@if(!$assignUser->isSuperAdmin() || $user->isSuperAdmin())
<div class="modal fade" id="assignRoleModal{{ $assignUser->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('access-control.assign-role', $assignUser) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Assign Roles: {{ $assignUser->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Roles</label>
                        @foreach($roles as $role)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="roles[]" 
                                   value="{{ $role->name }}" id="role_{{ $assignUser->id }}_{{ $role->id }}"
                                   {{ $assignUser->roles->contains($role->id) ? 'checked' : '' }}>
                            <label class="form-check-label" for="role_{{ $assignUser->id }}_{{ $role->id }}">
                                {{ ucwords(str_replace('-', ' ', $role->name)) }}
                                @if(in_array($role->name, $systemRoles))
                                <span class="badge bg-primary ms-1">System</span>
                                @endif
                                <br><small class="text-muted">{{ $role->permissions->count() }} {{ Str::plural('permission', $role->permissions->count()) }}</small>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Roles</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if($user->isOwner() || $user->isSuperAdmin())
<div class="modal fade" id="assignPermissionModal{{ $assignUser->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('access-control.assign-permission', $assignUser) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Assign Direct Permissions: {{ $assignUser->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <small>Direct permissions override role-based permissions. User already has permissions from their assigned roles.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Additional Permissions</label>
                        @foreach($permissions as $module => $modulePermissions)
                        <div class="mb-3">
                            <h6 class="text-muted small text-uppercase mb-2">{{ ucfirst($module) }}</h6>
                            <div class="row">
                                @foreach($modulePermissions as $permission)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" 
                                               value="{{ $permission->id }}" id="uperm_{{ $assignUser->id }}_{{ $permission->id }}"
                                               {{ $assignUser->permissions->contains($permission->id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="uperm_{{ $assignUser->id }}_{{ $permission->id }}">
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
@endif
@endforeach
