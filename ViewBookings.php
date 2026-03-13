<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit();
}

$clientID = $_SESSION['UserID'];
$message = "";

if (isset($_GET['delete'])) {
    $bookingID = intval($_GET['delete']);

     $sqlEvent = "SELECT EventID FROM bookings WHERE BookingID = ? AND ClientID = ?";
    $stmtEvent = $conn->prepare($sqlEvent);
    $stmtEvent->bind_param("ii", $bookingID, $clientID);
    $stmtEvent->execute();
    $resultEvent = $stmtEvent->get_result();
    $rowEvent = $resultEvent->fetch_assoc();
$eventID = $rowEvent['EventID'];

    $sql = "DELETE FROM bookings WHERE BookingID = ? AND ClientID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $bookingID, $clientID);
    $stmt->execute();

     $sqlEventDel = "DELETE FROM events WHERE EventID = ?";
        $stmtEventDel = $conn->prepare($sqlEventDel);
        $stmtEventDel->bind_param("i", $eventID);
        $stmtEventDel->execute();

    if ($stmt->affected_rows > 0) {
        $message = "Booking deleted successfully!";
    } else {
        $message = "⚠ Unable to delete booking!";
    }
    header("Location: ViewBookings.php");
    exit();
}

$sql = "SELECT 
            b.BookingID,
            e.EventName,
            e.EventDate,
            e.Location,
            b.Status,
            b.BookingDate
        FROM bookings b
        INNER JOIN events e ON b.EventID = e.EventID
        WHERE b.ClientID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $clientID);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Bookings | Event Management</title>
<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #00b4db, #0083b0);
    margin: 0;
    padding: 0;
    color: #333;
}
.navbar {
    background: #fff;
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}
.navbar h2 { color: #0083b0; margin: 0; }
.navbar a {
    text-decoration: none;
    background: #0083b0;
    color: #fff;
    padding: 8px 15px;
    border-radius: 5px;
    margin-left: 10px;
}
.navbar a:hover { background: #00b4db; }

.container {
    width: 80%;
    margin: 30px auto;
    background: #fff;
    padding: 10px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
table th, table td {
    padding: 15px;
    border-bottom: 1px solid #ddd;
}
table th {
    background: #0083b0;
    color: #fff;
}
.delete-btn {
    background: red;
    color: white;
    padding: 6px 10px;
    border-radius: 5px;
    text-decoration: none;
}
.delete-btn:hover {
    background: darkred;
}

 .back-button {
    position: fixed;
    right: 5px; 
    top: 18%;
    transform: translateY(-50%);
    background-color: #0083b0;
    color: white;
    padding: 12px 18px;
    text-decoration: none;
    font-weight: bold;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    transition: background 0.3s, transform 0.2s;
    z-index: 1000;  
}

.back-button:hover {
    background-color: #00b4db;
    transform: translateY(-50%) scale(1.05);
}
</style>
</head>
<body>
    <a href="client_dashboard.php" class="back-button">⬅ Back</a>

<div class="navbar">
    <h2>Welcome, <?= $_SESSION['FullName'] ?> 👋</h2>
    <div>
        <a href="client_dashboard.php">🏠 Dashboard</a>
        <a href="logout.php">🚪 Logout</a>
    </div>
</div>

<div class="container">
    <h1>My Event Bookings</h1>

    <?php if ($message != ""): ?>
        <p style="padding:10px;background:#e6ffe6;border-left:4px solid green;">
            <?= $message ?>
        </p>
    <?php endif; ?>

    <table>
        <tr>
            <th>Event Name</th>
            <th>Date</th>
            <th>Venue</th>
            <th>Status</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['EventName']) ?></td>
                <td><?= htmlspecialchars($row['EventDate']) ?></td>
                <td><?= htmlspecialchars($row['Location']) ?></td>
                <td><?= htmlspecialchars($row['Status']) ?></td>
                <td>
                    <a class="delete-btn" 
                       href="ViewBookings.php?delete=<?= $row['BookingID'] ?>"
                       onclick="return confirm('Are you sure you want to delete this booking?');">
                        ❌ Delete
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>

    </table>

</div>

</body>
</html>
