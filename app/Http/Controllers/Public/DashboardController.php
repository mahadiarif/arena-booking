<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $customer = $user->customer;
        
        if (!$customer) {
            // Try to find the customer by user_id or email (including trashed ones)
            $customer = \App\Models\Customer::withTrashed()
                ->where('user_id', $user->id)
                ->orWhere('email', $user->email)
                ->first();

            if ($customer) {
                // If it was deleted, restore it
                if ($customer->trashed()) {
                    $customer->restore();
                }
                
                // Ensure user_id is linked if it wasn't
                if (!$customer->user_id) {
                    $customer->update(['user_id' => $user->id]);
                }
            } else {
                // Create new customer only if absolutely not found
                // We use a unique temporary phone to avoid collisions
                $customer = \App\Models\Customer::create([
                    'user_id' => $user->id,
                    'name'    => $user->name,
                    'email'   => $user->email,
                    'phone'   => 'TEMP-' . $user->id . '-' . time(), 
                    'address' => 'Not Provided',
                ]);
            }
        }

        $bookings = $customer->bookings()->with(['venue', 'slot'])->latest()->paginate(10);
        $transactions = $customer->creditTransactions()->latest()->take(10)->get();

        return view('customer.dashboard', compact('customer', 'bookings', 'transactions'));
    }
}
