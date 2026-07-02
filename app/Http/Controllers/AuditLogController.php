<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display a listing of the audit logs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = ActivityLog::with('user');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                  ->orWhere('action', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($uQuery) use ($search) {
                      $uQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Display newest logs first
        $logs = $query->orderBy('created_at', 'desc')
                      ->orderBy('id', 'desc')
                      ->paginate(15)
                      ->withQueryString();

        return view('audit.index', compact('logs', 'search'));
    }
}
