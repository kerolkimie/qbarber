<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Senarai log aktiviti sistem — emel, subscription, topup point, komisen.
     * Route: GET /admin/logs
     */
    public function index(Request $request)
    {
        $type = $request->input('type');

        $logs = ActivityLog::with('causer')
            ->when($type, fn ($q) => $q->where('type', $type))
            ->latest()
            ->paginate(40)
            ->withQueryString();

        $types = ActivityLog::TYPE_LABELS;

        return view('admin.logs.index', compact('logs', 'types', 'type'));
    }
}
