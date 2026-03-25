<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $from = $request->date('from', 'Y-m-d') ?? now()->subDays(29)->startOfDay();
        $to   = $request->date('to', 'Y-m-d')   ?? now()->endOfDay();

        $totals = Donation::query()
            ->whereBetween('created_at', [$from, $to])
            ->toBase()
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status='available'  THEN 1 ELSE 0 END) as available,
                SUM(CASE WHEN status='claimed'    THEN 1 ELSE 0 END) as claimed,
                SUM(CASE WHEN status='completed'  THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status='cancelled'  THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN status='completed'  THEN qty_portions ELSE 0 END) as portions_saved
            ")
            ->first();

        $completionRate = $totals->total > 0
            ? round($totals->completed / $totals->total * 100, 1)
            : 0;

        $avgClaimHours = Claim::query()
            ->whereBetween('claimed_at', [$from, $to])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, d.created_at, claims.claimed_at)) as avg_hours')
            ->join('donations as d', 'd.id', '=', 'claims.donation_id')
            ->value('avg_hours');

        $daily = Donation::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $byStatus = Donation::query()
            ->where('moderation_status', 'approved')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get()
            ->map(fn ($row) => ['status' => $row->status, 'total' => (int) $row->total])
            ->values();

        return response()->json([
            'data' => [
                'totals'          => $totals,
                'completion_rate' => $completionRate,
                'portions_saved'  => (int) ($totals->portions_saved ?? 0),
                'avg_claim_hours' => round($avgClaimHours ?? 0, 1),
                'daily_counts'    => $daily,
                'donations_per_day' => [
                    'labels' => $daily->pluck('date'),
                    'counts' => $daily->pluck('count'),
                ],
                'status_breakdown' => $byStatus,
            ],
            'meta'    => [
                'from' => $from->toDateString(),
                'to'   => $to->toDateString(),
            ],
            'message' => 'OK',
        ]);
    }

    public function topDonors(Request $request): JsonResponse
    {
        $rows = Donation::query()
            ->where('moderation_status', 'approved')
            ->select('donor_id', DB::raw('count(*) as total'))
            ->groupBy('donor_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $users = User::query()
            ->whereIn('id', $rows->pluck('donor_id'))
            ->get()
            ->keyBy('id');

        $data = $rows->map(function ($row) use ($users) {
            $user = $users->get($row->donor_id);

            return [
                'donor_id' => $row->donor_id,
                'name'     => $user?->name,
                'total'    => (int) $row->total,
            ];
        })->values();

        return response()->json([
            'data'    => $data,
            'message' => 'OK',
        ]);
    }
}
