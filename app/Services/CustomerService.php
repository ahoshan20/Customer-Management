<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerService
{
    public function __construct(private readonly DataTableService $dataTableService) {}

    public function datatable(Request $request): array
    {
        $query = User::query()
            ->whereDoesntHave('admin')
            ->select(['id', 'name', 'email', 'status', 'created_at']);

        return $this->dataTableService->process($query, $request, [
            'searchable' => ['name', 'email', 'status'],
            'filterable' => ['status'],
            'sortable' => ['id', 'name', 'email', 'status', 'created_at'],
        ]);
    }

    public function create(array $validated): User
    {
        return User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'],
        ]);
    }

    public function findCustomerOrFail(User $customer): User
    {
        if ($customer->admin()->exists()) {
            abort(404);
        }

        return $customer;
    }

    public function update(User $customer, array $validated): User
    {
        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'status' => $validated['status'],
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $customer->update($payload);

        return $customer->fresh();
    }

    public function delete(User $customer): void
    {
        $this->findCustomerOrFail($customer)->delete();
    }
}
