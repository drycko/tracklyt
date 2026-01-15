@extends('admin.layout')

@section('title', 'Create Tenant')

@section('header')
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">
            Create Tenant
        </h1>
        <a href="{{ route('admin.tenants.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Back to Tenants
        </a>
    </div>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow sm:rounded-lg">
            <form method="POST" action="{{ route('admin.tenants.store') }}">
                @csrf
                
                <div class="px-4 py-5 sm:p-6 space-y-6">
                    <!-- Tenant Information -->
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Tenant Information</h3>
                        
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Company Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('name') border-red-300 @enderror">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Slug -->
                            <div>
                                <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                        {{ config('app.url') }}/
                                    </span>
                                    <input type="text" name="slug" id="slug" value="{{ old('slug') }}" required
                                           class="flex-1 block w-full border-gray-300 rounded-none rounded-r-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('slug') border-red-300 @enderror">
                                </div>
                                @error('slug')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Billing Email -->
                            <div>
                                <label for="billing_email" class="block text-sm font-medium text-gray-700">Billing Email</label>
                                <input type="email" name="billing_email" id="billing_email" value="{{ old('billing_email') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('billing_email') border-red-300 @enderror">
                                @error('billing_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Subscription Setup -->
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Subscription Setup</h3>
                        
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Subscription Plan -->
                            <div>
                                <label for="subscription_plan_id" class="block text-sm font-medium text-gray-700">Subscription Plan</label>
                                <select name="subscription_plan_id" id="subscription_plan_id" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('subscription_plan_id') border-red-300 @enderror">
                                    <option value="">Select a plan</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" {{ old('subscription_plan_id') == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->name }} - ${{ $plan->price_monthly }}/month
                                        </option>
                                    @endforeach
                                </select>
                                @error('subscription_plan_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Billing Cycle -->
                            <div>
                                <label for="billing_cycle" class="block text-sm font-medium text-gray-700">Billing Cycle</label>
                                <select name="billing_cycle" id="billing_cycle" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('billing_cycle') border-red-300 @enderror">
                                    <option value="">Select billing cycle</option>
                                    <option value="monthly" {{ old('billing_cycle') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="yearly" {{ old('billing_cycle') === 'yearly' ? 'selected' : '' }}>Yearly</option>
                                </select>
                                @error('billing_cycle')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Trial Days -->
                            <div>
                                <label for="trial_days" class="block text-sm font-medium text-gray-700">Trial Days (Optional)</label>
                                <input type="number" name="trial_days" id="trial_days" value="{{ old('trial_days', 0) }}" min="0" max="90"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('trial_days') border-red-300 @enderror">
                                <p class="mt-1 text-sm text-gray-500">Leave as 0 for immediate activation, or set days for trial period (max 90 days)</p>
                                @error('trial_days')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Create Tenant
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Auto-generate slug from name
        document.getElementById('name').addEventListener('input', function() {
            const name = this.value;
            const slug = name.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
                .replace(/\s+/g, '-') // Replace spaces with hyphens
                .replace(/-+/g, '-') // Replace multiple hyphens with single hyphen
                .trim('-'); // Remove leading/trailing hyphens
            
            document.getElementById('slug').value = slug;
        });

        // Update pricing display when plan or billing cycle changes
        const planSelect = document.getElementById('subscription_plan_id');
        const billingCycleSelect = document.getElementById('billing_cycle');
        
        function updatePlanPricing() {
            const selectedPlan = planSelect.options[planSelect.selectedIndex];
            const billingCycle = billingCycleSelect.value;
            
            if (selectedPlan && selectedPlan.value && billingCycle) {
                // You can enhance this to show yearly pricing when yearly is selected
                // For now, it shows monthly pricing in the option text
            }
        }
        
        planSelect.addEventListener('change', updatePlanPricing);
        billingCycleSelect.addEventListener('change', updatePlanPricing);
    </script>
@endsection