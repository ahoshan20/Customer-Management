<?php

namespace App\Http\Controllers\Admin\CustomerManagement;

use App\Enums\ActiveInactive;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CustomerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(Request $request, CustomerService $customerService): Response
    {
        return Inertia::render('admin/customer-management/Index', [
            ...$customerService->datatable($request),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/customer-management/Create');
    }

    public function store(Request $request, CustomerService $customerService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'status' => ['required', 'in:'.ActiveInactive::ACTIVE->value.','.ActiveInactive::INACTIVE->value],
        ]);

        $customerService->create($validated);

        return redirect()->route('admin.customer.index')->with('success', 'Customer created successfully.');
    }

    // In CustomerController.php
    public function show(User $customer, CustomerService $customerService): Response
    {
        // Ensure the address relationship is loaded
        $customer->load('addresses'); 

        return Inertia::render('admin/customer-management/Show', [
            'customer' => $customer,
            // Passing the primary address specifically
            'customerAddress' => $customer->addresses()->where('is_primary', true)->first() 
                ?? $customer->addresses()->first(),
        ]);
    }

    public function edit(User $customer, CustomerService $customerService): Response
    {
        return Inertia::render('admin/customer-management/Edit', [
            'customer' => $customerService->findCustomerOrFail($customer),
            'statuses' => [
                ActiveInactive::ACTIVE->value,
                ActiveInactive::INACTIVE->value,
            ],
        ]);
    }

    public function update(Request $request, User $customer, CustomerService $customerService): RedirectResponse
    {
        $customer = $customerService->findCustomerOrFail($customer);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$customer->id],
            'password' => ['nullable', 'string', 'min:8'],
            'status' => ['required', 'in:'.ActiveInactive::ACTIVE->value.','.ActiveInactive::INACTIVE->value],
        ]);

        $customerService->update($customer, $validated);

        return redirect()->route('admin.customer.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(User $customer, CustomerService $customerService): RedirectResponse
    {
        $customerService->delete($customer);

        return redirect()->route('admin.customer.index')->with('success', 'Customer deleted successfully.');
    }
}
