<form action="{{ route('onboarding.save-team') }}" method="POST">
    @csrf
    
    <div class="text-center mb-4">
        <h4 class="fw-bold text-primary mb-2">Invite Your Team</h4>
        <p class="text-muted">Add team members to collaborate (optional)</p>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div id="team-members-container">
        <div class="team-member-row mb-3 p-3 border rounded">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Team Member #1</h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-member" style="display: none;">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-5 mb-2">
                    <input type="text" class="form-control" name="team_members[0][name]" placeholder="Full Name">
                </div>
                <div class="col-md-4 mb-2">
                    <input type="email" class="form-control" name="team_members[0][email]" placeholder="Email">
                </div>
                <div class="col-md-3 mb-2">
                    <select class="form-select" name="team_members[0][role]">
                        <option value="admin">Admin</option>
                        <option value="staff" selected>Staff</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <button type="button" class="btn btn-outline-primary btn-sm mb-4" id="add-member">
        <i class="bi bi-plus-circle me-1"></i>Add Another Member
    </button>

    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        <small>Team members will receive an email with login instructions. You can add more members later from Settings.</small>
    </div>

    <div class="d-flex justify-content-between gap-2 mt-4">
        <a href="{{ route('onboarding.index', ['step' => 1]) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
        <button type="submit" class="btn btn-primary">
            Next: Quick Start <i class="bi bi-arrow-right ms-2"></i>
        </button>
    </div>
</form>

<script>
let memberCount = 1;

document.getElementById('add-member').addEventListener('click', function() {
    const container = document.getElementById('team-members-container');
    const newRow = document.createElement('div');
    newRow.className = 'team-member-row mb-3 p-3 border rounded';
    newRow.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Team Member #${memberCount + 1}</h6>
            <button type="button" class="btn btn-sm btn-outline-danger remove-member">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div class="row">
            <div class="col-md-5 mb-2">
                <input type="text" class="form-control" name="team_members[${memberCount}][name]" placeholder="Full Name">
            </div>
            <div class="col-md-4 mb-2">
                <input type="email" class="form-control" name="team_members[${memberCount}][email]" placeholder="Email">
            </div>
            <div class="col-md-3 mb-2">
                <select class="form-select" name="team_members[${memberCount}][role]">
                    <option value="admin">Admin</option>
                    <option value="staff" selected>Staff</option>
                </select>
            </div>
        </div>
    `;
    container.appendChild(newRow);
    memberCount++;
    
    // Show remove buttons if more than 1 member
    if(memberCount > 1) {
        document.querySelectorAll('.remove-member').forEach(btn => btn.style.display = 'block');
    }
});

document.addEventListener('click', function(e) {
    if(e.target.closest('.remove-member')) {
        e.target.closest('.team-member-row').remove();
        memberCount--;
        if(memberCount <= 1) {
            document.querySelectorAll('.remove-member').forEach(btn => btn.style.display = 'none');
        }
    }
});
</script>
