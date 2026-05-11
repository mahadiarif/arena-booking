<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\CreditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(
        protected CreditService $creditService,
    ) {}

    public function index(Request $request): View
    {
        $customers = Customer::when(
                $request->filled('search'),
                fn ($q) => $q->search($request->search)
            )
            ->withCount('bookings')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('admin.customers.create');
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        Customer::create(array_merge(
            $request->validated(),
            ['created_by' => auth()->id()]
        ));

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Customer added successfully.');
    }

    public function show(Customer $customer): View
    {
        $customer->load(['bookings.venue', 'bookings.slot', 'creditTransactions']);

        $activeBookings = $customer->bookings->filter(
            fn ($b) => ! $b->status->isTerminal()
        );

        $stats = [
            'total_bookings' => $customer->total_bookings,
            'total_spent'    => $customer->bookings->sum('paid_amount'),
            'outstanding'    => $activeBookings->sum(fn ($b) => $b->due_amount),
            'credit_balance' => $customer->credit_balance,
        ];

        return view('admin.customers.show', compact('customer', 'stats'));
    }

    public function edit(Customer $customer): View
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        Gate::authorize('update', $customer);

        $customer->update($request->validated());

        return redirect()->route('admin.customers.show', $customer)
                         ->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        Gate::authorize('delete', $customer);

        if ($customer->activeBookings()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete customer with active bookings.']);
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Customer deleted.');
    }

    public function search(Request $request): JsonResponse
    {
        $term = $request->q ?? '';

        $customers = Customer::search($term)
            ->select('id', 'name', 'phone', 'organization', 'credit_balance')
            ->limit(10)
            ->get();

        return response()->json($customers);
    }

    public function adjustCredit(Request $request, Customer $customer): RedirectResponse
    {
        Gate::authorize('manage credits');

        $request->validate([
            'amount'         => 'required|numeric',
            'note'           => 'required|string|max:500',
            'payment_method' => 'nullable|string',
            'reference_no'   => 'nullable|string|max:100',
        ]);

        $this->creditService->adjustManually(
            $customer,
            (float) $request->amount,
            $request->note,
            auth()->user(),
            $request->payment_method,
            $request->reference_no
        );

        return back()->with('success', 'Credit balance updated.');
    }
}
