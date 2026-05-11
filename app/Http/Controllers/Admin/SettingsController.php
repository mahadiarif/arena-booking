<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $settings = Setting::all()->groupBy('group');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        if (! auth()->user()->hasRole('super_admin')) {
            abort(403, 'Only super admins can modify settings.');
        }

        foreach ($request->settings ?? [] as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }

        Cache::forget('app_settings');

        return back()->with('success', 'Settings saved successfully.');
    }
}
