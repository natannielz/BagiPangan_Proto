<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(): View
    {
        $donors = User::where('role', 'donor')->orderBy('name')->get(['id', 'name']);

        return view('admin.reports', compact('donors'));
    }

    public function donationsCsv(Request $request): StreamedResponse
    {
        $request->validate([
            'from'              => 'nullable|date',
            'to'                => 'nullable|date|after_or_equal:from',
            'status'            => 'nullable|in:available,claimed,picked_up,completed,cancelled',
            'moderation_status' => 'nullable|in:pending,approved,rejected',
            'donor_id'          => 'nullable|integer|exists:users,id',
        ]);

        $query = Donation::with(['donor', 'category', 'claims.claimer'])
            ->when($request->from, fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to,   fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->when($request->status,            fn ($q) => $q->where('status', $request->status))
            ->when($request->moderation_status, fn ($q) => $q->where('moderation_status', $request->moderation_status))
            ->when($request->donor_id,          fn ($q) => $q->where('donor_id', $request->donor_id))
            ->orderBy('created_at', 'desc');

        $filename = 'donasi-'.now()->format('Ymd').'.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'X-Accel-Buffering'   => 'no',
        ];

        return response()->stream(function () use ($query) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM for Excel

            fputcsv($out, [
                'ID', 'Judul', 'Donor', 'Kategori', 'Porsi', 'Lokasi', 'Expiry',
                'Status', 'Moderasi', 'Diklaim Oleh', 'Diklaim Pada', 'Selesai Pada',
            ]);

            $query->chunk(500, function ($donations) use ($out) {
                foreach ($donations as $d) {
                    $claim = $d->claims->first();
                    fputcsv($out, [
                        $d->id,
                        $d->title,
                        $d->donor?->name,
                        $d->category?->name,
                        $d->qty_portions,
                        $d->location_district,
                        optional($d->expiry_at)->format('Y-m-d H:i'),
                        $d->status,
                        $d->moderation_status,
                        $claim?->claimer?->name ?? '-',
                        optional($claim?->claimed_at)->format('Y-m-d H:i') ?? '-',
                        optional($claim?->verified_at)->format('Y-m-d H:i') ?? '-',
                    ]);
                }
            });

            fclose($out);
        }, 200, $headers);
    }
}
