<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(): View
    {
        $logs = AuditLog::query()
            ->latest()
            ->paginate(20);

        return view('admin.audit-log', [
            'logs' => $logs,
        ]);
    }
}
