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
            // Check if a trashed customer exists with this user_id
            $customer = \App\Models\Customer::withTrashed()->where('user_id', $user->id)->first();
            
            if ($customer) {
                if ($customer->trashed()) {
                    $customer->restore();
                }
            } else {
                // Auto-create or fetch customer record
                $customer = \App\Models\Customer::updateOrCreate(
                    ['email' => $user->email], // Fallback to email if user_id link was broken
                    [
                        'user_id' => $user->id,
                        'name'    => $user->name,
                        'phone'   => '000-' . $user->id, 
                        'address' => 'Not Provided',
                    ]
                );
            }
        }

        $bookings = $customer->bookings()->with(['venue', 'slot'])->latest()->paginate(10);
        $transactions = $customer->creditTransactions()->latest()->take(10)->get();

        return view('customer.dashboard', compact('customer', 'bookings', 'transactions'));
    }
}
