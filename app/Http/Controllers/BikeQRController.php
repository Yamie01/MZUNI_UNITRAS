<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use App\Models\BikeRental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BikeQRController extends Controller
{
    /**
     * ============================================================
     * PUBLIC QR CODE ROUTES
     * ============================================================
     */

    /**
     * Show bike activation page from QR code scan
     */
    public function activate(Request $request)
    {
        $qrCode = $request->query('qr') ?? $request->input('qr');

        if (!$qrCode) {
            return redirect()->route('user.bikes.index')
                ->with('error', 'Invalid QR code.');
        }

        $bike = Bike::where('qr_code', $qrCode)->first();

        if (!$bike) {
            return redirect()->route('user.bikes.index')
                ->with('error', 'Bike not found. Please check the QR code.');
        }

        if (!$bike->isAvailable()) {
            return redirect()->route('user.bikes.index')
                ->with('error', 'This bike is currently not available for rent.');
        }

        // If user is not logged in, redirect to login with return URL
        if (!Auth::check()) {
            $returnUrl = route('bike.activate', ['qr' => $qrCode]);
            return redirect()->route('login', ['redirect_to' => $returnUrl])
                ->with('info', 'Please login to rent this bike.');
        }

        // Check if user already has an active rental
        $activeRental = BikeRental::where('user_id', Auth::id())
            ->where('status', 'active')
            ->first();

        if ($activeRental) {
            return redirect()->route('user.bike-rentals.show', $activeRental)
                ->with('error', 'You already have an active bike rental. Please return it first.');
        }

        return redirect()->route('user.bikes.rent', $bike)
            ->with('success', '✅ Scan successful! Activate this bike.');
    }

    /**
     * Get QR code for a specific bike (AJAX)
     */
    public function getQRCode(Bike $bike)
    {
        try {
            // Generate QR code if not exists
            if (!$bike->qr_code || !$bike->qr_code_path) {
                $bike->generateQRCode();
                $bike->refresh();
            }

            return response()->json([
                'success' => true,
                'bike_id' => $bike->id,
                'bike_name' => $bike->brand . ' ' . $bike->model,
                'qr_code_url' => $bike->qr_code_url,
                'activation_url' => route('bike.activate', ['qr' => $bike->qr_code]),
                'is_available' => $bike->isAvailable(),
                'status' => $bike->status
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get QR code', [
                'bike_id' => $bike->id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load QR code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activate bike from QR code scan (direct activation)
     */
    public function activateFromQR($qrCode)
    {
        try {
            $bike = Bike::where('qr_code', $qrCode)->first();

            if (!$bike) {
                return redirect()->route('user.bikes.index')
                    ->with('error', 'Invalid QR code. Bike not found.');
            }

            if (!$bike->isAvailable()) {
                return redirect()->route('user.bikes.index')
                    ->with('error', 'This bike is currently not available for rent.');
            }

            if (!Auth::check()) {
                $returnUrl = route('bike.activate', ['qr' => $qrCode]);
                return redirect()->route('login', ['redirect_to' => $returnUrl])
                    ->with('info', 'Please login to activate this bike.');
            }

            $activeRental = BikeRental::where('user_id', Auth::id())
                ->where('status', 'active')
                ->first();

            if ($activeRental) {
                return redirect()->route('user.bike-rentals.show', $activeRental)
                    ->with('error', 'You already have an active bike rental. Please return it first.');
            }

            return redirect()->route('user.bikes.rent', $bike)
                ->with('success', '✅ QR Code scanned successfully! Activate this bike now.');

        } catch (\Exception $e) {
            Log::error('QR activation failed', [
                'qr_code' => $qrCode,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('user.bikes.index')
                ->with('error', 'Failed to activate bike: ' . $e->getMessage());
        }
    }

    /**
     * ============================================================
     * ADMIN QR CODE MANAGEMENT
     * ============================================================
     */

    /**
     * Generate QR codes for all bikes (Admin)
     */
    public function generateAllQRs()
    {
        $bikes = Bike::all();
        $generated = 0;
        $failed = 0;

        foreach ($bikes as $bike) {
            try {
                // Skip if QR already exists and file exists
                if ($bike->qr_code && $bike->qr_code_path) {
                    $filePath = storage_path('app/public/' . $bike->qr_code_path);
                    if (file_exists($filePath)) {
                        continue;
                    }
                }
                
                $bike->generateQRCode();
                $generated++;
            } catch (\Exception $e) {
                $failed++;
                Log::error('Failed to generate QR for bike ' . $bike->id, [
                    'error' => $e->getMessage()
                ]);
            }
        }

        $message = "✅ Generated QR codes for {$generated} bikes.";
        if ($failed > 0) {
            $message .= " Failed: {$failed} bikes.";
        }

        return redirect()->route('admin.bikes.index')
            ->with('success', $message);
    }

    /**
     * Regenerate QR code for a specific bike (Admin)
     */
    public function regenerateQR(Bike $bike)
    {
        try {
            // Delete old QR file if exists
            if ($bike->qr_code_path) {
                $oldPath = storage_path('app/public/' . $bike->qr_code_path);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $bike->qr_code = null;
            $bike->qr_code_path = null;
            $bike->save();

            $bike->generateQRCode();

            return redirect()->route('admin.bikes.show', $bike)
                ->with('success', '✅ QR code regenerated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to regenerate QR for bike ' . $bike->id, [
                'error' => $e->getMessage()
            ]);

            return redirect()->route('admin.bikes.show', $bike)
                ->with('error', 'Failed to regenerate QR code: ' . $e->getMessage());
        }
    }

    /**
     * Download QR code for a bike
     */
    public function downloadQR(Bike $bike)
    {
        try {
            if (!$bike->qr_code_path) {
                $bike->generateQRCode();
            }

            $filePath = storage_path('app/public/' . $bike->qr_code_path);

            if (!file_exists($filePath)) {
                return back()->with('error', 'QR code file not found. Please regenerate.');
            }

            return response()->download($filePath, 'bike-qr-' . $bike->id . '.png');

        } catch (\Exception $e) {
            Log::error('Failed to download QR for bike ' . $bike->id, [
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to download QR code: ' . $e->getMessage());
        }
    }

    /**
     * Preview QR code
     */
    public function previewQR(Bike $bike)
    {
        try {
            if (!$bike->qr_code_path) {
                $bike->generateQRCode();
            }

            return view('admin.bikes.qr-preview', compact('bike'));

        } catch (\Exception $e) {
            Log::error('Failed to preview QR for bike ' . $bike->id, [
                'error' => $e->getMessage()
            ]);

            return redirect()->route('admin.bikes.show', $bike)
                ->with('error', 'Failed to preview QR code: ' . $e->getMessage());
        }
    }

    /**
     * Print QR code labels for all bikes
     */
    public function printLabels()
    {
        $bikes = Bike::where('is_active', true)
            ->orderBy('brand')
            ->orderBy('model')
            ->get();

        return view('admin.bikes.qr-labels', compact('bikes'));
    }
}