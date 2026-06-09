<?php
// Simple dashboard to check bike status
$tx_ref = $_GET['tx_ref'] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Bike Rental Dashboard - MZUNI UNITRAS</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f0f0f0; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; }
        .success { color: green; background: #dff0df; padding: 10px; border-radius: 5px; }
        .error { color: red; background: #ffdfdf; padding: 10px; border-radius: 5px; }
        .bike { border: 1px solid #ddd; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .available { border-left: 4px solid green; }
        .pending { border-left: 4px solid orange; }
        .rented { border-left: 4px solid red; }
        button { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚲 MZUNI UNITRAS Bike Share System</h1>
        
        <?php if($tx_ref): ?>
            <div class="success">
                <strong>Payment Webhook Triggered!</strong><br>
                Transaction Reference: <?php echo htmlspecialchars($tx_ref); ?><br>
                <a href="webhook.php?tx_ref=<?php echo urlencode($tx_ref); ?>" target="_blank">View Webhook Response</a>
            </div>
        <?php endif; ?>
        
        <h2>Available Bikes</h2>
        <div id="bike-list">
            <!-- Bike list will be loaded here -->
            <div class="bike available">
                <strong>Bike #1</strong> - Mountain Bike<br>
                Status: <span style="color: green">Available</span><br>
                Rate: $5/hour<br>
                <button onclick="rentBike(1)">Rent Now</button>
            </div>
            <div class="bike pending">
                <strong>Bike #6</strong> - Electric Bike<br>
                Status: <span style="color: orange">Pending Payment</span><br>
                Rate: $10/hour<br>
                <button disabled>Processing...</button>
            </div>
        </div>
        
        <h2>Test Webhook</h2>
        <p>Click below to simulate a payment webhook for Bike #6:</p>
        <button onclick="testWebhook()">Simulate Payment for Bike #6</button>
        
        <h2>Debug Information</h2>
        <p>Webhook URL: <code>http://127.0.0.1/MZUNI_UNITRAS/bike-rental/webhook.php</code></p>
        <p><a href="webhook_debug.log" target="_blank">View Webhook Log</a></p>
    </div>
    
    <script>
        function rentBike(bikeId) {
            alert('Rental initiated for Bike ' + bikeId);
            // Here you would redirect to payment
            window.location.href = 'initiate_payment.php?bike_id=' + bikeId;
        }
        
        function testWebhook() {
            const tx_ref = 'BIKE-6-1778273734';
            fetch('webhook.php?tx_ref=' + tx_ref)
                .then(response => response.json())
                .then(data => {
                    alert('Webhook Response:\n' + JSON.stringify(data, null, 2));
                    location.reload();
                })
                .catch(error => {
                    alert('Error: ' + error);
                });
        }
    </script>
</body>
</html>
