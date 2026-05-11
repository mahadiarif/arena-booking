<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Services\SlotGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function __construct(
        protected SlotGeneratorService $slotGenerator,
    ) {}

    public function index(): View
    {
        $schedules = Schedule::withCount('venues')->get();

        return view('admin.schedules.index', compact('schedules'));
    }

    public function create(): View
    {
        return view('admin.schedules.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        
        // Handle pricing rules if provided
        if ($request->has('peak_start')) {
            $rules = [];
            foreach ($request->peak_start as $i => $start) {
                if ($start && $request->peak_end[$i]) {
                    $rules[] = [
                        'start_time'  => $start,
                        'end_time'    => $request->peak_end[$i],
                        'extra_price' => $request->peak_price[$i] ?? 0,
                    ];
                }
            }
            $validated['pricing_rules'] = $rules;
        }

        Schedule::create($validated);

        return redirect()->route('admin.schedules.index')
                         ->with('success', 'Schedule created successfully.');
    }

    public function edit(Schedule $schedule): View
    {
        return view('admin.schedules.edit', compact('schedule'));
    }

    public function update(Request $request, Schedule $schedule): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        // Handle pricing rules
        $rules = [];
        if ($request->has('peak_start')) {
            foreach ($request->peak_start as $i => $start) {
                if ($start && $request->peak_end[$i]) {
                    $rules[] = [
                        'start_time'  => $start,
                        'end_time'    => $request->peak_end[$i],
                        'extra_price' => $request->peak_price[$i] ?? 0,
                    ];
                }
            }
        }
        $validated['pricing_rules'] = $rules;

        $schedule->update($validated);

        // Refresh slots for all linked venues
        foreach ($schedule->venues as $venue) {
            $this->slotGenerator->refreshSlots($venue);
        }

        return redirect()->route('admin.schedules.index')
                         ->with('success', 'Schedule updated and slots refreshed.');
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        if ($schedule->venues()->count() > 0) {
            return back()->withErrors([
                'error' => 'Cannot delete a schedule that is assigned to venues. Reassign venues first.',
            ]);
        }

        $schedule->delete();

        return redirect()->route('admin.schedules.index')
                         ->with('success', 'Schedule deleted.');
    }

    // ── Shared Validation ──────────────────────────────────────────────────────

    private function rules(): array
    {
        return [
            'name'                 => ['required', 'string', 'max:100'],
            'timezone'             => ['nullable', 'string'],
            'start_time'           => ['required'],
            'end_time'             => ['required'],
            'slot_interval_minutes'=> ['required', 'integer'],
            'allowed_days'         => ['required', 'array', 'min:1'],
            'allowed_days.*'       => ['string'],
            'allow_concurrent'     => ['nullable'],
            'max_concurrent'       => ['nullable', 'integer'],
            'availability_start'   => ['nullable', 'date'],
            'availability_end'     => ['nullable', 'date'],
            'is_active'            => ['nullable'],
        ];
    }
}
