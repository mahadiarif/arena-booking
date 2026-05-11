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
            // Auto-create customer record if missing
            $customer = \App\Models\Customer::create([
                'user_id' => $user->id,
                'name'    => $user->name,
                'email'   => $user->email,
                'phone'   => '000-' . $user->id, // Unique temporary phone
                'address' => 'Not Provided',
            ]);
        }

        $bookings = $customer->bookings()->with(['venue', 'slot'])->latest()->paginate(10);
        $transactions = $customer->creditTransactions()->latest()->take(10)->get();

        return view('customer.dashboard', compact('customer', 'bookings', 'transactions'));
    }
}
