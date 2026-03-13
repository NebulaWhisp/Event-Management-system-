<?php
require 'db_connect.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h3>🧪 Booking Debug Mode</h3>";

if (!$conn) {
    die("<p style='color:red;'>❌ No DB connection</p>");
} else {
    echo "<p style='color:green;'>✅ DB connection active</p>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<h4>Form Data:</h4><pre>";
    print_r($_POST);
    echo "</pre>";

    $clientID  = 1;
    $managerID = null;
    $status    = "Pending";
    $bookingDate = date("Y-m-d");

    $eventType = $_POST['eventType'] ?? "Birthday Event";
    $eventMap = [
        "Birthday Event" => 1,
        "Wedding" => 2,
        "Corporate Event" => 3
    ];
    $eventID = $eventMap[$eventType] ?? 1;

    echo "<p>📅 Using EventID: <strong>$eventID</strong>, ClientID: <strong>$clientID</strong></p>";

    try {
        $sql = "INSERT INTO Bookings ([EventID], [ClientID], [ManagerID], [Status], [BookingDate])
                VALUES (?, ?, ?, ?, ?)";

        echo "<h4>SQL Prepared Query:</h4><code>$sql</code><br>";

        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([$eventID, $clientID, $managerID, $status, $bookingDate]);

        if ($result) {
            echo "<p style='color:green;'>✅ Insert successful! Check Access table 'Bookings'.</p>";
        } else {
            echo "<p style='color:red;'>❌ Insert failed (no exception thrown).</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color:red;'>❌ Error inserting booking: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p>⚠️ No POST data received.</p>";
}
?>
