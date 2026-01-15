@extends('admin.layout')

@section('title', 'Tenants Management')

@section('header')
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">
            Tenants Management
        </h1>
        <a href="{{ route('admin.tenants.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Create Tenant
        </a>
    </div>
@endsection

@section('content')
    <!-- Filters -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <form method="GET" action="{{ route('admin.tenants.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           placeholder="Name, slug, or email">
                </div>
                
                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Subscription Status Filter -->
                <div>
                    <label for="subscription_status" class="block text-sm font-medium text-gray-700">Subscription</label>
                    <select name="subscription_status" id="subscription_status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All Subscriptions</option>
                        <option value="active" {{ request('subscription_status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="trial" {{ request('subscription_status') === 'trial' ? 'selected' : '' }}>Trial</option>
                        <option value="canceled" {{ request('subscription_status') === 'canceled' ? 'selected' : '' }}>Canceled</option>
                        <option value="expired" {{ request('subscription_status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="suspended" {{ request('subscription_status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>

                <!-- Filter Button -->
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tenants Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @forelse($tenants as $tenant)
                <li>
                    <div class="px-4 py-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-sm font-medium text-indigo-800">{{ substr($tenant->name, 0, 2) }}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="flex items-center">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            <a href="{{ route('admin.tenants.show', $tenant) }}" class="hover:text-indigo-600">
                                                {{ $tenant->name }}
                                            </a>
                                        </p>
                                        <p class="text-sm text-gray-500">{{ $tenant->slug }} • {{ $tenant->billing_email }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <!-- Subscription Info -->
                            <div class="text-right">
                                @if($tenant->subscription)
                                    <p class="text-sm font-medium text-gray-900">{{ $tenant->subscription->plan->name }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ ucfirst($tenant->subscription->billing_cycle) }} • 
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $tenant->subscription->status === 'active' ? 'bg-green-100 text-green-800' : 
                                               ($tenant->subscription->status === 'trial' ? 'bg-blue-100 text-blue-800' : 
                                               ($tenant->subscription->status === 'canceled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ ucfirst($tenant->subscription->status) }}
                                        </span>
                                    </p>
                                @else
                                    <p class="text-sm text-gray-500">No subscription</p>
                                @endif
                            </div>

                            <!-- Tenant Status -->
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $tenant->status === 'active' ? 'bg-green-100 text-green-800' : 
                                       ($tenant->status === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($tenant->status) }}
                                </span>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center space-x-2">
                                @if($tenant->status === 'active')
                                    <form method="POST" action="{{ route('admin.tenants.suspend', $tenant) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium"
                                                onclick="return confirm('Are you sure you want to suspend this tenant?')">
                                            Suspend
                                        </button>
                                    </form>
                                @elseif($tenant->status === 'suspended')
                                    <form method="POST" action="{{ route('admin.tenants.reactivate', $tenant) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900 text-sm font-medium">
                                            Reactivate
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('admin.tenants.show', $tenant) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    View
                                </a>

                                <a href="{{ route('admin.tenants.edit', $tenant) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <li>
                    <div class="px-4 py-12 text-center">
                        <div class="text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM9 9a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No tenants found</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new tenant.</p>
                            <div class="mt-6">
                                <a href="{{ route('admin.tenants.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Create Tenant
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
            @endforelse
        </ul>

        @if($tenants->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $tenants->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection