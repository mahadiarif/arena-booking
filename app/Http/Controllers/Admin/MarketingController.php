<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Customer;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MarketingController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::with('creator')->latest()->paginate(10);
        return view('admin.marketing.index', compact('campaigns'));
    }

    public function create()
    {
        $customerCount = Customer::count();
        return view('admin.marketing.create', compact('customerCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:sms,whatsapp,email',
            'target_audience' => 'required|in:all,selected',
            'message' => 'required|string',
        ]);

        $campaign = Campaign::create([
            'name' => $request->name,
            'type' => $request->type,
            'target_audience' => $request->target_audience,
            'message' => $request->message,
            'status' => 'completed', // Mocking completion for now
            'created_by' => auth()->id(),
            'sent_count' => Customer::count(), // Mocking sent count
        ]);

        // Logic to actually send would go here
        Log::info("Marketing Campaign '{$campaign->name}' of type '{$campaign->type}' sent.");

        return redirect()->route('admin.marketing.index')->with('success', 'Marketing campaign sent successfully.');
    }

    public function settings()
    {
        $settings = [
            'sms_gateway' => Setting::get('marketing_sms_gateway', 'twilio'),
            'twilio_sid' => Setting::get('marketing_twilio_sid', ''),
            'twilio_token' => Setting::get('marketing_twilio_token', ''),
            'twilio_from' => Setting::get('marketing_twilio_from', ''),
            
            'whatsapp_provider' => Setting::get('marketing_whatsapp_provider', 'meta'),
            'whatsapp_token' => Setting::get('marketing_whatsapp_token', ''),
            
            'mail_host' => Setting::get('marketing_mail_host', config('mail.mailers.smtp.host')),
            'mail_port' => Setting::get('marketing_mail_port', config('mail.mailers.smtp.port')),
            'mail_username' => Setting::get('marketing_mail_username', config('mail.mailers.smtp.username')),
            'mail_password' => Setting::get('marketing_mail_password', config('mail.mailers.smtp.password')),
        ];

        return view('admin.marketing.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->except('_token');

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => 'marketing_' . $key],
                [
                    'value' => $value,
                    'group' => 'marketing',
                    'type' => 'string',
                    'label' => ucwords(str_replace('_', ' ', $key))
                ]
            );
        }

        return back()->with('success', 'Marketing configurations updated.');
    }
}
