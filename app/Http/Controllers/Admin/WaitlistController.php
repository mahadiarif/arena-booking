<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Slot;
use App\Models\WaitlistEntry;
use App\Services\WaitlistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WaitlistController extends Controller
{
    public function __construct(
        protected WaitlistService $waitlistService,
    ) {}

    public function index(Request $request): View
    {
        $entries = WaitlistEntry::with(['slot.venue', 'customer'])
            ->when($request->filled('slot_id'), fn ($q) => $q->where('slot_id', $request->slot_id))
            ->orderBy('slot_id')
            ->orderBy('position')
            ->paginate(20)
            ->withQueryString();

        return view('admin.waitlist.index', compact('entries'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'slot_id'     => ['required', 'exists:slots,id'],
            'customer_id' => ['required', 'exists:customers,id'],
        ]);

        $this->waitlistService->addToWaitlist(
            Slot::findOrFail($request->slot_id),
            Customer::findOrFail($request->customer_id)
        );

        return back()->with('success', 'Customer added to waitlist.');
    }

    public function destroy(WaitlistEntry $waitlistEntry): RedirectResponse
    {
        $this->waitlistService->removeFromWaitlist($waitlistEntry);

        return back()->with('success', 'Removed from waitlist.');
    }
}
