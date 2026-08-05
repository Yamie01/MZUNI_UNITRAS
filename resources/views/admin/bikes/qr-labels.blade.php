<!DOCTYPE html>
<html>
<head>
    <title>Bike QR Labels</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px;
            background: #f5f5f5;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            color: #00693E;
        }
        .header p {
            color: #666;
        }
        .label-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .label {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            page-break-inside: avoid;
        }
        .label img {
            max-width: 150px;
            height: auto;
            border: 1px solid #eee;
            border-radius: 4px;
        }
        .label .bike-info {
            margin-top: 10px;
        }
        .label .bike-info .brand {
            font-weight: bold;
            font-size: 14px;
            color: #333;
        }
        .label .bike-info .detail {
            font-size: 12px;
            color: #666;
            margin: 2px 0;
        }
        .label .qr-id {
            font-size: 9px;
            color: #999;
            margin-top: 5px;
            word-break: break-all;
        }
        .label .status-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 10px;
            font-size: 10px;
            background: #28a745;
            color: white;
        }
        .print-btn {
            display: block;
            margin: 20px auto;
            padding: 12px 30px;
            background: #00693E;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }
        .print-btn:hover {
            background: #004d2e;
        }
        .watermark {
            position: fixed;
            bottom: 10px;
            right: 10px;
            opacity: 0.1;
            font-size: 12px;
            color: #00693E;
        }
        @media print {
            body { background: white; padding: 10px; }
            .label { box-shadow: none; border: 1px solid #ccc; }
            .print-btn { display: none; }
            .watermark { display: none; }
        }
        @media (max-width: 768px) {
            .label-container { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 480px) {
            .label-container { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="header">
    <h2>🚲 MZUNI UNITRAS - Bike QR Labels</h2>
    <p>Scan to activate bike | {{ now()->format('d M Y') }}</p>
</div>

<button class="print-btn" onclick="window.print()">
    <i class="fas fa-print"></i> 🖨️ Print Labels
</button>

<div class="label-container">
    @forelse($bikes as $bike)
        <div class="label">
            @if($bike->qr_code_path)
                <img src="{{ asset('storage/' . $bike->qr_code_path) }}" alt="QR Code">
            @else
                <div style="width:150px;height:150px;display:flex;align-items:center;justify-content:center;background:#f0f0f0;border-radius:4px;margin:0 auto;">
                    <span style="color:#999;font-size:12px;">No QR</span>
                </div>
            @endif
            <div class="bike-info">
                <div class="brand">{{ $bike->brand }} {{ $bike->model }}</div>
                <div class="detail">Reg: {{ $bike->registration_number }}</div>
                <div class="detail">Type: {{ ucfirst($bike->type) }}</div>
                <div class="detail">Rate: MWK {{ number_format($bike->rate_per_minute, 2) }}/min</div>
                <span class="status-badge">
                    <i class="fas fa-check-circle"></i> {{ $bike->isAvailable() ? 'Available' : 'Rented' }}
                </span>
            </div>
            <div class="qr-id">ID: {{ $bike->qr_code ?? 'Not generated' }}</div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <p>No bikes found.</p>
        </div>
    @endforelse
</div>

<div class="watermark">MZUNI UNITRAS</div>

<script>
    // Auto-print when page loads (optional - uncomment to enable)
    // window.onload = function() { setTimeout(window.print, 1000); }
</script>

</body>
</html>