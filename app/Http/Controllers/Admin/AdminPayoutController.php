<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Models\PayoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminPayoutController extends Controller
{
    public function index(Request $request)
    {
        $query = Payout::with(['owner', 'booking']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $payouts = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total_payouts' => Payout::count(),
            'total_amount' => Payout::sum('owner_share'),
            'pending_count' => Payout::where('status', 'pending')->count(),
            'failed_count' => Payout::where('status', 'failed')->count(),
            'completed_count' => Payout::where('status', 'completed')->count(),
        ];

        return view('admin.payouts.index', compact('payouts', 'stats'));
    }

    public function show($id)
    {
        $payout = Payout::with(['owner', 'booking', 'history'])->findOrFail($id);
        return view('admin.payouts.show', compact('payout'));
    }

    public function approve(Request $request, $id)
    {
        $payout = Payout::findOrFail($id);

        if (!$payout->canBeProcessed()) {
            return response()->json([
                'error' => 'This payout cannot be approved'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $payout->markAsProcessing();

            // Dispatch payout verification job
            dispatch(new \App\Jobs\VerifyPayoutStatus($payout->id))
                ->delay(now()->addMinutes(2));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payout approved and processing',
                'data' => $payout
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin payout approval failed', [
                'payout_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to approve payout: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:10'
        ]);

        $payout = Payout::findOrFail($id);

        try {
            $payout->markAsFailed($request->reason);

            // Restore owner's balance
            $owner = $payout->owner;
            $owner->increment('available_balance', $payout->owner_share);
            $owner->decrement('pending_balance', $payout->owner_share);

            return response()->json([
                'success' => true,
                'message' => 'Payout rejected successfully',
                'data' => $payout
            ]);

        } catch (\Exception $e) {
            Log::error('Admin payout rejection failed', [
                'payout_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to reject payout: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $query = Payout::with(['owner', 'booking']);

        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $payouts = $query->orderBy('created_at', 'desc')->get();

        // Generate CSV export
        $filename = 'payouts_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($payouts) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'ID', 'Owner', 'Staff ID', 'Department',
                'Total Amount', 'Owner Share (80%)', 'Platform Share (20%)',
                'Status', 'Date'
            ]);

            // Data
            foreach ($payouts as $payout) {
                fputcsv($file, [
                    $payout->id,
                    $payout->owner->name ?? 'N/A',
                    $payout->staff_id ?? 'N/A',
                    $payout->department ?? 'N/A',
                    number_format($payout->total_amount, 2),
                    number_format($payout->owner_share, 2),
                    number_format($payout->platform_share, 2),
                    $payout->status,
                    $payout->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}