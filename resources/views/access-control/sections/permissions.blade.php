<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-key text-primary me-2"></i>Permissions</h5>
        @if($user->isOwner() || $user->isSuperAdmin())
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createPermissionModal">
            <i class="bi bi-plus-circle me-1"></i>Create Permission
        </button>
        @endif
    </div>
    <div class="card-body">
        @forelse($permissions as $module => $modulePermissions)
        <div class="mb-4">
            <h6 class="text-primary mb-3">
                <i class="bi bi-folder me-2"></i>{{ ucfirst($module) }} Module
            </h6>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Permission</th>
                            <th>Roles</th>
                            <th class="text-end" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modulePermissions as $permission)
                        <tr>
                            <td>
                                <code class="text-dark">{{ $permission->name }}</code>
                                @php
                                    $isSystemPermission = in_array($permission->name, [
                                        'clients.view', 'clients.create', 'clients.edit', 'clients.delete',
                                        'projects.view', 'projects.create', 'projects.edit', 'projects.delete',
                                        'tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete',
                                        'time-entries.view', 'time-entries.create', 'time-entries.edit', 'time-entries.delete',
                                        'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete',
                                        'quotes.view', 'quotes.create', 'quotes.edit', 'quotes.delete',
                                        'reports.view', 'settings.view', 'settings.edit',
                                    ]);
                                @endphp
                                @if($isSystemPermission)
                                <span class="badge bg-primary ms-2">System</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @forelse($permission->roles as $role)
                                    <span class="badge bg-light text-dark border">{{ $role->name }}</span>
                                    @empty
                                    <span class="text-muted small">Not assigned</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="text-end">
                                {{-- @if(!$isSystemPermission && ($user->isOwner() || $user->isSuperAdmin()))
                                <form action="{{ route('access-control.destroy-permission', $permission) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this permission?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif --}}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @empty
        <p class="text-center text-muted py-4">No permissions found</p>
        @endforelse
    </div>
</div>

<!-- Create Permission Modal -->
<div class="modal fade" id="createPermissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('access-control.store-permission') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create New Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Permission Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required placeholder="e.g., reports.export">
                        <small class="text-muted">Use format: module.action (e.g., reports.export, users.impersonate)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="display_name" required placeholder="e.g., Export Reports">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Brief description of this permission"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Permission</button>
                </div>
            </form>
        </div>
    </div>
</div>
