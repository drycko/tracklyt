@extends('layouts.app')

@section('title', 'Create Invoice')
@section('header', 'Create Invoice')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Invoices</a></li>
<li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<form action="{{ route('invoices.store') }}" method="POST">
    @csrf
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Invoice Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Invoice Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                            <select class="form-select @error('client_id') is-invalid @enderror" id="client_id" name="client_id" required>
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('client_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="project_id" class="form-label">Project (Optional)</label>
                            <select class="form-select @error('project_id') is-invalid @enderror" id="project_id" name="project_id">
                                <option value="">No Project</option>
                                @foreach($projects as $project)
                                <option value="{{ $project->id }}" 
                                        data-client-id="{{ $project->client_id }}"
                                        {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('project_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="issue_date" class="form-label">Issue Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('issue_date') is-invalid @enderror" 
                                   id="issue_date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required>
                            @error('issue_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                   id="due_date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required>
                            @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                            <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                                <option value="USD" {{ old('currency', 'USD') === 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR</option>
                                <option value="GBP" {{ old('currency') === 'GBP' ? 'selected' : '' }}>GBP</option>
                                <option value="ZAR" {{ old('currency') === 'ZAR' ? 'selected' : '' }}>ZAR</option>
                            </select>
                            @error('currency')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Time Entries Section -->
            <div class="card border-0 shadow-sm mb-4" id="timeEntriesCard" style="display: none;">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Billable Time Entries</h5>
                </div>
                <div class="card-body">
                    <div id="timeEntriesList">
                        <p class="text-muted">Select a project to load unbilled time entries...</p>
                    </div>
                </div>
            </div>

            <!-- Manual Items Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Manual Items</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addManualItem()">
                        <i class="bi bi-plus-circle me-1"></i>Add Item
                    </button>
                </div>
                <div class="card-body">
                    <div id="manualItemsList">
                        <!-- Manual items will be added here -->
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Invoice Summary -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Invoice Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong id="summarySubtotal">$0.00</strong>
                    </div>
                    <div class="mb-3">
                        <label for="tax" class="form-label">Tax Amount</label>
                        <input type="number" class="form-control form-control-sm @error('tax') is-invalid @enderror" 
                               id="tax" name="tax" value="{{ old('tax', '0') }}" step="0.01" min="0">
                        @error('tax')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong class="text-primary fs-4" id="summaryTotal">$0.00</strong>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Create Invoice
                        </button>
                        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
let manualItemCounter = 0;

// Filter projects by selected client
document.getElementById('client_id').addEventListener('change', function() {
    const clientId = this.value;
    const projectSelect = document.getElementById('project_id');
    const projectOptions = projectSelect.querySelectorAll('option');
    
    // Reset project selection
    projectSelect.value = '';
    
    // Hide time entries card when client changes
    document.getElementById('timeEntriesCard').style.display = 'none';
    
    projectOptions.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block'; // Always show "No Project" option
        } else {
            const optionClientId = option.getAttribute('data-client-id');
            if (!clientId || optionClientId === clientId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        }
    });
    
    updateSummary();
});

// Load unbilled time entries when project changes
document.getElementById('project_id').addEventListener('change', function() {
    const projectId = this.value;
    const card = document.getElementById('timeEntriesCard');
    const list = document.getElementById('timeEntriesList');
    
    if (!projectId) {
        card.style.display = 'none';
        return;
    }
    
    card.style.display = 'block';
    list.innerHTML = '<p class="text-muted">Loading...</p>';
    
    fetch(`/invoices/unbilled-entries?project_id=${projectId}`)
        .then(response => response.json())
        .then(entries => {
            if (entries.length === 0) {
                list.innerHTML = '<p class="text-muted">No unbilled time entries found for this project.</p>';
                return;
            }
            
            let html = '<div class="table-responsive"><table class="table table-sm">';
            html += '<thead><tr><th><input type="checkbox" id="selectAll"></th><th>Date</th><th>User</th><th>Task</th><th>Hours</th><th>Rate</th><th>Total</th></tr></thead><tbody>';
            
            entries.forEach(entry => {
                html += `<tr>
                    <td><input type="checkbox" name="time_entries[]" value="${entry.id}" class="time-entry-checkbox" data-total="${entry.total}"></td>
                    <td>${entry.date}</td>
                    <td>${entry.user}</td>
                    <td>${entry.task}</td>
                    <td>${entry.hours}</td>
                    <td>$${entry.rate}</td>
                    <td>$${entry.total.toFixed(2)}</td>
                </tr>`;
            });
            
            html += '</tbody></table></div>';
            list.innerHTML = html;
            
            // Select all functionality
            document.getElementById('selectAll').addEventListener('change', function() {
                document.querySelectorAll('.time-entry-checkbox').forEach(cb => {
                    cb.checked = this.checked;
                });
                updateSummary();
            });
            
            // Update summary when checkboxes change
            document.querySelectorAll('.time-entry-checkbox').forEach(cb => {
                cb.addEventListener('change', updateSummary);
            });
        });
});

// Add manual item
function addManualItem() {
    manualItemCounter++;
    const html = `
        <div class="row g-2 mb-2 manual-item" id="item-${manualItemCounter}">
            <div class="col-md-5">
                <input type="text" class="form-control form-control-sm" name="manual_items[${manualItemCounter}][description]" 
                       placeholder="Description" required>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control form-control-sm manual-quantity" name="manual_items[${manualItemCounter}][quantity]" 
                       placeholder="Qty" step="0.01" min="0" value="1" required>
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control form-control-sm manual-price" name="manual_items[${manualItemCounter}][unit_price]" 
                       placeholder="Price" step="0.01" min="0" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeManualItem(${manualItemCounter})">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
    document.getElementById('manualItemsList').insertAdjacentHTML('beforeend', html);
    
    // Add event listeners for calculation
    document.querySelectorAll('.manual-quantity, .manual-price').forEach(input => {
        input.addEventListener('input', updateSummary);
    });
}

// Remove manual item
function removeManualItem(id) {
    document.getElementById(`item-${id}`).remove();
    updateSummary();
}

// Update summary
function updateSummary() {
    let subtotal = 0;
    
    // Add time entries
    document.querySelectorAll('.time-entry-checkbox:checked').forEach(cb => {
        subtotal += parseFloat(cb.dataset.total);
    });
    
    // Add manual items
    document.querySelectorAll('.manual-item').forEach(item => {
        const qty = parseFloat(item.querySelector('.manual-quantity').value) || 0;
        const price = parseFloat(item.querySelector('.manual-price').value) || 0;
        subtotal += qty * price;
    });
    
    const tax = parseFloat(document.getElementById('tax').value) || 0;
    const total = subtotal + tax;
    
    document.getElementById('summarySubtotal').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('summaryTotal').textContent = `$${total.toFixed(2)}`;
}

// Update summary when tax changes
document.getElementById('tax').addEventListener('input', updateSummary);

// Add at least one manual item on load
addManualItem();
</script>
@endsection
