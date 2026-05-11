<?php

namespace App\Http\Controllers;

use App\Services\CalendarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitorDisplayController extends Controller
{
    public function __construct(
        protected CalendarService $calendarService,
    ) {}

    public function index(): View
    {
        return view('monitor.display');
    }

    public function data(Request $request): JsonResponse
    {
        if ($request->get('token') !== config('arenabook.monitor_token')) {
            abort(403, 'Invalid monitor token.');
        }

        $data = $this->calendarService->getMonitorData();

        return response()->json($data);
    }
}
