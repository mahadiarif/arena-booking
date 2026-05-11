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
            // Auto-create or fetch customer record
            $customer = \App\Models\Customer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name'    => $user->name,
                    'email'   => $user->email,
                    'phone'   => '000-' . $user->id, 
                    'address' => 'Not Provided',
                ]
            );
        }

        $bookings = $customer->bookings()->with(['venue', 'slot'])->latest()->paginate(10);
        $transactions = $customer->creditTransactions()->latest()->take(10)->get();

        return view('customer.dashboard', compact('customer', 'bookings', 'transactions'));
    }
}
